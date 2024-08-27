<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Esploratore di File</title>
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
    .folder-link {
        color: white;
        text-decoration: underline;
        cursor: pointer;
    }
    .folder-link:hover {
        color: yellow;
    }
    .search-box {
        margin-bottom: 20px;
    }
</style>
</head>
<body>

<h2>Esploratore di File</h2>

<table>
    <thead>
        <tr>
            <th>Nome Cartella/File</th>
            <th>Contenuto PDF</th>
        </tr>
    </thead>
    <tbody>
        <?php
        function scanDirectory($dir) {
            $folders = glob("$dir/*", GLOB_ONLYDIR);
            foreach ($folders as $folder) {
                echo "<tr>";
                echo "<td class='folder-link' onclick='showFolderContent(\"$folder\")'>" . basename($folder) . "</td>";
                echo "<td></td>"; // Placeholder per i file PDF, verranno riempiti tramite JavaScript
                echo "</tr>";
            }
        }

        // Funzione per ottenere i file PDF all'interno di una cartella
        function getPDFFiles($dir) {
            $pdfFiles = glob("$dir/*.pdf");
            foreach ($pdfFiles as $pdfFile) {
                echo "<a href='$pdfFile' class='file-link' target='_blank'>" . basename($pdfFile) . "</a><br>";
            }
        }

        // Percorso della directory corrente del file PHP
        $currentDir = dirname(__FILE__);

        // Scansione delle sottodirectory
        scanDirectory($currentDir);
        ?>
    </tbody>
</table>

<script>
    function showFolderContent(folder) {
        var folderName = folder.split('/').pop();
        var folderCell = event.target;
        var row = folderCell.parentElement;
        var contentCell = row.cells[1];
        
        // Chiamata AJAX per ottenere il contenuto della cartella
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                contentCell.innerHTML = xhr.responseText;
            }
        };
        xhr.open("GET", "get_folder_content.php?folder=" + encodeURIComponent(folder), true);
        xhr.send();
    }
</script>

</body>
</html>
