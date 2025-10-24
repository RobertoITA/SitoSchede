<?php
// Percorsi
$base_dir = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
$riepilogo_file = $base_dir . 'riepilogo.json';

// Funzioni helper (pulizia)
function clean_key($string) {
    if ($string === null) return '';
    return trim(preg_replace('/[\x00-\x1F\x7F]/', '', $string));
}
function clean_final_value($string) {
    if ($string === null) return '';
    $cleaned = ltrim($string, " ;:");
    return trim($cleaned);
}

// Prova a leggere il file JSON generato da parser.py
$products = null;
if (file_exists($riepilogo_file) && is_readable($riepilogo_file)) {
    $json_raw = file_get_contents($riepilogo_file);
    $products = json_decode($json_raw, true);
    // Se decode fallisce, forza null per usare fallback
    if ($products === null || json_last_error() !== JSON_ERROR_NONE) {
        $products = null;
    }
}

// Se non esiste o è corrotto, usa il fallback PHP (legge direttamente i SCHEDA.csv)
if (!$products) {
    $products = parseWithPhpFallback($base_dir);
}

function parseWithPhpFallback($base_dir) {
    $products = [];
    $dirs = glob($base_dir . '*', GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        $scheda_file = $dir . '/SCHEDA.csv';
        if (file_exists($scheda_file)) {
            $content = file_get_contents($scheda_file);
            $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
            $lines = array_values(array_filter($lines, function($l){ return trim($l) !== ''; }));

            $product_data = [
                'SCHEDA_NUMERO' => clean_key($lines[0] ?? ''),
                'LOGO_FILENAME' => clean_key($lines[1] ?? ''),
                'NOME_PRODOTTO' => clean_key($lines[2] ?? ''),
                'IMMAGINE_FILENAME' => clean_key($lines[3] ?? ''),
                'dettagli' => []
            ];

            $current_key = '';
            for ($i = 4; $i < count($lines); $i++) {
                $line = $lines[$i];
                if (strpos($line, ';;') !== false) {
                    $parts = explode(';;', $line, 2);
                    $new_key = clean_key($parts[0]);
                    if ($new_key !== '') {
                        $current_key = $new_key;
                        $product_data['dettagli'][$current_key] = clean_final_value($parts[1] ?? '');
                    }
                } elseif ($current_key !== '' && trim($line) !== '') {
                    $product_data['dettagli'][$current_key] .= "\n" . clean_final_value($line);
                }
            }

            $products[basename($dir)] = $product_data;
        }
    }
    return $products;
}

// Headers per la tabella
$headers = [
    'DESCRIZIONE', 'ASPETTO', 'PESO SPECIFICO', 'RESIDUO SECCO',
    'PERMEABILITA AL VAPORE ACQUEO', 'PRESA DI SPORCO', "LAVABILITA'",
    'COLORE', 'ESSICCAZIONE', 'RESA PRATICA', 'DILUIZIONE',
    'ATTREZZI', 'SUPPORTI', 'CODICE ARTICOLO'
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>TABELLA PRODOTTI</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #2c3e50; color: white; position: sticky; top: 0; z-index: 10; }
        .search-box { padding: 10px; width: 300px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>📊 TABELLA RIEPILOGATIVA PRODOTTI</h1>
    <input type="text" id="searchInput" class="search-box" placeholder="🔍 Cerca...">

    <table>
        <thead>
            <tr>
                <th>PRODOTTO</th>
                <?php foreach ($headers as $header): ?>
                    <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $dir => $product): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($product['NOME_PRODOTTO'] ?? '') ?></strong><br>
                        <small>Scheda: <?= htmlspecialchars($product['SCHEDA_NUMERO'] ?? '') ?></small>
                    </td>
                    <?php foreach ($headers as $header): ?>
                        <?php $value = clean_final_value($product['dettagli'][$header] ?? ''); ?>
                        <td><?= nl2br(htmlspecialchars($value)) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        document.getElementById("searchInput").addEventListener("input", function(e) {
            const filter = e.target.value.toLowerCase();
            const rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>
</html>
