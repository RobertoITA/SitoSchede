<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <?php
    header('Content-Type: text/html; charset=utf-8');
    
    // Il file SCHEDA.csv si trova nella stessa directory
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
        
        // Percorso RELATIVO per i link HTML (../ per tornare alla cartella PRODOTTI/)
        $BASE_DIR_URL = '../'; 
        
        // Percorso assoluto della directory corrente per lo script Python (es. /var/www/.../PRODOTTI/001/)
        $SERVER_FILE_PATH_BASE = rtrim(getcwd(), '/') . '/';
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
            margin-bottom: 5px; 
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
        
        /* CSS per il Modale (Aggiunto per l'invio via AJAX) */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
            padding-top: 50px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            color: black;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* Aggiungo lo stile per i messaggi */
        #emailStatus {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }

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

        $filtered_row = array_filter($row, fn($value) => !empty(trim($value)));

        if (count($filtered_row) > 1) {
            echo '<tr>';
            echo '<th>' . htmlspecialchars(array_shift($filtered_row)) . '</th>';
            echo '<td>';
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
                $encoded_server_filepath = htmlspecialchars(urlencode($server_filepath));
                $encoded_file_name = htmlspecialchars(urlencode($filename));
                
                echo '<div class="file-entry">';
                // Link per il download
                echo "<a href='" . htmlspecialchars($filename) . "' download>" . htmlspecialchars($filename) . "</a>";
                
                // Chiama una funzione JS per aprire il modale
                // L'icona è referenziata tramite percorso RELATIVO (../em.png)
                echo "<a href=\"javascript:void(0);\" 
                        onclick=\"openEmailModal('{$encoded_server_filepath}', '{$encoded_file_name}');\">
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

<!-- *** MODALE PER L'INVIO EMAIL *** -->
<div id="emailModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEmailModal()">&times;</span>
        <h2>Invia Scheda per Email</h2>
        
        <form id="emailForm">
            <input type="hidden" id="filePathMain" name="filePathMain" value="">
            <input type="hidden" id="fileNameMain" name="fileNameMain" value="">

            <p><strong>Allegato Principale:</strong> <span id="fileNameDisplay"></span></p>

            <label for="recipients">Destinatari (separati da virgola, spazio o punto e virgola):</label>
            <input type="text" id="recipients" name="recipients" required style="width: 95%; padding: 8px; margin-bottom: 10px;">

            <label for="extraFiles">Aggiungi altri file (nomi separati da virgola, es: file1.pdf, logo.jpg):</label>
            <input type="text" id="extraFiles" name="extraFiles" style="width: 95%; padding: 8px; margin-bottom: 20px;">
            
            <button type="submit" style="padding: 10px 20px; background-color: #008000; color: white; border: none; cursor: pointer;">Invia Email</button>
        </form>

        <div id="emailStatus"></div>

    </div>
</div>
<!-- *** FINE MODALE *** -->

<footer>
    Italmont Srl - Via IV Novembre, 13 63078 Pagliare del Tronto - Spinetoli (AP) Part. IVA 01441970447 Tel. 0736899238 Fax 0736899489 <a href="http://www.italmont.it" target="_blank">www.italmont.it</a> E-mail: <a href="mailto:info@italmont.it">info@italmont.it</a>
</footer>

<script>
    const EMAIL_HANDLER_URL = '<?php echo $BASE_DIR_URL; ?>invia_email_scheda_server.php';
    const modal = document.getElementById('emailModal');
    const statusDiv = document.getElementById('emailStatus');

    function openEmailModal(filePath, fileName) {
        document.getElementById('filePathMain').value = decodeURIComponent(filePath);
        document.getElementById('fileNameMain').value = decodeURIComponent(fileName);
        document.getElementById('fileNameDisplay').innerText = decodeURIComponent(fileName);
        
        statusDiv.style.display = 'none';
        modal.style.display = 'block';
    }

    function closeEmailModal() {
        modal.style.display = 'none';
        document.getElementById('emailForm').reset();
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeEmailModal();
        }
    }

    document.getElementById('emailForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerText = 'Invio in corso...';
        
        statusDiv.style.display = 'none';
        
        const formData = new FormData(this);
        
        fetch(EMAIL_HANDLER_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerText = 'Invia Email';
            
            statusDiv.style.display = 'block';
            statusDiv.classList.remove('status-success', 'status-error');

            if (data.status === 'success') {
                statusDiv.classList.add('status-success');
                statusDiv.innerHTML = data.message || 'Email inviata con successo!';
            } else {
                statusDiv.classList.add('status-error');
                // Uso innerHTML per mostrare i tag nl2br(htmlspecialchars($output)) provenienti da PHP
                statusDiv.innerHTML = data.message || 'Si è verificato un errore durante l\'invio.';
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.innerText = 'Invia Email';
            
            statusDiv.style.display = 'block';
            statusDiv.classList.add('status-error');
            statusDiv.innerText = 'Errore di connessione al server.';
            console.error('Error:', error);
        });
    });
</script>

</body>
</html>