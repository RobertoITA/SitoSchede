<?php
// index_csv.php - versione con stile richiesto e intestazioni ruotate

$base_dir = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
$riepilogo_file = $base_dir . 'riepilogo.json';

// Pulizia testi
function clean_key($string) {
    if ($string === null) return '';
    return trim(preg_replace('/[\x00-\x1F\x7F]/', '', $string));
}
function clean_final_value($string) {
    if ($string === null) return '';
    $cleaned = ltrim($string, " ;:");
    return trim($cleaned);
}

// Proviamo a leggere il riepilogo JSON scritto da parser.py
$products = null;
if (file_exists($riepilogo_file) && is_readable($riepilogo_file)) {
    $json_raw = file_get_contents($riepilogo_file);
    $products = json_decode($json_raw, true);
    if ($products === null || json_last_error() !== JSON_ERROR_NONE) {
        $products = null;
    }
}

// Se non esiste o è corrotto, fallback al parsing diretto dei file SCHEDA.csv
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

// Intestazioni della tabella (manteniamo le stesse chiavi usate nella pagina precedente)
$headers = [
    'DESCRIZIONE', 'ASPETTO', 'PESO SPECIFICO', 'RESIDUO SECCO',
    'PERMEABILITA AL VAPORE ACQUEO', 'PRESA DI SPORCO', "LAVABILITA'",
    'COLORE', 'ESSICCAZIONE', 'RESA PRATICA', 'DILUIZIONE',
    'ATTREZZI', 'SUPPORTI', 'CODICE ARTICOLO'
];

// Elenco intestazioni da ruotare (90° anticlockwise)
$rotated_headers = [
    'ASPETTO',
    'PESO SPECIFICO',
    'RESIDUO SECCO',
    'PERMEABILITA AL VAPORE ACQUEO',
    'PRESA DI SPORCO',
    "LAVABILITA'"
];

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Elenco Schede Prodotti</title>
<style>
    /* Body e sfondo come dal file che mi hai fornito */
    :root{
        --bg1: #003000;
        --bg2: #003f00;
        --accent: #630000;
        --header-bg: rgba(1,1,1,0.75);
        --th-border: rgba(255,255,255,0.9);
        --odd: rgba(102,102,101,0.75);
        --even: rgba(64,64,64,0.75);
        --text: #ffffff;
    }
    html,body { height:100%; margin:0; padding:0; }
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background: linear-gradient(to bottom, var(--bg1), var(--bg2) 15%, #FFFFFF 38%, #FFFFFF 62%, var(--accent) 85%, var(--accent));
        color: var(--text);
        text-align: center;
        box-sizing: border-box;
    }

    h1 {
        margin: 10px 0 20px 0;
        color: var(--text);
        text-shadow: 0 1px 2px rgba(0,0,0,0.6);
    }

    .search-box {
        margin-bottom: 20px;
        padding: 8px 12px;
        width: 360px;
        max-width: 90%;
        border-radius: 6px;
        border: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.5);
    }

    .table-wrap {
        width: 100%;
        overflow-x: auto; /* se la tabella è larga */
        background: rgba(0,0,0,0.25);
        padding: 8px;
        border-radius: 6px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px; /* forza layout ampio per colonne ruotate */
    }

    thead {
        position: sticky;
        top: 0;
        z-index: 5;
        background: rgba(102,102,101,1);
    }

    th, td {
        border: 1px solid rgba(255,255,255,0.85);
        padding: 10px;
        vertical-align: middle;
        text-align: left;
        font-size: 13px;
    }

    th {
        background: var(--header-bg);
        color: rgba(255,255,255,0.95);
        white-space: nowrap;
    }

    tbody tr:nth-child(even) { background: var(--even); }
    tbody tr:nth-child(odd)  { background: var(--odd); }

    /* Stile per celle del prodotto (nome e scheda) */
    td.product {
        width: 280px;
    }
    td.product strong { display:block; color:#fff; }
    td.product small { color: #ddd; }

    /* Stile per le intestazioni ruotate */
    th.rotated {
        width: 60px; /* larghezza della colonna */
        padding: 4px;
        text-align: center;
        vertical-align: bottom;
    }

    /* Contenitore interno che verrà ruotato -90deg (anticlockwise) */
    th.rotated > .rot {
        display: inline-block;
        transform: rotate(-90deg);
        transform-origin: left bottom;
        /* centratura e spaziatura del testo ruotato */
        padding: 6px 4px;
        line-height: 1.1;
        font-size: 12px;
    }

    /* Quando l'intestazione contiene <br>, verranno più righe (come richiesto) */
    th.rotated > .rot br { display:block; line-height:1; }

    /* Hoping to keep readability */
    td, th { color: #ffffff; }

    /* responsive: riduci la dimensione del testo su schermi piccoli */
    @media (max-width:800px){
        table { min-width: 900px; }
        th.rotated { width: 48px; }
        th.rotated > .rot { font-size: 11px; padding:4px; }
    }
</style>
</head>
<body>
    <h1>ELENCO DELLE SCHEDE DEI PRODOTTI</h1>

    <input type="text" id="searchInput" class="search-box" placeholder="🔍 Cerca...">

    <div class="table-wrap">
        <table id="productsTable" role="table" aria-label="Tabella prodotti">
            <thead>
                <tr>
                    <th>SCHEDA NUMERO</th>
                    <th>PRODOTTO / DESCRIZIONE</th>

                    <?php
                    // Stampa le intestazioni, ruotate se necessarie
                    foreach ($headers as $header) {
                        if (in_array($header, $rotated_headers)) {
                            // sostituisco gli spazi con <br> in modo che le intestazioni con più parole occupino più righe,
                            // quindi ruotando risultano più compatte verticalmente
                            $label_html = str_replace(' ', '<br>', htmlspecialchars($header));
                            echo "<th class='rotated'><div class='rot'>{$label_html}</div></th>";
                        } else {
                            echo '<th>' . htmlspecialchars($header) . '</th>';
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $dir => $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['SCHEDA_NUMERO'] ?? $dir) ?></td>
                        <td class="product">
                            <strong><?= htmlspecialchars($product['NOME_PRODOTTO'] ?? $dir) ?></strong>
                            <small><?= nl2br(htmlspecialchars($product['dettagli']['DESCRIZIONE'] ?? '')) ?></small>
                        </td>

                        <?php foreach ($headers as $header): ?>
                            <?php
                                $value = $product['dettagli'][$header] ?? '';
                                $value = clean_final_value($value);
                                // Mostra a capo all'interno delle celle
                                $value_html = nl2br(htmlspecialchars($value));
                            ?>
                            <td><?= $value_html ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script>
// semplice filtro client-side (testo completo nella riga)
document.getElementById("searchInput").addEventListener("input", function(e) {
    const filter = e.target.value.toLowerCase();
    const rows = document.querySelectorAll("#productsTable tbody tr");
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
</body>
</html>
