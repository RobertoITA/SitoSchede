<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <?php
    header('Content-Type: text/html; charset=utf-8');
    
    // Il file SCHEDA.csv si trova nella stessa directory di HTML - SCHEDA.php (es. /PRODOTTI/001/)
    $fileHandle = fopen("SCHEDA.csv", "r");
    if (!$fileHandle) {
        echo "<title>Impossibile aprire il file</title>";
    } else {
        $data = [];
        while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
            // Conversione per gestire correttamente caratteri come accenti
            foreach ($row as $key => $value) {
                $row[$key] = mb_convert_encoding($value, "UTF-8", "ISO-8859-1");
            }
            $data[] = $row;
        }
        fclose($fileHandle);

        // Estrazione della prima voce dal file CSV (che è il numero di scheda/nome cartella)
        $schedaNumero = htmlspecialchars($data[0][0]);
        echo "<title>Scheda " . $schedaNumero . "</title>";
        
        // Determina il percorso base del PRODOTTI (due livelli sopra)
        // Se HTML - SCHEDA.php è in /PRODOTTI/001/, il percorso base è /PRODOTTI/
        $BASE_DIR_URL = '../'; 
        
        // Percorso assoluto della directory corrente per lo script Python (DEVE ESSERE ASSOLUTO PER IL SERVER!)
        // L'uso di getcwd() prende la directory dove risiede HTML - SCHEDA.php (es. /var/www/html/SitoSchede/pro_ita/PRODOTTI/001/)
        $SERVER_FILE_PATH_BASE = getcwd() . '/';
    }
    ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(to bottom, #003000, #003f00 15%, #FFFFFF 38%, #FFFFFF 62%, #630000 85%, #630000);
            color: #211c3c;
            text-align: center;
        }

        .header-table, .info-table {
            width: 95%;
            margin: 20px auto;
            box-shadow: 0px 0px 0px rgba(0,0,0,0.1);
            border-collapse: collapse;
            background-color: transparent;
            color: white;
        }

        .header-table {
            table-layout: auto;
        }

        .header-table .left-column {
            text-align: left;
            padding-left: 5px;
        }

        .header-table h1 {
            margin: 0;
            font-size: 72px;
        }

        .scheda-logo {
            width: 180px;
            height: 120px;
        }

        .scheda-img {
            width: 140px;
            height: 150px;
        }

        .info-table {
            background-color: rgba(255, 255, 255, 0.7);
            color: black;
            box-shadow: 10px 10px 10px rgba(0,0,0,0.1);
            table-layout: fixed;
        }

        .info-table th, .info-table td {
            padding: 10px;
            border-bottom: 2px solid #008000;
            border-top: 2px solid #008000;
            vertical-align: top;
            word-break: break-word;
            white-space: normal;
            text-align: left;
        }

        .info-table th {
            width: 180px;
            min-width: 180px;
        }

        .file-entry {
            display: flex;
            align-items: center;
            margin-bottom: 5px; /* Spazio tra i link dei file */
        }

        .file-entry a {
            margin-right: 5px;
        }

        .file-icon {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        footer {
            background: #630000;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
            color: #888888;
        }

        @media (max-width: 600px) {
            .info-table th, .info-table td {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<?php
if (isset($data)) {
    // Estrazione dei dati dall'intestazione
    $logoFile = htmlspecialchars($data[1][0]);
    $title = htmlspecialchars($data[2][0]);
    $schedaImg = htmlspecialchars($data[3][0]);
?>

<table class="header-table">
    <tr>
        <td class="left-column">
            <span>SCHEDA NUMERO: <?php echo $schedaNumero; ?></span><br>
            <img src="<?php echo $logoFile; ?>" alt="Logo della scheda" class="scheda-logo">
        </td>
        <td style="text-align: center;">
            <h1><?php echo $title; ?></h1>
        </td>
        <td style="text-align: right;">
            <img src="<?php echo $schedaImg; ?>" alt="Immagine della scheda" class="scheda-img">
        </td>
    </tr>
</table>

<table class="info-table">
    <?php
    for ($i = 4; $i < count($data); $i++) {
        $row = $data[$i];

        // Filtra righe vuote
        $filtered_row = array_filter($row, fn($value) => !empty(trim($value)));

        if (count($filtered_row) > 1) {
            echo '<tr>';
            echo '<th>' . htmlspecialchars(array_shift($filtered_row)) . '</th>';
            echo '<td>';
            // Unisci tutte le colonne non vuote in una sola stringa separata da un salto di riga
            echo nl2br(htmlspecialchars(implode("\n", $filtered_row)));
            echo '</td>';
            echo '</tr>';
        }
    }
    ?>
</table>

<table class="info-table">
    <tr>
        <th>SCHEDE TECNICHE</th>
        <th>SCHEDE DI SICUREZZA</th>
        <th>CERTIFICATI</th>
    </tr>
    <tr>
        <td>
            <?php
            // Funzione per generare il link e l'icona email
            function generateFileEntry($filename, $baseDirUrl, $serverFilePathBase) {
                // Costruisci il percorso assoluto per lo script Python
                $server_filepath = $serverFilePathBase . $filename;
                $encoded_server_filepath = urlencode($server_filepath);
                $encoded_file_name = urlencode($filename);
                
                // Determina il percorso relativo dello script PHP intermedio (../invia_email_server.php)
                $email_script_url = $baseDirUrl . 'invia_email_server.php';

                echo '<div class="file-entry">';
                // Link per il download (usa il nome del file perché siamo nella stessa cartella)
                echo "<a href='" . htmlspecialchars($filename) . "' download>" . htmlspecialchars($filename) . "</a>";
                
                // Link Email con JavaScript e percorso RELATIVO per l'icona
                echo "<a href=\"javascript:void(0);\" 
                        onclick=\"var recipient = prompt('Inserisci l\'indirizzo email del destinatario:'); 
                                 if (recipient) { 
                                     window.location.href = '" . $email_script_url . "?path=$encoded_server_filepath&filename=$encoded_file_name&to=' + encodeURIComponent(recipient); 
                                 }\">
                        <img src='" . $baseDirUrl . "em.png' class='file-icon' alt='Invia per email'>
                      </a>";
                echo '</div>';
            }

            $files = glob("ST -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    generateFileEntry($filename, $BASE_DIR_URL, $SERVER_FILE_PATH_BASE);
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
        <td>
            <?php
            $files = glob("SDS -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    generateFileEntry($filename, $BASE_DIR_URL, $SERVER_FILE_PATH_BASE);
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
        <td>
            <?php
            $files = glob("CER -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    generateFileEntry($filename, $BASE_DIR_URL, $SERVER_FILE_PATH_BASE);
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
    </tr>
</table>

<?php
}
?>

<footer>
    Italmont Srl - Via IV Novembre, 13 63078 Pagliare del Tronto - Spinetoli (AP) Part. IVA 01441970447 Tel. 0736899238 Fax 0736899489 <a href="http://www.italmont.it" target="_blank">www.italmont.it</a> E-mail: <a href="mailto:info@italmont.it">info@italmont.it</a>
</footer>

</body>
</html>