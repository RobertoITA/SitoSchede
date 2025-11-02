<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco dei file PDF</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: black;
        color: white;
        text-align: center;
        margin: 0;
        padding: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid white;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #333;
        color: white;
    }
    td:first-child {
        width: 15%; /* Larghezza per la colonna "SCHEDA NUMERO" */
        font-weight: bold;
    }
    tr:nth-child(even) { background-color: #444; }
    tr:nth-child(odd) { background-color: #555; }
    .file-link {
        color: white;
        text-decoration: none;
    }
    .file-link:hover { color: yellow; }
    .search-box {
        width: 50%;
        padding: 8px;
        margin-bottom: 20px;
    }
    .file-icon {
        width: 20px;
        height: 20px;
        margin-left: 8px;
        vertical-align: middle;
        cursor: pointer;
    }
    /* Stili per i messaggi di stato AJAX */
    #status-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 20px;
        border-radius: 5px;
        color: black;
        font-weight: bold;
        display: none; /* Nascosto di default */
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .status-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }
    .status-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
</style>
</head>
<body>

<!-- Contenitore per i messaggi di stato -->
<div id="status-container"></div>

<h2>Elenco dei file PDF</h2>
<h2>SCHEDE di Sicurezza - SDS | SCHEDE TECNICHE - ST</h2>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca per nome del file...">

<table>
    <thead>
        <tr>
            <th>SCHEDA NUMERO</th>
            <th>Schede Tecniche (ST)</th>
            <th>Schede di Sicurezza (SDS)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Percorso della directory base
        $dir_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';

        // Scansiona le sottocartelle
        $subdirs = array_filter(glob($dir_path . '*'), 'is_dir');
        foreach ($subdirs as $subdir) {
            $subdir_name = basename($subdir);
            
            // Filtra i file ST e SDS
            $pdf_files_st = glob($subdir . '/ST - *.pdf');
            $pdf_files_sds = glob($subdir . '/SDS - *.pdf');

            // Mostra la riga solo se c'è almeno un file
            if (!empty($pdf_files_st) || !empty($pdf_files_sds)) {
                echo "<tr>";
                echo "<td>$subdir_name</td>";
                
                // Cella per le Schede Tecniche (ST)
                echo "<td>";
                foreach ($pdf_files_st as $pdf_file) {
                    $pdf_file_name = basename($pdf_file);
                    // Percorso assoluto sul server, essenziale per lo script PHP
                    $server_filepath = $pdf_file; 
                    
                    echo "<div>";
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    
                    // Icona email che chiama la nuova funzione JavaScript
                    echo "<img src='em.png' class='file-icon' alt='Invia per email' 
                         onclick=\"sendEmail('" . htmlspecialchars($server_filepath, ENT_QUOTES) . "', '" . htmlspecialchars($pdf_file_name, ENT_QUOTES) . "')\">";
                    
                    echo "</div>";
                }
                echo "</td>";
                
                // Cella per le Schede di Sicurezza (SDS)
                echo "<td>";
                foreach ($pdf_files_sds as $pdf_file) {
                    $pdf_file_name = basename($pdf_file);
                    $server_filepath = $pdf_file;
                    
                    echo "<div>";
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    
                    // Icona email che chiama la nuova funzione JavaScript
                    echo "<img src='em.png' class='file-icon' alt='Invia per email' 
                         onclick=\"sendEmail('" . htmlspecialchars($server_filepath, ENT_QUOTES) . "', '" . htmlspecialchars($pdf_file_name, ENT_QUOTES) . "')\">";
                    
                    echo "</div>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>

<script>
    // Funzione per mostrare i messaggi di stato
    function showStatusMessage(message, type) {
        const statusContainer = document.getElementById('status-container');
        statusContainer.textContent = message;
        statusContainer.className = (type === 'success') ? 'status-success' : 'status-error';
        statusContainer.style.display = 'block';

        // Nasconde il messaggio dopo 5 secondi
        setTimeout(() => {
            statusContainer.style.display = 'none';
        }, 5000);
    }

    // NUOVA FUNZIONE per inviare l'email tramite AJAX
    function sendEmail(filePath, fileName) {
        const recipient = prompt("Inserisci l'indirizzo email del destinatario:", "");

        if (recipient && recipient.trim() !== "") {
            // Mostra un messaggio di attesa
            showStatusMessage("Invio dell'email in corso...", 'success');

            const formData = new FormData();
            formData.append('to', recipient);
            formData.append('path', filePath);
            formData.append('filename', fileName);

            fetch('invia_email_server.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showStatusMessage(data.message, 'success');
                } else {
                    // Mostra un errore più dettagliato
                    const errorMessage = data.details ? `${data.message} Dettagli: ${data.details}` : data.message;
                    showStatusMessage(errorMessage, 'error');
                }
            })
            .catch(error => {
                console.error('Errore nella richiesta fetch:', error);
                showStatusMessage('Errore di comunicazione con il server.', 'error');
            });
        }
    }

    // Script di ricerca (invariato)
    document.getElementById("searchInput").addEventListener("input", function() {
        const filter = this.value.toUpperCase();
        const table = document.querySelector("table");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            tr[i].style.display = "none"; // Nasconde la riga di default
            const td = tr[i].getElementsByTagName("td");
            for (let j = 1; j < td.length; j++) { // Cerca in tutte le celle tranne la prima
                if (td[j]) {
                    if (td[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ""; // Mostra la riga se trova una corrispondenza
                        break; // Passa alla riga successiva
                    }
                }
            }
        }
    });
</script>

</body>
</html>