<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco dei file PDF e HTML</title>
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
</style>
</head>
<body>

<h1>ELENCO DELLE SCHEDE DEI PRODOTTI ITALMONT</h1>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca per nome del file...">

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
        
        // Filtra i file PDF per Schede Tecniche e Schede di Sicurezza
        $pdf_files_st = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'ST -') === 0;
        });
        $pdf_files_sds = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'SDS -') === 0;
        });
        $php_files_php = array_filter($php_files, function($file) {
            return strpos(basename($file), 'HTML -') === 0;
        });

        // Variabile per la descrizione del prodotto (presente nel file SCHEDA.csv)
        $descrizione = "N/A";
        $csv_file_path = $subdir . '/SCHEDA.csv';

        // Se il file SCHEDA.csv esiste, leggi la descrizione dalla terza riga, prima colonna
        if (file_exists($csv_file_path)) {
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
        }

        if (!empty($pdf_files) || !empty($php_files)) {
            echo "<tr>";
            // Modifica qui: link alla pagina di esplorazione della directory
            echo "<td><a href='explore_directory.php?dir=" . urlencode($subdir_name) . "' class='file-link' target='_blank'>$subdir_name</a></td>";
            // Nuova colonna: descrizione del prodotto
            echo "<td>$descrizione</td>";
            echo "<td>";
            foreach ($pdf_files_st as $pdf_file) {
                // Usa pathinfo per ottenere il nome del file senza estensione e rimuovi il prefisso "ST -"
                $pdf_file_name = pathinfo(basename($pdf_file), PATHINFO_FILENAME);
                $pdf_file_name = str_replace('ST - ', '', $pdf_file_name);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($pdf_file) . "' class='file-link' target='_blank'>$pdf_file_name</a><br>";
            }
            echo "</td>";
            echo "<td>";
            foreach ($pdf_files_sds as $pdf_file) {
                // Usa pathinfo per ottenere il nome del file senza estensione e rimuovi il prefisso "SDS -"
                $pdf_file_name = pathinfo(basename($pdf_file), PATHINFO_FILENAME);
                $pdf_file_name = str_replace('SDS - ', '', $pdf_file_name);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($pdf_file) . "' class='file-link' target='_blank'>$pdf_file_name</a><br>";
            }
            echo "</td>";
            echo "<td>";
            foreach ($php_files_php as $php_file) {
                // Usa pathinfo per ottenere il nome del file senza estensione e rimuovi il prefisso "HTML -"
                $php_file_name = pathinfo(basename($php_file), PATHINFO_FILENAME);
                $php_file_name = str_replace('HTML - ', '', $php_file_name);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/" . basename($php_file) . "' class='file-link' target='_blank'>$php_file_name</a><br>";
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
</script>

</body>
</html>
