#!/usr/bin/env python3
# File: send_email.py

import sys
import os
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
from email import encoders

# Configurazione SMTP (SOSTITUISCI CON I TUOI DATI REALI)
SMTP_SERVER = 'smtp.gmail.com'  # Es. smtp.gmail.com, mail.tuodominio.it
SMTP_PORT = 587                 # 587 (TLS/STARTTLS) o 465 (SSL)
SMTP_USERNAME = 'roberto.italmont@gmail.com'
SMTP_PASSWORD = 'ital12345mont'
SENDER_EMAIL = 'roberto.italmont@gmail.com'
SENDER_NAME = 'Sistema Schede Tecniche e di Sicurezza'

# Controllo Argomenti
if len(sys.argv) != 4:
    # Stampa un messaggio di errore sullo standard error che verrà catturato da PHP
    print("Error: Usage: python send_email.py <destinatario> <percorso_file> <nome_file>", file=sys.stderr)
    sys.exit(1)

# Recupero Argomenti
RECIPIENT_EMAIL = sys.argv[1]
FILE_PATH = sys.argv[2]
FILE_NAME = sys.argv[3]

# Controllo Esistenza File
if not os.path.exists(FILE_PATH):
    print(f"Error: File non trovato al percorso: {FILE_PATH}", file=sys.stderr)
    sys.exit(1)

def send_attached_email():
    try:
        # Crea il corpo del messaggio
        msg = MIMEMultipart()
        msg['From'] = f'{SENDER_NAME} <{SENDER_EMAIL}>'
        msg['To'] = RECIPIENT_EMAIL
        msg['Subject'] = f'Invio del file: {FILE_NAME}'

        # Aggiungi il corpo del testo
        body = f"Buongiorno,\n\nIn allegato troverai il file **{FILE_NAME}**.\n\nCordiali Saluti."
        msg.attach(MIMEText(body, 'plain'))
        
        # Allegato: apertura e preparazione
        with open(FILE_PATH, "rb") as attachment:
            # Crea un oggetto MIMEBase
            part = MIMEBase("application", "octet-stream")
            part.set_payload(attachment.read())

        # Codifica l'allegato in base64
        encoders.encode_base64(part)

        # Aggiungi l'header Content-Disposition con il nome del file
        part.add_header(
            "Content-Disposition",
            f"attachment; filename= {FILE_NAME}",
        )

        # Aggiungi l'allegato al messaggio
        msg.attach(part)

        # Connessione e Invio
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            # server.set_debuglevel(1) # Rimuovi il commento per debug
            server.starttls()  # Passa alla modalità TLS/STARTTLS
            server.login(SMTP_USERNAME, SMTP_PASSWORD)
            text = msg.as_string()
            server.sendmail(SENDER_EMAIL, RECIPIENT_EMAIL, text)
            
        # Successo: Esci con codice 0
        sys.exit(0)

    except Exception as e:
        # Fallimento: Stampa l'errore ed esci con codice 1
        print(f"Error during email sending: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    send_attached_email()