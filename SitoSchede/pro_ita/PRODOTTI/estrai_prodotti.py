import os
import pdfplumber
import re
import csv
import json
import sys

# --- CONFIGURAZIONE ---
BASE_DIR = "/var/www/html/SitoSchede/pro_ita/PRODOTTI/"
FILE_CSV = os.path.join(BASE_DIR, "riepilogo_sds.csv")
FILE_JSON = os.path.join(BASE_DIR, "riepilogo_sds.json")

# Verifica permessi di scrittura
if not os.access(BASE_DIR, os.W_OK):
    print(f"ERRORE CRITICO: Non hai i permessi di scrittura in {BASE_DIR}")
    print("Suggerimento: esegui 'sudo chown -R roberto:roberto /var/www/html/SitoSchede/pro_ita/PRODOTTI/'")
    sys.exit(1)

def pulisci_testo(testo):
    """Rimuove spazi extra, a capo e unità di misura comuni per pulire il dato."""
    if not testo: return "N/D"
    t = testo.replace("\n", " ").strip()
    return " ".join(t.split())

def estrai_dati_pdf(percorso_file, nome_cartella, nome_file):
    dati = {
        "Numero Scheda": nome_cartella,
        "Nome File": nome_file,
        "Nome Prodotto": "N/D",
        "Elementi Etichetta": "",     # Solo codici
        "Peso Specifico (g/cm3)": "N/D", # Densità relativa
        "Caratteristiche Particelle": "N/D",
        "VOC Espressi": "N/D",
        "Limite Massimo VOC": "N/D"
    }

    testo_completo = ""
    testo_sez2 = "" # Variabile per isolare la sezione etichettatura (prime pagine)

    try:
        with pdfplumber.open(percorso_file) as pdf:
            # Leggiamo le pagine
            for i, pagina in enumerate(pdf.pages):
                testo_pag = pagina.extract_text()
                if testo_pag:
                    testo_completo += testo_pag + "\n"
                    # Limitiamo la ricerca etichetta (Sez 2) alle prime 2-3 pagine
                    if i < 3: 
                        testo_sez2 += testo_pag + "\n"
    except Exception as e:
        print(f"Errore lettura {nome_file}: {e}")
        return dati

    # --- 1.1 Denominazione (Nome Prodotto) ---
    match_nome = re.search(r"Denominazione\s+(.+?)\n", testo_completo)
    if match_nome:
        dati["Nome Prodotto"] = match_nome.group(1).strip()

    # --- 2.2 Elementi dell'etichetta (SOLO CODICI) ---
    # Cerca codici H, P, EUH isolati da confini di parola
    codici_trovati = re.findall(r"\b(EUH\d{3}|H\d{3}|P\d{3})\b", testo_sez2)
    # Rimuovi duplicati e ordina
    codici_univoci = sorted(list(set(codici_trovati)))
    
    if codici_univoci:
        dati["Elementi Etichetta"] = ", ".join(codici_univoci)
    else:
        if "non classificato pericoloso" in testo_sez2.lower() or "non è classificato pericoloso" in testo_sez2.lower():
            dati["Elementi Etichetta"] = "Non classificato pericoloso"
        else:
            dati["Elementi Etichetta"] = "Nessun codice rilevato"

    # --- VOC (Sezione 2 o 15) ---
    # Cerca: VOC espressi in...
    match_voc = re.search(r"VOC espressi.*?:(.*?)(?=\n|Limite)", testo_completo, re.IGNORECASE)
    if match_voc:
        valore = match_voc.group(1).replace("g/litro", "").replace("di prodotto pronto all'uso", "").strip()
        dati["VOC Espressi"] = pulisci_testo(valore)

    # Cerca: Limite massimo
    match_limite = re.search(r"Limite massimo\s*:(.*?)(?=\n)", testo_completo, re.IGNORECASE)
    if match_limite:
        dati["Limite Massimo VOC"] = pulisci_testo(match_limite.group(1))

    # --- Proprietà Sezione 9 ---
    
    # Peso Specifico (Densità relativa)
    # Cerca "Densità e/o Densità relativa"
    match_dens = re.search(r"Densità e/o Densità relativa\s+(.*?)(?=\n)", testo_completo, re.IGNORECASE)
    if match_dens:
        # Pulisci eventuale unità di misura per avere il numero pulito, se vuoi
        dati["Peso Specifico (g/cm3)"] = pulisci_testo(match_dens.group(1))

    # Caratteristiche delle particelle
    match_particelle = re.search(r"Caratteristiche delle particelle\s+(.*?)(?=\n)", testo_completo, re.IGNORECASE)
    if match_particelle:
        dati["Caratteristiche Particelle"] = pulisci_testo(match_particelle.group(1))

    return dati

# --- MAIN LOOP ---
print(f"--- INIZIO SCANSIONE ---")
print(f"Directory base: {BASE_DIR}")
print("Filtro file: Inizia con 'SDS - ' AND contiene ' IT - ' AND finisce con '.pdf'")

risultati = []

try:
    # Elenco cartelle ordinate (001, 002...)
    cartelle = sorted([d for d in os.listdir(BASE_DIR) if os.path.isdir(os.path.join(BASE_DIR, d))])
except OSError as e:
    print(f"Errore accesso directory: {e}")
    sys.exit(1)

for cartella in cartelle:
    path_cartella = os.path.join(BASE_DIR, cartella)
    
    try:
        # Filtro specifico richiesto
        files = [f for f in os.listdir(path_cartella) 
                 if f.startswith("SDS -") and "- IT" in f and f.lower().endswith(".pdf")]
    except OSError:
        continue 

    for file_pdf in files:
        percorso_full = os.path.join(path_cartella, file_pdf)
        print(f"Elaborazione: [{cartella}] {file_pdf}")
        
        dati = estrai_dati_pdf(percorso_full, cartella, file_pdf)
        risultati.append(dati)

if not risultati:
    print("\nNessun file trovato. Controlla che i nomi dei file contengano 'SDS - ' e ' IT - '")
    sys.exit()

# --- 1. SCRITTURA CSV ---
try:
    with open(FILE_CSV, mode='w', newline='', encoding='utf-8') as csvfile:
        nomi_colonne = [
            "Numero Scheda", 
            "Nome File", 
            "Nome Prodotto", 
            "Elementi Etichetta", 
            "Peso Specifico (g/cm3)", 
            "Caratteristiche Particelle", 
            "VOC Espressi", 
            "Limite Massimo VOC"
        ]
        
        # Delimitatore punto e virgola per compatibilità Excel ITA
        writer = csv.DictWriter(csvfile, fieldnames=nomi_colonne, delimiter=';')
        writer.writeheader()
        writer.writerows(risultati)
    print(f"\n[OK] CSV creato: {FILE_CSV}")
except Exception as e:
    print(f"[ERR] Errore salvataggio CSV: {e}")

# --- 2. SCRITTURA JSON (Per PHP) ---
try:
    with open(FILE_JSON, mode='w', encoding='utf-8') as jsonfile:
        # ensure_ascii=False permette di salvare caratteri accentati leggibili
        json.dump(risultati, jsonfile, ensure_ascii=False, indent=4)
    print(f"[OK] JSON creato: {FILE_JSON}")
except Exception as e:
    print(f"[ERR] Errore salvataggio JSON: {e}")

print(f"\nOperazione completata. Elaborati {len(risultati)} file.")
