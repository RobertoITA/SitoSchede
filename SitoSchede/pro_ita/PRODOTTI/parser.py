#!/usr/bin/env python3
import os
import json
import re
from pathlib import Path
import tempfile

# Definizione della directory base. Assicurarsi che sia corretta.
BASE_DIR = Path('/var/www/html/SitoSchede/pro_ita/PRODOTTI/')

# Codifica del file sorgente CSV:
# Assumiamo ISO-8859-1 (latin-1) o windows-1252 per i file CSV generati
# su sistemi occidentali non configurati per UTF-8, dove risiede il problema.
SOURCE_ENCODING = 'latin-1' 

def clean_key_value(s):
    if s is None:
        return ''
    # Non è più necessario gestire il BOM, ma la pulizia è utile.
    # Rimuove solo caratteri di controllo (0x00-0x1F e 0x7F)
    s = re.sub(r'[\x00-\x1F\x7F]', '', s).strip()
    # Rimuove eventuali separatori indesiderati all'inizio del valore
    s = re.sub(r'^[ ;:]+', '', s)
    return s

def parse_scheda_file(file_path):
    try:
        # CORREZIONE: Cambia la codifica di lettura a 'latin-1' 
        # (o 'windows-1252' se necessario) per decodificare correttamente 
        # i caratteri accentati italiani dai file CSV.
        with open(file_path, 'r', encoding=SOURCE_ENCODING, errors='strict') as f:
            content = f.read()

        # Normalizza newline e mantieni righe non completamente vuote
        lines = [line.rstrip('\r') for line in content.split('\n')]
        lines = [line for line in lines if line.strip() != '']

        if len(lines) < 1:
            return None

        # Estrazione delle prime 4 righe
        data = {
            'SCHEDA_NUMERO': clean_key_value(lines[0]) if len(lines) > 0 else '',
            'LOGO_FILENAME': clean_key_value(lines[1]) if len(lines) > 1 else '',
            'NOME_PRODOTTO': clean_key_value(lines[2]) if len(lines) > 2 else '',
            'IMMAGINE_FILENAME': clean_key_value(lines[3]) if len(lines) > 3 else '',
            'dettagli': {}
        }

        current_key = None
        current_value = []

        # Parsifica i dettagli
        for line in lines[4:]:
            if ';;' in line:
                # salva precedente
                if current_key is not None:
                    # Non è necessario 'clean_key_value' sulle righe interne se si assume 
                    # che i dati siano puliti dopo la decodifica. Manteniamo la logica 
                    # per coerenza, ma il vero fix è l'encoding.
                    data['dettagli'][current_key] = '\n'.join([clean_key_value(v) for v in current_value])
                
                parts = line.split(';;', 1)
                current_key = clean_key_value(parts[0])
                initial_value = clean_key_value(parts[1] if len(parts) > 1 else '')
                current_value = [initial_value]
            else:
                if current_key is not None:
                    current_value.append(clean_key_value(line))

        # Salva l'ultimo blocco
        if current_key is not None:
            data['dettagli'][current_key] = '\n'.join([clean_key_value(v) for v in current_value])

        return data

    except Exception as e:
        # log su stderr
        print(f"ERROR parsing {file_path}: {e}", file=os.sys.stderr)
        return None

def build_catalog():
    if not BASE_DIR.exists():
        raise FileNotFoundError(f"Base directory {BASE_DIR} not found")

    all_data = {}
    for product_dir in sorted(BASE_DIR.iterdir()):
        if product_dir.is_dir():
            scheda_file = product_dir / 'SCHEDA.csv'
            if scheda_file.exists():
                parsed = parse_scheda_file(scheda_file)
                if parsed is not None:
                    # Usa il nome della cartella come chiave
                    all_data[product_dir.name] = parsed
    return all_data

def atomic_write_json(target_path: Path, data):
    # Scrive su file temporaneo e poi sposta (atomicamente) sul target
    target_dir = target_path.parent
    with tempfile.NamedTemporaryFile('w', delete=False, dir=str(target_dir), encoding='utf-8') as tmp:
        # Per correggere l'output JSON che di default usa \uXXXX per i caratteri non ASCII, 
        # usiamo 'ensure_ascii=False'. Questo garantirà che il JSON contenga direttamente 
        # i caratteri UTF-8 come 'à', 'è', 'ò', ecc.
        json.dump(data, tmp, ensure_ascii=False, indent=2)
        tmpname = tmp.name
    os.replace(tmpname, str(target_path))
    # Imposta permessi leggibili
    try:
        os.chmod(target_path, 0o644)
    except Exception:
        pass

def main():
    try:
        catalog = build_catalog()
        out_path = BASE_DIR / 'riepilogo.json'
        atomic_write_json(out_path, catalog)
    except Exception as e:
        print(f"ERROR: {e}", file=os.sys.stderr)

if __name__ == '__main__':
    main()