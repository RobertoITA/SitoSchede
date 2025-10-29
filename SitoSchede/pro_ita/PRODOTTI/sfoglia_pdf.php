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
    }
    th, td {
        border: 1px solid white;
        padding: 8px;
    }
    th {
        background-color: #333;
        color: white;
        width: 0px; 
    }
    tr:nth-child(even) {
        background-color: #444;
    }
    tr:nth-child(odd) {
        background-color: #555;
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
    }
    .file-icon {
        width: 24px;
        height: 24px;
        margin-left: 5px;
    }
    /* Stili per i messaggi di stato */
    .status-message {
        padding: 10px; 
        margin-bottom: 20px; 
        border-radius: 5px; 
        color: black; 
        font-weight: bold;
    }
    .status-success {
        background-color: #d4edda; border: 1px solid #c3e6cb;
    }
    .status-error {
        background-color: #f8d7da; border: 1px solid #f5c6cb;
    }
</style>
</head>
<body>

<?php
// Blocco di gestione messaggi
if (isset($_GET['status']) && isset($_GET['msg'])) {
    $status = htmlspecialchars($_GET['status']);
    $message = htmlspecialchars($_GET['msg']);
    
    $class = ($status === 'success') ? 'status-success' : 'status-error';
    
    echo "<div class=\"status-message $class\">$message</div>";
}
?>

<h2>Elenco dei file PDF</h2>
<h2>SCHEDE di Sicurezza - SDS </h2>
<h2>SCHEDE TECNICHE - ST </h2>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca per nome del file...">

<table>
    <thead>
        <tr>
            <th>SCHEDA NUMERO</th>
            <th>Schede Tecniche ST</th>
            <th>Schede di sicurezza SDS</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Percorso della directory contenente le sottocartelle
        $dir_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';

        // Scandisci la directory per trovare le sottocartelle con file PDF
        $subdirs = array_filter(glob($dir_path . '*'), 'is_dir');
        foreach ($subdirs as $subdir) {
            $subdir_name = basename($subdir);
            $pdf_files = glob($subdir . '/*.pdf');
            if (!empty($pdf_files)) {
                $pdf_files_st = array_filter($pdf_files, function($file) {
                    return strpos(basename($file), 'ST -') === 0;
                });
                $pdf_files_sds = array_filter($pdf_files, function($file) {
                    return strpos(basename($file), 'SDS -') === 0;
                });
                echo "<tr>";
                echo "<td>$subdir_name</td>";
                echo "<td>";
                foreach ($pdf_files_st as $pdf_file) {
                    $pdf_file_name = basename($pdf_file);
                    $pdf_file_path = $subdir_name . '/' . $pdf_file_name;
                    $server_filepath = $dir_path . $subdir_name . '/' . $pdf_file_name;
                    $encoded_server_filepath = urlencode($server_filepath);
                    $encoded_file_name = urlencode($pdf_file_name);
                    
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    
                    // NUOVO LINK EMAIL che chiama lo script PHP intermedio
                    echo "<a href=\"javascript:void(0);\" 
                            onclick=\"var recipient = prompt('Inserisci l\'indirizzo email del destinatario:'); 
                                     if (recipient) { 
                                         window.location.href = 'invia_email_server.php?path=$encoded_server_filepath&filename=$encoded_file_name&to=' + encodeURIComponent(recipient); 
                                     }\">
                            <img src='em.png' class='file-icon' alt='Invia per email'>
                          </a>";
                    
                    // Link WhatsApp (rimane invariato)
                    echo "<a href='whatsapp://send?text=Invio%20del%20file%20$pdf_file_name%20via%20WhatsApp%20al%20numero%20di%20telefono'><img src='wa.ico' class='file-icon' alt='Invia su WhatsApp'></a><br>";
                }
                echo "</td>";
                echo "<td>";
                foreach ($pdf_files_sds as $pdf_file) {
                    $pdf_file_name = basename($pdf_file);
                    $pdf_file_path = $subdir_name . '/' . $pdf_file_name;
                    $server_filepath = $dir_path . $subdir_name . '/' . $pdf_file_name;
                    $encoded_server_filepath = urlencode($server_filepath);
                    $encoded_file_name = urlencode($pdf_file_name);
                    
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    
                    // NUOVO LINK EMAIL che chiama lo script PHP intermedio
                    echo "<a href=\"javascript:void(0);\" 
                            onclick=\"var recipient = prompt('Inserisci l\'indirizzo email del destinatario:'); 
                                     if (recipient) { 
                                         window.location.href = 'invia_email_server.php?path=$encoded_server_filepath&filename=$encoded_file_name&to=' + encodeURIComponent(recipient); 
                                     }\">
                            <img src='em.png' class='file-icon' alt='Invia per email'>
                          </a>";

                    // Link WhatsApp (rimane invariato)
                    echo "<a href='whatsapp://send?text=Invio%20del%20file%20$pdf_file_name%20via%20WhatsApp%20al%20numero%20di%20telefono'><img src='wa.ico' class='file-icon' alt='Invia su WhatsApp'></a><br>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>

<script>
    // Script di ricerca invariato
    document.getElementById("searchInput").addEventListener("input", function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) { // Inizio da 1 per saltare l'intestazione
            td = tr[i].getElementsByTagName("td");
            // Controlla tutte le colonne (escludendo la prima che è il numero scheda)
            for (var j = 1; j < td.length; j++) { 
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    } 
                }
            }
            // Se nessuna colonna ha trovato il filtro e il ciclo è terminato
            if (j === td.length) {
                tr[i].style.display = "none";
            }
        }
    });
</script>

</body>
</html>