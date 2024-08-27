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

<h2>Elenco dei file </h2>
<h2>SCHEDE TECNICHE - ST </h2> <h2>SCHEDE di Sicurezza - SDS </h2> <h2>SCHEDE Prodotti - HTML </h2>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca per nome del file...">

<table>
    <thead>
        <tr>
            <th>SCHEDA NUMERO</th>
            <th>Schede Tecniche ST</th>
            <th>Schede di sicurezza SDS</th>
            <th>Schede Prodotti HTML</th>
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
        
        $pdf_files_st = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'ST -') === 0;
        });
        $pdf_files_sds = array_filter($pdf_files, function($file) {
            return strpos(basename($file), 'SDS -') === 0;
        });
        $php_files_php = array_filter($php_files, function($file) {
            return strpos(basename($file), 'HTML -') === 0;
        });

        if (!empty($pdf_files) || !empty($php_files)) {
            echo "<tr>";
            // Modifica qui: link alla pagina di esplorazione della directory
            echo "<td><a href='explore_directory.php?dir=" . urlencode($subdir_name) . "' class='file-link' target='_blank'>$subdir_name</a></td>";
            echo "<td>";
            foreach ($pdf_files_st as $pdf_file) {
                $pdf_file_name = basename($pdf_file);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a><br>";
            }
            echo "</td>";
            echo "<td>";
            foreach ($pdf_files_sds as $pdf_file) {
                $pdf_file_name = basename($pdf_file);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$pdf_file_name' class='file-link' target='_blank'>$pdf_file_name</a><br>";
            }
            echo "</td>";
            echo "<td>";
            foreach ($php_files_php as $php_file) {
                $php_file_name = basename($php_file);
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$php_file_name' class='file-link' target='_blank'>$php_file_name</a><br>";
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