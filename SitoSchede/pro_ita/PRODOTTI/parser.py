#!/usr/bin/env python3
import os
import json
import re
from pathlib import Path

# Funzione per pulire chiavi e valori da caratteri invisibili/anomali
def clean_key_value(s):
    # Rimuove BOM (Byte Order Mark) e altri caratteri non stampabili
    s = s.replace('\ufeff', '')
    # Rimuove spazi anomali (tab, carriage return)
    # s = s.replace('\t', '').replace('\r', '')
    # Rimuove caratteri di controllo ASCII/non stampabili e poi trim
    s = re.sub(r'[\x00-\x1F\x7F-\xFF]', '', s).strip()
    # Rimuove eventuali delimitatori residui all'inizio (es. da CODICE ARTICOLO;;;;P1005)
    s = re.sub(r'^[ ;:]+', '', s)
    return s

def parse_scheda_file(file_path):
    try:
        # Leggi il contenuto con gestione degli errori di codifica
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Dividi in righe e rimuovi gli spazi bianchi esterni e le righe completamente vuote
        lines = [line.strip() for line in content.split('\n') if line.strip()]
        
        if len(lines) < 4:
            return None
            
        data = {
            'SCHEDA_NUMERO': clean_key_value(lines[0]),
            'LOGO_FILENAME': clean_key_value(lines[1]),
            'NOME_PRODOTTO': clean_key_value(lines[2]),
            'IMMAGINE_FILENAME': clean_key_value(lines[3]),
            'dettagli': {}
        }
        
        current_key = None
        current_value = []
        
        # Inizia il parsing dei dettagli dalla riga 5 (indice 4)
        for line in lines[4:]:
            if ';;' in line:
                # Inizio di una nuova chiave, salva la precedente (se esiste)
                if current_key and current_value:
                    data['dettagli'][current_key] = '\n'.join([clean_key_value(v) for v in current_value])
                
                parts = line.split(';;', 1)
                
                # Pulisci la chiave prima di assegnarla
                current_key = clean_key_value(parts[0])
                
                # Pulisci il valore iniziale
                initial_value = clean_key_value(parts[1])
                current_value = [initial_value] if initial_value else []
            else:
                # Continuazione del valore precedente (multiriga)
                if current_key is not None:
                    # Pulisci anche le righe di continuazione (rimuove spazi anomali)
                    current_value.append(clean_key_value(line))
        
        # Salva l'ultimo campo dopo il ciclo
        if current_key and current_value:
            data['dettagli'][current_key] = '\n'.join([clean_key_value(v) for v in current_value])
            
        return data
        
    except Exception as e:
        # Stampa l'errore per il debug PHP shell_exec
        print(f"Error parsing {file_path}: {e}", file=os.sys.stderr)
        return None

def main():
    base_dir = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/'
    all_data = {}
    
    # Assicurati che il percorso esista prima di iterare
    base_path = Path(base_dir)
    if not base_path.exists():
        print(f"Error: Base directory {base_dir} not found.", file=os.sys.stderr)
        return
        
    for product_dir in base_path.iterdir():
        if product_dir.is_dir():
            scheda_file = product_dir / 'SCHEDA.csv'
            if scheda_file.exists():
                data = parse_scheda_file(scheda_file)
                if data:
                    all_data[product_dir.name] = data
    
    # Output JSON finale (necessario per l'esecuzione shell_exec)
    print("Content-Type: application/json\n")
    print(json.dumps(all_data, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    main()
