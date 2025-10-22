<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contenuto dei File SCHEDA.csv</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        flex-direction: column;
        min-height: 100vh;
        background: linear-gradient(to bottom, #003000, #003f00 15%, #FFFFFF 38%, #FFFFFF 62%, #630000 85%, #630000);
        color: #211c3c;
        text-align: center;
        padding-top: 50px;
    }
    table {
        width: 98%; /* Ancora più largo per massimizzare lo spazio */
        margin: 20px auto;
        border-collapse: collapse;
        background-color: rgba(0, 0, 0, 0.75);
        color: white;
    }
    th, td {
        border: 1px solid white;
        padding: 8px;
        text-align: left;
        vertical-align: top;
        font-size: 0.85em; /* Rendi il testo un po' più piccolo per compattezza */
    }
    th {
        background-color: rgba(1, 1, 1, 0.75);
        color: rgba(255,255,255,0.9);
        position: sticky;
        top: 0;
        z-index: 2; /* Aumenta z-index per sovrapporre il contenuto in caso di scroll */

        /* Stili per testo verticale */
        height: 150px; /* Altezza per visualizzare il testo ruotato */
        padding: 0 5px; /* Riduci il padding orizzontale */
        white-space: nowrap; /* Evita il wrap del testo prima della rotazione */
        text-align: center; /* Centra il testo all'interno della cella ruotata */
    }
    th > div { /* Wrapper per il testo delle intestazioni */
        transform: rotate(-90deg); /* Ruota di -90 gradi (antiorario) */
        transform-origin: center center; /* Punto di origine della rotazione */
        width: 100%; /* Occupa tutta la larghezza del div */
        position: absolute;
        bottom: 0; /* Allinea il testo ruotato verso il basso della cella */
        left: 50%; /* Centra orizzontalmente */
        transform: translateX(-50%) rotate(-90deg); /* Combina centratura e rotazione */
        display: flex; /* Usa flexbox per centrare verticalmente il testo ruotato */
        align-items: center; /* Centra verticalmente */
        justify-content: center; /* Centra orizzontalmente */
        height: 100%; /* Occupa tutta l'altezza del TH */
    }

    /* Stile specifico per la prima colonna (Nome Prodotto + link cartella) */
    td:first-child {
        min-width: 150px; /* Larghezza fissa per la prima colonna */
        max-width: 200px; /* Larghezza massima per la prima colonna */
        white-space: normal; /* Permetti il wrap del testo */
        text-align: left;
    }

    tr:nth-child(even) {
        background-color: rgba(64, 64, 64, 0.75);
    }
    tr:nth-child(odd) {
        background-color: rgb(102, 102, 101, 0.75);
    }
    .file-link {
        color: white;
        text-decoration: none;
    }
    .file-link:hover {
        color: yellow;
    }
    .search-box {
        margin-bottom: 20px;
        padding: 10px;
        width: 400px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    h1 {
        color: white;
        margin-bottom: 30px;
    }
    /* Stile per l'immagine del logo */
    td img {
        max-width: 40px; /* Riduci la dimensione massima delle immagini */
        max-height: 40px;
        display: block; /* Centra l'immagine nella cella */
        margin: auto;
    }
</style>
</head>
<body>

<h1>DETTAGLI PRODOTTI DAI FILE SCHEDA.csv</h1>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca nel contenuto delle schede...">

<table>
    <thead>
        <tr>
            <th><div>Nome Prodotto</div></th> <!-- Questa sarà la nuova prima colonna fissa -->
            <!-- Le intestazioni delle colonne CSV verranno generate qui dal PHP -->
        </tr>
    </thead>
    <tbody>
    <?php
    // Percorso della directory contenente le sottocartelle
    $base_dir_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
    $subdirs = array_filter(glob($base_dir_path . '*'), 'is_dir');

    // Array per memorizzare tutte le intestazioni uniche trovate
    // Ho rimosso le prime 3 righe da qui, verranno gestite come dati della prima colonna o colonne fisse.
    $unique_headers = [];
    foreach ($subdirs as $subdir) {
        $csv_file_path = $subdir . '/SCHEDA.csv';
        if (file_exists($csv_file_path)) {
            $file_content = file($csv_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            // Inizia a leggere dalla riga 3 (indice 3) per le intestazioni chiave;;valore
            for ($i = 3; $i < count($file_content); $i++) {
                $line = $file_content[$i];
                $parts = explode(';;', $line, 2);
                if (count($parts) == 2) {
                    $header = trim($parts[0]);
                    if (!in_array($header, $unique_headers)) {
                        $unique_headers[] = $header;
                    }
                }
            }
        }
    }
    
    // Ordina le intestazioni alfabeticamente per consistenza (opzionale)
    sort($unique_headers);

    // Prepara le intestazioni per la stampa nell'HTML
    echo "<thead><tr>";
    // La prima intestazione è "Nome Prodotto" per la colonna fissa
    echo "<th><div>Nome Prodotto</div></th>"; // La prima intestazione gestirà Nome Prodotto + link
    
    foreach ($unique_headers as $header) {
        echo "<th><div>" . htmlspecialchars($header) . "</div></th>";
    }
    echo "</tr></thead>";
    echo "<tbody>";

    foreach ($subdirs as $subdir) {
        $subdir_name = basename($subdir);
        $csv_file_path = $subdir . '/SCHEDA.csv';
        $product_data = [];
        
        // Inizializza i dati con valori vuoti per tutte le intestazioni finali
        foreach ($unique_headers as $header) {
            $product_data[$header] = '';
        }

        // Recupera i dati specifici dalle prime tre righe del CSV
        $scheda_numero = 'N/A';
        $logo_image = ''; // Vuoto di default
        $nome_prodotto = 'N/A';

        if (file_exists($csv_file_path)) {
            $file_content = file($csv_file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if (isset($file_content[0])) $scheda_numero = htmlspecialchars($file_content[0]);
            if (isset($file_content[1])) {
                 $logo_filename = htmlspecialchars($file_content[1]);
                 $image_path = "/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . $logo_filename;
                 if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) { // Usa il percorso assoluto per verificare
                    $logo_image = "<a href='$image_path' target='_blank'><img src='$image_path' alt='Logo'></a>";
                 }
            }
            if (isset($file_content[2])) $nome_prodotto = htmlspecialchars($file_content[2]);

            // Processa le righe successive (da indice 3 in poi) come chiave;;valore
            for ($i = 3; $i < count($file_content); $i++) {
                $line = $file_content[$i];
                $parts = explode(';;', $line, 2);
                if (count($parts) == 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    $product_data[$key] = htmlspecialchars($value);
                }
            }
        }

        echo "<tr>";
        // Nuova prima colonna: Nome Prodotto, Numero Scheda e Immagine, più il link alla cartella
        echo "<td>";
        echo "<strong>$nome_prodotto</strong><br>";
        echo "Scheda n. $scheda_numero<br>";
        echo $logo_image;
        echo "<br><a href='explore_directory.php?dir=" . urlencode($subdir_name) . "' class='file-link' target='_blank'>Vai alla cartella</a>";
        echo "</td>";
        
        // Output dei dati per le colonne basate su chiave;;valore
        foreach ($unique_headers as $header) {
            echo "<td>" . (isset($product_data[$header]) ? $product_data[$header] : '') . "</td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>

<script>
    document.getElementById("searchInput").addEventListener("input", function() {
        var input, filter, table, tr, i, td, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");

        // Loop attraverso tutte le righe della tabella, escludendo l'intestazione
        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = "none"; // Nascondi la riga per default
            td = tr[i].getElementsByTagName("td"); // Prendi tutte le celle della riga

            // Loop attraverso tutte le celle della riga
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ""; // Mostra la riga se c'è una corrispondenza
                        break; // Esci dal ciclo delle celle per questa riga
                    }
                }
            }
        }
    });
</script>

</body>
</html>