#!/bin/bash

# Definisco i percorsi (adattati alla struttura Linux che hai indicato)
# NOTA: È importante usare le virgolette perché il nome del file contiene spazi.
SOURCE="/var/www/html/SitoSchede/pro_ita/Scheda_base/HTML - SCHEDA.php"
BASE_DEST="/var/www/html/SitoSchede/pro_ita/PRODOTTI"

# Ciclo for da 1 a 200
for i in {1..200}; do
    
    # "printf" formatta il numero con 3 cifre (padding con zeri)
    # Es: 1 diventa 001, 50 diventa 050, 200 resta 200
    FOLDER=$(printf "%03d" $i)
    
    # Costruisco il percorso completo della cartella di destinazione
    TARGET_DIR="$BASE_DEST/$FOLDER"

    # Controllo se la cartella esiste (-d verifica se è una directory)
    if [ -d "$TARGET_DIR" ]; then
        echo "Copiando in $TARGET_DIR"
        cp "$SOURCE" "$TARGET_DIR/"
    else
        echo "La cartella $TARGET_DIR non esiste."
    fi

done
