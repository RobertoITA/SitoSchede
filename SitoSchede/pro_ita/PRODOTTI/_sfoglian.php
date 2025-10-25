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

<h1>Elenco dei file PDF</h1>

<!-- Casella di ricerca -->
<form method="GET" action="">
    <input type="text" name="search" placeholder="Cerca per nome del file..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <input type="submit" value="Cerca">
</form>

<?php
$baseDir = '.'; // Directory corrente

// Filtraggio dei file in base alla ricerca
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$filteredFiles = [];
if (!empty($searchTerm)) {
    $allFiles = glob($baseDir . '/**/*.pdf'); // Trova tutti i file PDF nelle sottodirectory
    foreach ($allFiles as $file) {
        if (strpos(strtolower(basename($file)), strtolower($searchTerm)) !== false) {
            $filteredFiles[] = $file;
        }
    }
} else {
    $filteredFiles = glob($baseDir . '/**/*.pdf'); // Trova tutti i file PDF nelle sottodirectory
}

foreach ($filteredFiles as $file) {
    // Ottieni il numero della scheda dalla struttura della directory
    $schedaNum = substr(dirname($file), strrpos(dirname($file), '/') + 1);

    // Stampa il numero della scheda e il nome del file
    echo '<h2>Scheda numero ' . $schedaNum . '</h2>';
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Schede Tecniche</th>';
    echo '<th>Schede di Sicurezza</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Cerca i file PDF per le schede tecniche e di sicurezza
    $pdfFilesST = glob(dirname($file) . '/ST -*.pdf');
    $pdfFilesSDS = glob(dirname($file) . '/SDS -*.pdf');

    $maxRows = max(count($pdfFilesST), count($pdfFilesSDS));

    // Stampa i file PDF trovati nelle colonne corrispondenti
    for ($i = 0; $i < $maxRows; $i++) {
        echo '<tr>';
        echo '<td>';
        if (isset($pdfFilesST[$i])) {
            echo '<a href="' . $pdfFilesST[$i] . '" class="file-link">' . basename($pdfFilesST[$i]) . '</a>';
        }
        echo '</td>';
        echo '<td>';
        if (isset($pdfFilesSDS[$i])) {
            echo '<a href="' . $pdfFilesSDS[$i] . '" class="file-link">' . basename($pdfFilesSDS[$i]) . '</a>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
?>

</body>
</html>

