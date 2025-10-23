<?php
// Funzione helper per pulire le chiavi in caso di fallback PHP
function clean_key($string) {
    // Rimuove caratteri di controllo (inclusi BOM) e spazi anomali
    return trim(preg_replace('/[[:cntrl:]\x00-\x1F\x7F-\xFF]/', '', $string));
}

// Funzione helper per pulire i valori finali
function clean_final_value($string) {
    // Rimuove spazi anomali all'inizio e delimitatori residui (come ';' o ':')
    $cleaned = ltrim($string, ' ;:');
    return trim($cleaned);
}

// Percorso dello script Python
$python_script_path = '/var/www/html/SitoSchede/parser.py';

// Esegui il script Python e cattura l'output JSON e gli errori
// 2>&1 reindirizza stderr (errori) a stdout (output standard)
$json_output = shell_exec('python3 ' . escapeshellarg($python_script_path) . ' 2>&1');
$products = json_decode($json_output, true);

// Se ci sono errori, prova a leggere direttamente i file con PHP come fallback
if (!$products || json_last_error() !== JSON_ERROR_NONE) {
    // Codice di debug per mostrare il problema di esecuzione Python
    $error_msg = "ERRORE di esecuzione Python (o di decodifica JSON).";
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error_msg .= " Codice errore JSON: " . json_last_error();
    } else {
         // Se non c'è errore JSON, l'output grezzo contiene probabilmente messaggi di errore della shell o Python
         $error_msg .= " Output grezzo Python (potrebbe contenere errori di shell/esecuzione):\n" . htmlspecialchars($json_output);
    }
    
    // In un ambiente di produzione, si dovrebbe solo loggare l'errore
    // Qui viene mostrato per il tuo debug
    // echo "<pre style='color:red; background:#ffe0e0; padding:10px;'>PYTHON EXECUTION FAILURE:\n" . $error_msg . "</pre>";

    // Utilizza il fallback PHP se l'esecuzione Python fallisce
    $products = parseWithPhpFallback();
}

function parseWithPhpFallback() {
    $base_dir = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
    $products = [];
    
    $dirs = glob($base_dir . '*', GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
        $scheda_file = $dir . '/SCHEDA.csv';
        if (file_exists($scheda_file)) {
            $content = file_get_contents($scheda_file);
            // Normalizza i newline
            $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
            
            $product_data = [
                'SCHEDA_NUMERO' => clean_key($lines[0] ?? ''),
                'LOGO_FILENAME' => clean_key($lines[1] ?? ''),
                'NOME_PRODOTTO' => clean_key($lines[2] ?? ''),
                'IMMAGINE_FILENAME' => clean_key($lines[3] ?? ''),
                'dettagli' => []
            ];
            
            $current_key = '';
            // Inizia il parsing dalla riga 5 (indice 4)
            for ($i = 4; $i < count($lines); $i++) {
                $line = $lines[$i]; // Non fare trim() qui, ci pensa la logica
                
                if (strpos($line, ';;') !== false) {
                    // È una nuova chiave
                    $parts = explode(';;', $line, 2);
                    $new_key = clean_key($parts[0]);
                    
                    if (!empty($new_key)) {
                        $current_key = $new_key;
                        $product_data['dettagli'][$current_key] = clean_final_value($parts[1] ?? '');
                    }
                } elseif (!empty($current_key) && trim($line) !== '') {
                    // È una continuazione del valore (multiriga)
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

// Se per qualche motivo $products è vuoto (es. errore di autorizzazione/file not found)
if (empty($products)) {
    // Questo è un messaggio di errore generico da mostrare se il debug fallisce e i dati non vengono caricati
    // e la variabile products è vuota
    // echo "<p style='color:red;'>ERRORE FATALE: Nessun dato prodotto è stato caricato. Controllare i permessi del file Python e i log del server.</p>";
}

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
        th { background: #2c3e50; color: white; }
        .search-box { padding: 10px; width: 300px; margin: 10px 0; }
        
        /* Stile per l'intestazione flottante */
        th { 
            position: sticky; 
            top: 0; 
            z-index: 10; 
        }
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
                        <strong><?= htmlspecialchars($product['NOME_PRODOTTO']) ?></strong><br>
                        <small>Scheda: <?= htmlspecialchars($product['SCHEDA_NUMERO']) ?></small>
                        <!-- Aggiungere qui la visualizzazione dell'immagine e il link alla cartella se necessario -->
                    </td>
                    <?php foreach ($headers as $header): ?>
                        <?php 
                            // Pulisci il valore prima di renderlo
                            $value = clean_final_value($product['dettagli'][$header] ?? '');
                        ?>
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