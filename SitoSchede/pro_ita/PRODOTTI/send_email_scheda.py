#!/usr/bin/env python3
# File: send_email_scheda.py

import sys
import os
import smtplib
import io
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
from email.mime.image import MIMEImage
from email import encoders
from email.header import Header
from email.utils import formataddr

# Importa la libreria di gestione immagini Pillow
try:
    from PIL import Image
except ImportError:
    # Uscita in caso di errore di importazione, l'output verrà catturato da PHP
    print("Error: La libreria 'Pillow' (PIL) non è installata. Esegui: pip install Pillow", file=sys.stderr)
    sys.exit(1)


# Configurazione SMTP (SOSTITUISCI CON I TUOI DATI REALI)
SMTP_SERVER = '192.168.0.228'
SMTP_PORT = 587                 # 587 (TLS/STARTTLS) o 465 (SSL)
SMTP_USERNAME = 'roberto.bartolini'
SMTP_PASSWORD = 'Zaq1Mko0#iM'
SENDER_EMAIL = 'roberto@italmont.it' 
SENDER_NAME = 'Roberto Bartolini - Italmont srl'
LOGO_FILENAME = 'logo.jpg' 
LOGO_SIZE = (320, 240) 
BCC_EMAIL = SENDER_EMAIL 


# Controllo Argomenti (adesso sono 3: Destinatari, Allegati, Nome_File_Principale)
if len(sys.argv) != 4:
    print("Error: Usage: python send_email_scheda.py <destinatari,> <percorsi_allegati,> <nome_file_principale>", file=sys.stderr)
    sys.exit(1)

# Recupero Argomenti
RECIPIENTS_INPUT = sys.argv[1] 
ATTACHMENTS_INPUT = sys.argv[2]
MAIN_FILE_NAME = sys.argv[3] 

# --- PARSING E VALIDAZIONE ---
RECIPIENT_LIST = [r.strip() for r in RECIPIENTS_INPUT.split(',') if r.strip()]
ATTACHMENT_PATHS = [a.strip() for a in ATTACHMENTS_INPUT.split(',') if a.strip()]

if not RECIPIENT_LIST:
    print("Error: Nessun destinatario valido specificato.", file=sys.stderr)
    sys.exit(1)

if not ATTACHMENT_PATHS:
    print("Error: Nessun allegato specificato.", file=sys.stderr)
    sys.exit(1)

# Il percorso del file principale è il primo della lista degli allegati
MAIN_FILE_PATH = ATTACHMENT_PATHS[0]
if not os.path.exists(MAIN_FILE_PATH):
    print(f"Error: File principale non trovato al percorso: {MAIN_FILE_PATH}", file=sys.stderr)
    sys.exit(1)


# --- LOGICA DI ESTRAZIONE DATI PER IL CORPO EMAIL ---
DIR_PATH = os.path.dirname(MAIN_FILE_PATH)
SCHEDA_NUMBER = os.path.basename(DIR_PATH)
LOGO_PATH = os.path.join(DIR_PATH, LOGO_FILENAME) 

PRODUCT_NAME_WITH_PREFIX = os.path.splitext(MAIN_FILE_NAME)[0]

if PRODUCT_NAME_WITH_PREFIX.upper().startswith('ST - '):
    CLEAN_PRODUCT_NAME = PRODUCT_NAME_WITH_PREFIX[5:]
elif PRODUCT_NAME_WITH_PREFIX.upper().startswith('SDS - '):
    CLEAN_PRODUCT_NAME = PRODUCT_NAME_WITH_PREFIX[6:]
else:
    CLEAN_PRODUCT_NAME = PRODUCT_NAME_WITH_PREFIX
    
# --- FINE LOGICA DI ESTRAZIONE ---

