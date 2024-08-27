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

<h1>Elenco dei file PDF nella sottocartella "SDS"</h1>

<!-- Casella di ricerca -->
<form method="GET" action="">
    <input type="text" name="search" placeholder="Cerca per nome del file..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <input type="submit" value="Cerca">
</form>

<?php
$baseDir = 'SDS'; // Modifica la cartella base a 'SDS'

// Filtraggio dei file in base alla ricerca
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filteredFiles = [];
if (!empty($searchTerm)) {
    $allFiles = glob($baseDir . '/*.pdf'); // Trova tutti i file PDF nella cartella 'ST'
    foreach ($allFiles as $file) {
        if (strpos(strtolower(basename($file)), strtolower($searchTerm)) !== false) {
            $filteredFiles[] = $file;
        }
    }
} else {
    $filteredFiles = glob($baseDir . '/*.pdf'); // Trova tutti i file PDF nella cartella 'ST'
}

// Stampa i file PDF nella tabella
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Schede Tecniche</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($filteredFiles as $file) {
    echo '<tr>';
    echo '<td>';
    echo '<a href="' . $file . '" class="file-link">' . basename($file) . '</a>';
    echo '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
?>

</body>
</html>
