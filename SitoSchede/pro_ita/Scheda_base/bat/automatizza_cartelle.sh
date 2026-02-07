#!/bin/bash

# --- CONFIGURAZIONE ---
BASE_DEST="/var/www/html/SitoSchede/pro_ita/PRODOTTI"

# Colori per l'output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variabili globali per la gestione dei conflitti (Sì a tutti / No a tutti)
OVERWRITE_ALL=false
SKIP_ALL=false

# --- FUNZIONI ---

# Funzione per gestire i conflitti (File/Cartella già esistente)
check_conflict() {
    local target="$1"
    
    # Se il file non esiste, procedi pure (ritorna 0)
    if [ ! -e "$target" ]; then
        return 0
    fi

    # Se l'utente ha già detto "Salta tutto", salta (ritorna 1)
    if [ "$SKIP_ALL" = true ]; then
        return 1
    fi

    # Se l'utente ha già detto "Sovrascrivi tutto", procedi (ritorna 0)
    if [ "$OVERWRITE_ALL" = true ]; then
        return 0
    fi

    # Altrimenti chiedi all'utente
    echo -e "${YELLOW}ATTENZIONE: Il file/cartella esiste già in: $target${NC}"
    while true; do
        read -p "Vuoi sovrascriverlo? [y]Sì, [n]No, [a]Sì a tutti, [s]Salta tutti i conflitti: " choice
        case "$choice" in 
            y|Y ) return 0 ;;      # Sovrascrivi questo
            n|N ) return 1 ;;      # Salta questo
            a|A ) OVERWRITE_ALL=true; return 0 ;; # Da ora in poi sovrascrivi sempre
            s|S ) SKIP_ALL=true; return 1 ;;      # Da ora in poi salta sempre se esiste
            * ) echo "Scelta non valida.";;
        esac
    done
}

# Funzione Principale per il Loop
process_loop() {
    local action="$1"      # "copy" o "create"
    local type="$2"        # "file" o "folder" (solo per create)
    local input_items=("${@:3}") # Array degli elementi da processare

    echo -e "\n${GREEN}Inizio operazione...${NC}"

    for i in {1..200}; do
        # Formatta cartella 001, 002...
        FOLDER_NAME=$(printf "%03d" $i)
        TARGET_DIR="$BASE_DEST/$FOLDER_NAME"

        # Verifica se la cartella prodotto esiste (es. 001 esiste?)
        if [ ! -d "$TARGET_DIR" ]; then
            # echo "Cartella $TARGET_DIR non trovata, salto..."
            continue
        fi

        # Reset variabili per ogni elemento nel loop interno? No, manteniamo lo stato globale per "Sì a tutti".
        
        # Logica COPIA
        if [ "$action" == "copy" ]; then
            for item in "${input_items[@]}"; do
                item_name=$(basename "$item")
                DEST_PATH="$TARGET_DIR/$item_name"

                if check_conflict "$DEST_PATH"; then
                    cp -r "$item" "$DEST_PATH"
                    echo "Copiato $item_name in $FOLDER_NAME"
                else
                    echo "Saltato $item_name in $FOLDER_NAME"
                fi
            done
        
        # Logica CREAZIONE
        elif [ "$action" == "create" ]; then
            for item_name in "${input_items[@]}"; do
                DEST_PATH="$TARGET_DIR/$item_name"
                
                if check_conflict "$DEST_PATH"; then
                    if [ "$type" == "folder" ]; then
                        mkdir -p "$DEST_PATH"
                        echo "Creata cartella $item_name in $FOLDER_NAME"
                    else
                        touch "$DEST_PATH"
                        echo "Creato file $item_name in $FOLDER_NAME"
                    fi
                else
                    echo "Saltato $item_name in $FOLDER_NAME"
                fi
            done
        fi
    done
}

# --- MENU PRINCIPALE ---

clear
echo "------------------------------------------------"
echo "   GESTIONE AUTOMATIZZATA SCHEDE PRODOTTO"
echo "   Base: $BASE_DEST"
echo "------------------------------------------------"
echo "Cosa vuoi fare?"
echo "1) COPIARE file o cartelle esistenti nelle sottocartelle"
echo "2) CREARE nuovi file o cartelle vuote nelle sottocartelle"
echo "3) Esci"
read -p "Seleziona (1-3): " main_choice

case $main_choice in
    1)
        echo -e "\n--- MODALITÀ COPIA ---"
        echo "Inserisci i percorsi dei file/cartelle da copiare."
        echo "Puoi inserirne più di uno separati da spazio."
        echo "Esempio: /tmp/foto.jpg /tmp/dati.txt"
        read -e -p "Percorsi: " -a sources # -a legge in un array
        
        # Verifica che i sorgenti esistano
        valid_sources=()
        for src in "${sources[@]}"; do
            if [ -e "$src" ]; then
                valid_sources+=("$src")
            else
                echo -e "${RED}Errore: Il file sorgente '$src' non esiste. Ignorato.${NC}"
            fi
        done

        if [ ${#valid_sources[@]} -eq 0 ]; then
            echo "Nessun file valido selezionato. Uscita."
            exit 1
        fi

        process_loop "copy" "none" "${valid_sources[@]}"
        ;;

    2)
        echo -e "\n--- MODALITÀ CREAZIONE ---"
        read -p "Vuoi creare (f)ile o (c)artelle? [f/c]: " create_type
        
        if [[ "$create_type" == "f" ]]; then
            ctype="file"
            echo "Inserisci i nomi dei file da creare (es: index.html style.css)"
        elif [[ "$create_type" == "c" ]]; then
            ctype="folder"
            echo "Inserisci i nomi delle cartelle da creare (es: images docs)"
        else
            echo "Scelta non valida."
            exit 1
        fi

        read -e -p "Nomi: " -a names
        
        if [ ${#names[@]} -eq 0 ]; then
            echo "Nessun nome inserito. Uscita."
            exit 1
        fi

        process_loop "create" "$ctype" "${names[@]}"
        ;;

    3)
        echo "Uscita."
        exit 0
        ;;
    *)
        echo "Opzione non valida."
        ;;
esac

echo -e "\n${GREEN}Operazione completata.${NC}"
