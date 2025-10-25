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
        width: 50px; /* Modifica per la larghezza fissa della colonna */
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
</style>
</head>
<body>

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
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    echo "<button class='file-action' onclick='sendEmail(\"$pdf_file_path\")'><img src='em.png' class='file-icon' alt='Invia per email'></button>";
                    echo "<button class='file-action' onclick='sendWhatsApp(\"$pdf_file_path\")'><img src='wa.ico' class='file-icon' alt='Invia su WhatsApp'></button><br>";
                }
                echo "</td>";
                echo "<td>";
                foreach ($pdf_files_sds as $pdf_file) {
                    $pdf_file_name = basename($pdf_file);
                    $pdf_file_path = $subdir_name . '/' . $pdf_file_name;
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a>";
                    echo "<button class='file-action' onclick='sendEmail(\"$pdf_file_path\")'><img src='em.png' class='file-icon' alt='Invia per email'></button>";
                    echo "<button class='file-action' onclick='sendWhatsApp(\"$pdf_file_path\")'><img src='wa.ico' class='file-icon' alt='Invia su WhatsApp'></button><br>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
        
    </tbody>
</table>

<script>
    document.getElementById("searchInput").addEventListener("input", function() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td");
            for (var j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    });

    function sendEmail(filePath) {
    // Indirizzo email del destinatario
    var recipientEmail = "roberto@italmont.it";
    // Oggetto dell'email
    var subject = "Invio di un file PDF";
    // Corpo dell'email
    var body = "Ciao,\n\nTi sto inviando un file PDF. Controlla l'allegato.\n\nGrazie.";

    // Costruiamo l'URL mailto
    var mailtoLink = "mailto:" + recipientEmail + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);

    // Apriamo il link mailto in una nuova finestra
    window.open(mailtoLink);

    // Mostra un messaggio di conferma
    alert("Email inviata per il file: " + filePath);
}


    function sendWhatsApp(filePath) {
        // Aggiungi qui la logica per l'invio di WhatsApp con JavaScript
        alert("Invio WhatsApp per il file: " + filePath);
    }
</script>

</body>
</html>