def send_attached_email():
    try:
        # Crea il corpo del messaggio
        msg = MIMEMultipart('mixed')
        
        msg['From'] = formataddr((str(Header(SENDER_NAME, 'utf-8')), SENDER_EMAIL))
        # Metti tutti i destinatari nel campo To (visibile)
        msg['To'] = ", ".join(RECIPIENT_LIST) 
        msg['Subject'] = f'Invio file: {MAIN_FILE_NAME} e allegati'
        msg['Bcc'] = BCC_EMAIL 

        # Usa un contenitore "related" per il corpo HTML e le immagini inline
        msg_related = MIMEMultipart('related')
        
        # --- CREAZIONE CORPO HTML ---
        html_body = f"""
        <html>
        <body style="font-family: Arial, sans-serif; font-size: 10pt;">
            <p>Buongiorno,</p>
            <p>in allegato a questo messaggio e-mail, troverà i file richiesti, tra cui:</p>
            <p><strong>{MAIN_FILE_NAME}</strong></p>
            <p>relativo alla scheda:<br>
            <strong>{SCHEDA_NUMBER} - {CLEAN_PRODUCT_NAME}</strong></p>
            <p>Cordiali Saluti.</p>
            
            <img src="cid:logo_italmont" alt="Logo Italmont" style="display: block; margin: 10px 0;"><br>

            <p>Italmont srl<br>
            Via IV Novembre, 13<br>
            63078 Pagliare del Tronto (AP)</p>

            <p>Roberto Bartolini<br>
            Cell: 3288781654<br>
            Tel: 0736899238 &nbsp; Fax: 0736899489<br>
            <a href="mailto:roberto@italmont.it">roberto@italmont.it</a></p>

            <p style="font-size: 8pt; color: #666;">
            Le informazioni contenute nella presente comunicazione e i relativi allegati possono essere riservate e sono, comunque, destinate esclusivamente alle persone o alla Società sopraindicati.<br>
            La diffusione, distribuzione e/o copiatura del documento trasmesso da parte di qualsiasi soggetto diverso dal destinatario è proibita, sia ai sensi dell’art. 616 c.p.<br>
            Se avete ricevuto questo messaggio per errore, vi preghiamo di distruggerlo e di informarci immediatamente per telefono allo +39 0736/899238 o inviando un messaggio all’indirizzo <a href="mailto:info@italmont.it">info@italmont.it</a>.<br>
            Si ricorda che i dati personali presenti all’interno di questa comunicazione verranno trattati secondo le disposizioni del regolamento UE 2016/679.
            </p>
        </body>
        </html>
        """
        msg_related.attach(MIMEText(html_body, 'html'))
        
        # --- ALLEGATO LOGO INLINE (CID) CON RIDIMENSIONAMENTO ---
        if os.path.exists(LOGO_PATH):
            img = Image.open(LOGO_PATH)
            # Utilizza thumbnail che rimpicciolisce senza forzare lo stretching
            img.thumbnail(LOGO_SIZE) 
            
            buffer = io.BytesIO()
            img.save(buffer, format='jpeg') 
            
            logo_data = buffer.getvalue()
            logo = MIMEImage(logo_data)
            
            logo.add_header('Content-ID', '<logo_italmont>') 
            logo.add_header('Content-Disposition', 'inline; filename="logo.jpg"')
            msg_related.attach(logo)
        
        msg.attach(msg_related)


        # --- ALLEGATI PDF/ALTRI FILE (Tutti i file) ---
        for file_path in ATTACHMENT_PATHS:
            if os.path.exists(file_path):
                file_name = os.path.basename(file_path)
                with open(file_path, "rb") as attachment:
                    part = MIMEBase("application", "octet-stream")
                    part.set_payload(attachment.read())

                encoders.encode_base64(part)

                part.add_header(
                    "Content-Disposition",
                    f"attachment; filename=\"{file_name}\"", 
                )
                msg.attach(part)

        # --- GESTIONE INVIO E DESTINATARI ---
        
        # La lista dei destinatari reali include TO e BCC (il tuo indirizzo)
        recipient_list_for_server = RECIPIENT_LIST + [BCC_EMAIL]
        recipient_list_for_server = list(set([r for r in recipient_list_for_server if r])) 
        
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls() 
            server.login(SMTP_USERNAME, SMTP_PASSWORD)
            text = msg.as_string()
            
            # Invia dal tuo indirizzo a tutti i destinatari (inclusa la copia BCC)
            server.sendmail(SENDER_EMAIL, recipient_list_for_server, text)
            
        sys.exit(0)

    except Exception as e:
        # Stampa l'errore sullo standard error in modo che PHP possa catturarlo
        print(f"Error: Errore durante l'invio via SMTP: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    send_attached_email()