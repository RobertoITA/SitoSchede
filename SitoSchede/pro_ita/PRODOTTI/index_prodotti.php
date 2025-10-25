<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco dei file PDF e HTML</title>
<style>
    /* Definizione del colore Verde Scuro per il testo */
    :root {
        --dark-green: #004d00; /* Verde molto scuro */
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        flex-direction: column;
        min-height: 100vh;
        background: linear-gradient(to bottom, #003000, #003f00 15%, #FFFFFF 38%, #FFFFFF 62%, #630000 85%, #630000);
        color: white; /* Colore base testo */
        text-align: center;
        padding: 20px;
        padding-top: 50px; 
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        /* Modifica 1: Bordi a 2px e completamente trasparenti */
        border: 2px solid rgba(255, 255, 255, 0); 
        padding: 8px;
    }
    th {
        background-color: rgba(1, 1, 1, 0.75);
        color: rgba(255,255,255,0.9);
        width: 0px;
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
    
    .file-icon {
        width: 24px;
        height: 24px;
        margin-left: 5px;
    }
    thead {
        position: sticky;
        top: 0;
        background-color: rgb(102, 102, 101, 1);
        z-index: 1;
    }

    /* Stili per la riga di intestazione con Flexbox */
    .header-row {
        display: flex;
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 20px;
        width: 100%;
    }
    .header-title {
        flex-grow: 1; 
        text-align: center;
        color: white;
    }
    .search-input-container {
        width: 250px; 
        text-align: left;
    }

    /* Modifica 2: Stile Casella di Ricerca (Input) */
    .search-box {
        margin-bottom: 0; /* Rimuovi margin-bottom */
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 100%;
        background-color: #e0e0e0; /* Grigio Chiaro */
        color: var(--dark-green); /* Verde Scuro */
        font-weight: bold;
    }

    /* Modifica 3: Stile Pulsante Data/Ora (Link) */
    .date-button {
        padding: 10px 15px;
        background-color: #e0e0e0; /* Grigio Chiaro */
        color: var(--dark-green); /* Verde Scuro */
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-weight: bold;
    }
    .date-button:hover {
        background-color: #d0d0d0; /* Grigio leggermente più scuro all'hover */
    }

    /* Stile per il testo di avviso SCHEDA INCOMPLETA */
    .warning-text {
        font-style: italic;
        color: #ffcc00; 
    }
</style>
</head>
<body>

<div class="header-row">
    <!-- Contenitore Casella di Ricerca a Sinistra -->
    <div class="search-input-container">
        <!-- Classe search-box con stili aggiornati -->
        <input type="text" id="searchInput" class="search-box" placeholder="Cerca per nome del file...">
    </div>

    <!-- Titolo Centrale -->
    <h1 class="header-title">ELENCO DELLE SCHEDE DEI PRODOTTI ITALMONT</h1>

    <!-- Pulsante Data/Ora a Destra -->
    <a href="http://schede/SitoSchede/pro_ita/PRODOTTI/tutto.html" id="dateButton" class="date-button" target="_blank">
        Caricamento...
    </a>
</div>

<table>
    <thead>
        <tr>
            <th>SCHEDA NUMERO</th>
            <th>DESCRIZIONE</th>
            <th>Schede Tecniche</th>
            <th>Schede di sicurezza</th>
            <th>Scheda Analitica Prodotto</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Percorso della directory contenente le sottocartelle
    $dir_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';

    // Scandisci la directory per trovare le sottocartelle con file PDF e PHP
    $subdirs = array_filter(glob($dir_path . '*'), 'is_dir');
    foreach ($subdirs as $subdir) {
        $subdir_name = basename($subdir);
        $pdf_files = glob($subdir . '/*.pdf');
        $php_files = glob($subdir . '/*.php');
        
        // Filtra i file PDF per Schede Tecniche (ST) e Schede di Sicurezza (SDS)
        $pdf_files_st = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'ST -') === 0;
        });
        $pdf_files_sds = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'SDS -') === 0;
        });
        // Filtra i file PHP per Scheda Analitica Prodotto (HTML)
        $php_files_php = array_filter($php_files, function($file) {
            return strpos(basename($file), 'HTML -') === 0;
        });

        // Variabile per la descrizione del prodotto
        $descrizione = "N/A";
        $csv_file_path = $subdir . '/SCHEDA.csv';
        $csv_exists = file_exists($csv_file_path); // Traccia l'esistenza del CSV

        // Se il file SCHEDA.csv esiste, leggi la descrizione
        if ($csv_exists) {
            $csv_file = fopen($csv_file_path, 'r');
            if ($csv_file !== false) {
                $row_count = 0;
                while (($data = fgetcsv($csv_file)) !== false) {
                    $row_count++;
                    if ($row_count == 3) { // Terza riga
                        $descrizione = $data[0]; // Prima colonna
                        break;
                    }
                }
                fclose($csv_file);
            }
        } else {
            // Se il CSV non esiste, imposta il messaggio di avviso
            $descrizione = "<span class='warning-text'>SCHEDA INCOMPLETA O DISMESSA</span>";
        }

        // Condizione per mostrare la riga: Deve esserci almeno un file PDF o PHP (anche se manca il CSV)
        if (!empty($pdf_files) || !empty($php_files)) {
            echo "<tr>";
            
            // Colonna 1: SCHEDA NUMERO (Link all'esplorazione della directory)
            echo "<td><a href='explore_directory.php?dir=" . urlencode($subdir_name) . "' class='file-link' target='_blank'>$subdir_name</a></td>";
            
            // Colonna 2: DESCRIZIONE
            echo "<td>$descrizione</td>";

            // Se il CSV non esiste, inserisce "-" nelle colonne dei file, altrimenti inserisce i link
            if (!$csv_exists) {
                echo "<td>-</td>"; // Schede Tecniche
                echo "<td>-</td>"; // Schede di sicurezza
                echo "<td>-</td>"; // Scheda Analitica Prodotto
            } else {
                // Colonna 3: Schede Tecniche
                echo "<td>";
                foreach ($pdf_files_st as $pdf_file) {
                    // Ottieni il nome del file senza estensione e rimuovi il prefisso "ST -"
                    $pdf_file_name = pathinfo(basename($pdf_file), PATHINFO_FILENAME);
                    $pdf_file_name = str_replace('ST - ', '', $pdf_file_name);
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($pdf_file) . "' class='file-link' target='_blank'>$pdf_file_name</a><br>";
                }
                echo "</td>";
                
                // Colonna 4: Schede di sicurezza
                echo "<td>";
                foreach ($pdf_files_sds as $pdf_file) {
                    // Ottieni il nome del file senza estensione e rimuovi il prefisso "SDS -"
                    $pdf_file_name = pathinfo(basename($pdf_file), PATHINFO_FILENAME);
                    $pdf_file_name = str_replace('SDS - ', '', $pdf_file_name);
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($pdf_file) . "' class='file-link' target='_blank'>$pdf_file_name</a><br>";
                }
                echo "</td>";
                
                // Colonna 5: Scheda Analitica Prodotto
                echo "<td>";
                foreach ($php_files_php as $php_file) {
                    // Ottieni il nome del file senza estensione e rimuovi il prefisso "HTML -"
                    $php_file_name = pathinfo(basename($php_file), PATHINFO_FILENAME);
                    $php_file_name = str_replace('HTML - ', '', $php_file_name);
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($php_file) . "' class='file-link' target='_blank'>$php_file_name</a><br>";
                }
                echo "</td>";
            }
            
            echo "</tr>";
        }
    }
    ?>
    </tbody>
</table>

<script>
    // Funzione per il filtro di ricerca
    document.getElementById("searchInput").addEventListener("input", function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) { 
            // Cerca solo nel corpo della tabella
            if (tr[i].parentNode.tagName === 'TBODY') { 
                td = tr[i].getElementsByTagName("td");
                var rowMatch = false; 
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        // Prendi il testo interno (ignora HTML, come lo span)
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            rowMatch = true;
                            break; 
                        }
                    }
                }
                if (rowMatch) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

    // Funzione per aggiornare la data e l'ora sul pulsante
    function updateDateTimeButton() {
        var now = new Date();
        var date = now.toLocaleDateString('it-IT');
        var time = now.toLocaleTimeString('it-IT');
        document.getElementById('dateButton').textContent = date + ' ' + time;
    }

    // Aggiorna subito al caricamento della pagina e poi ogni secondo
    updateDateTimeButton();
    setInterval(updateDateTimeButton, 1000);
</script>

</body>
</html>