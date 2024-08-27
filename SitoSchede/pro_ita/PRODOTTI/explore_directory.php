<?php
// Verifica se è stato fornito un parametro 'dir'
if (!isset($_GET['dir'])) {
    die("Nessuna directory specificata.");
}

$dir = $_GET['dir'];
$base_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
$full_path = $base_path . $dir;

// Verifica che la directory esista e sia all'interno del percorso base consentito
if (!is_dir($full_path) || strpos(realpath($full_path), realpath($base_path)) !== 0) {
    die("Directory non valida.");
}

// Funzione per ottenere l'icona del file
function getFileIcon($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    switch ($ext) {
        case 'pdf':
            return '📄';
        case 'php':
        case 'html':
            return '🌐';
        default:
            return '📁';
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esplora Directory: <?php echo htmlspecialchars($dir); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: white;
            padding: 20px;
        }
        a {
            color: #00BFFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Contenuto della directory: <?php echo htmlspecialchars($dir); ?></h1>
    <ul>
    <?php
    $files = scandir($full_path);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $icon = getFileIcon($file);
            echo "<li>$icon <a href='/SitoSchede/pro_ita/PRODOTTI/$dir/$file' target='_blank'>" . htmlspecialchars($file) . "</a></li>";
        }
    }
    ?>
    </ul>
</body>
</html>