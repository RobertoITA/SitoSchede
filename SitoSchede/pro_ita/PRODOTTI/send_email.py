#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
File: send_email.py
Invia una singola email con un allegato, logo inline e corpo HTML.
Progettato per essere chiamato da uno script PHP, con gestione robusta
degli argomenti e dei codici di uscita.
"""
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

# Prova a importare Pillow, altrimenti esci con un errore chiaro.
try:
    from PIL import Image
except ImportError:
    print("FATAL ERROR: La libreria 'Pillow' (PIL) non e' installata. Esegui: pip install Pillow", file=sys.stderr)
    sys.exit(1)

# --- CONFIGURAZIONE SMTP (SOSTITUISCI CON I TUOI DATI REALI) ---
SMTP_SERVER = '192.168.0.228'
SMTP_PORT = 587
SMTP_USERNAME = 'roberto.bartolini'
SMTP_PASSWORD = 'Zaq1Mko0#iM'
SENDER_EMAIL = 'roberto@italmont.it'
SENDER_NAME = 'ITALMONT SRL - Schede Tecniche e di Sicurezza'
LOGO_FILENAME = 'logo.jpg' 
LOGO_SIZE = (320, 240)
BCC_EMAIL = SENDER_EMAIL # Invia una copia a te stesso

def main():
    # --- 1. CONTROLLO E RECUPERO DEGLI ARGOMENTI ---
    if len(sys.argv) != 4:
        print(f"Error: Numero di argomenti non corretto. Attesi 3 (destinatario, percorso_file, nome_file), ricevuti {len(sys.argv) - 1}.", file=sys.stderr)
        sys.exit(1)

    recipient_email = sys.argv[1]
    file_path = sys.argv[2]
    file_name = sys.argv[3]

    if not os.path.exists(file_path):
        print(f"Error: File non trovato al percorso: {file_path}", file=sys.stderr)
        sys.exit(1)

    # --- 2. ESTRAZIONE DATI E PREPARAZIONE CONTENUTO ---
    try:
        dir_path = os.path.dirname(file_path)
        scheda_number = os.path.basename(dir_path)
        logo_path = os.path.join(dir_path, LOGO_FILENAME)

        product_name_prefix = os.path.splitext(file_name)[0]
        if product_name_prefix.upper().startswith('ST - '):
            clean_product_name = product_name_prefix[5:]
        elif product_name_prefix.upper().startswith('SDS - '):
            clean_product_name = product_name_prefix[6:]
        else:
            clean_product_name = product_name_prefix

        # --- 3. COSTRUZIONE DEL MESSAGGIO EMAIL ---
        msg = MIMEMultipart('mixed')
        msg['From'] = formataddr((str(Header(SENDER_NAME, 'utf-8')), SENDER_EMAIL))
        msg['To'] = recipient_email
        msg['Subject'] = f'Invio del file: {file_name}'
        if BCC_EMAIL:
            msg['Bcc'] = BCC_EMAIL

        msg_related = MIMEMultipart('related')
        
        html_body = f"""
        <html>
        <body style="font-family: Arial, sans-serif; font-size: 10pt;">
            <p>Buongiorno,</p>
            <p>in allegato a questo messaggio e-mail, troverà il file:<br>
            <strong>{file_name}</strong></p>
            <p>relativo alla scheda:<br>
            <strong>{scheda_number} - {clean_product_name}</strong></p>
            <p>Cordiali Saluti.</p>
            <img src="cid:logo_italmont" alt="Logo Italmont" style="display: block; margin: 10px 0;"><br>
            <p>Italmont srl<br>Via IV Novembre, 13<br>63078 Pagliare del Tronto (AP)</p>
            <p>Roberto Bartolini<br>Cell: 3288781654<br>Tel: 0736899238 &nbsp; Fax: 0736899489<br>
            <a href="mailto:roberto@italmont.it">roberto@italmont.it</a></p>
        </body>
        </html>
        """
        msg_related.attach(MIMEText(html_body, 'html', 'utf-8'))

        if os.path.exists(logo_path):
            with Image.open(logo_path) as img:
                img.thumbnail(LOGO_SIZE)
                buffer = io.BytesIO()
                img.save(buffer, format='JPEG')
                logo_data = buffer.getvalue()
            
            logo = MIMEImage(logo_data)
            logo.add_header('Content-ID', '<logo_italmont>')
            msg_related.attach(logo)
        
        msg.attach(msg_related)

        with open(file_path, "rb") as attachment:
            part = MIMEBase("application", "octet-stream")
            part.set_payload(attachment.read())
        encoders.encode_base64(part)
        part.add_header("Content-Disposition", f"attachment; filename=\"{file_name}\"")
        msg.attach(part)

        # --- 4. CONNESSIONE AL SERVER SMTP E INVIO ---
        all_recipients = [recipient_email]
        if BCC_EMAIL:
            all_recipients.append(BCC_EMAIL)
        
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()
            server.login(SMTP_USERNAME, SMTP_PASSWORD)
            server.sendmail(SENDER_EMAIL, all_recipients, msg.as_string())
        
        # Se tutto è andato bene, esci con codice 0 (successo).
        sys.exit(0)

    except Exception as e:
        print(f"Error: Si e' verificato un errore durante la preparazione o l'invio dell'email: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()