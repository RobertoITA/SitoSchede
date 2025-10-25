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
    .action-link {
        color: #0099ff;
        cursor: pointer;
        text-decoration: none;
    }
    .action-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<h1>Elenco dei file PDF nella cartella e sottocartelle</h1>

<!-- Casella di ricerca -->
<form method="GET" action="">
    <input type="text" name="search" placeholder="Cerca per nome del file..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <input type="submit" value="Cerca">
</form>

<?php
function listFiles($directory, $searchTerm) {
    $files = glob($directory . '/*.pdf');
    foreach ($files as $file) {
        $filename = basename($file);
        // Converti il percorso assoluto in un URL relativo
        $fileUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
        if ((empty($searchTerm) || stripos($filename, $searchTerm) !== false) && strpos($filename, 'SDS -') === 0) {
            echo '<tr>';
            echo '<td>';
            echo '<a href="' . $fileUrl . '" class="file-link">' . $filename . '</a>';
            echo '</td>';
            echo '<td>';
            echo '<a href="' . $fileUrl . '" download class="action-link">Download</a>';
            echo '</td>';
            echo '</tr>';
        }
    }
    $subdirectories = glob($directory . '/*', GLOB_ONLYDIR);
    foreach ($subdirectories as $subdirectory) {
        listFiles($subdirectory, $searchTerm);
    }
}


$baseDir = __DIR__;
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Nome del file</th>';
echo '<th>Azioni</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
listFiles($baseDir, $searchTerm);

echo '</tbody>';
echo '</table>';
?>

</body>
</html>
