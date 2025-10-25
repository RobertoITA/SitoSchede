<?php
// Imposta l'header HTTP per forzare la codifica UTF-8 del contenuto.
header('Content-Type: text/html; charset=utf-8');

// Percorsi di file system
$base_dir = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';
$riepilogo_file = $base_dir . 'riepilogo.json';
// Percorso web-accessibile (deve essere corretto in base alla configurazione del server web)
$web_base_path = '/SitoSchede/pro_ita/PRODOTTI/';

// CORREZIONE PERCORSO PYTHON: Usa $base_dir per puntare correttamente al parser.py
$parser_path = $base_dir . 'parser.py'; 

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

// Logica per l'aggiornamento dei dati
$update_message = '';
if (isset($_POST['update_data'])) {
    if (file_exists($parser_path) && is_executable($parser_path)) {
        // Esecuzione sincrona per catturare l'output di errore
        $command = "python3 " . escapeshellarg($parser_path) . " 2>&1";
        $output = shell_exec($command);
        
        // Verifica se il file JSON è stato creato/aggiornato con successo
        clearstatcache(); 
        if (file_exists($riepilogo_file) && filesize($riepilogo_file) > 0) {
            $update_message = "Dati aggiornati con successo.";
        } else {
            $error_detail = empty(trim($output)) ? "Impossibile creare/scrivere 'riepilogo.json'. Controlla i permessi di scrittura sulla directory PRODOTTI/." : nl2br(htmlspecialchars($output));
            $update_message = "Errore durante l'aggiornamento dei dati: " . $error_detail;
        }
    } else {
        $update_message = "Errore: File 'parser.py' non trovato o non eseguibile. Controlla il percorso e i permessi.";
    }
}

// Prova a leggere il file JSON
$products = [];
if (file_exists($riepilogo_file) && is_readable($riepilogo_file)) {
    $json_raw = file_get_contents($riepilogo_file);
    $products = json_decode($json_raw, true);
    if ($products === null || json_last_error() !== JSON_ERROR_NONE) {
        $products = [];
    }
}

// Headers per la tabella. 
$headers = [
    'DESCRIZIONE', 'ASPETTO', 'PESO SPECIFICO', 'RESIDUO SECCO',
    'PERMEABILITA', 'PRESA DI SPORCO', "LAVABILITA'",
    'COLORE', 'ESSICCAZIONE', 'RESA PRATICA', 'DILUIZIONE',
    'ATTREZZI', 'SUPPORTI', 'CODICE ARTICOLO'
];

// Mapping per la colonna 'PERMEABILITA'
$header_keys_map = [
    'PERMEABILITA' => 'PERMEABILITA AL VAPORE ACQUEO'
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>TABELLA PRODOTTI</title>
    <style>
        /* COLORI CHIARI E MODERNI */
        body { 
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: #f0f4f8; /* Light Blue/Gray Background */
            color: #333333; /* Dark text */
        }
        h1 { 
            color: #1a73e8; /* Accent Blue */
            font-size: 1.8em;
            margin: 0;
            padding: 0;
        }
        
        /* Contenitore principale per forzare lo scroll orizzontale se necessario */
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px; /* Bordo leggermente arrotondato */
        }

        .products-table { 
            min-width: 100%; 
            border-collapse: collapse; 
            table-layout: auto; 
            background-color: #ffffff; /* White Base Table Background */
        }
        
        /* Stili Intestazione (TH) - Resi sticky e TRASPARENTI */
        .products-table th { 
            background: rgba(220, 230, 240, 0.95); /* Very Light Blue/Gray semi-trasparente */
            backdrop-filter: blur(2px); 
            color: #1a73e8; /* Accent Blue per l'intestazione */
            position: sticky; 
            top: 0; 
            z-index: 10; 
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #c8d1da;
            padding: 10px;
            vertical-align: middle; 
            transition: background-color 0.1s; 
        }
        .products-table th:hover {
            background: rgba(220, 230, 240, 1);
        }
        
        /* RIGHE ALTERNATE */
        .products-table tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Bianco per dispari */
        }
        .products-table tbody tr:nth-child(even) {
            background-color: #f5f9fc; /* Grigio chiarissimo per pari */
        }

        .products-table td {
            border: 1px solid #c8d1da;
            padding: 10px; 
            text-align: left; 
            vertical-align: top;
            font-size: 0.9em;
            height: 150px; 
            box-sizing: border-box;
        }
        
        /* Controlli Header */
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-box { 
            padding: 10px; 
            width: 300px; 
            border: 1px solid #c8d1da; 
            border-radius: 6px;
            background-color: #ffffff;
            color: #333333;
        }
        /* Stili per i pulsanti */
        .right-controls {
            display: flex;
            gap: 10px; 
        }
        .control-btn {
            background-color: #1a73e8; /* Accent Blue */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s, transform 0.1s;
            text-decoration: none; 
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .control-btn:hover {
            background-color: #4285f4;
            transform: translateY(-1px);
        }
        .update-message {
            position: fixed; 
            bottom: 20px;
            right: 20px;
            z-index: 100;
            padding: 10px 15px;
            border-radius: 6px;
            background-color: #0f9d58; /* Green for success */
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
        }

        /* Larghezza Fissa solo per la colonna PRODOTTO */
        .col-prodotto { width: 120px; min-width: 120px; } 
        
        .product-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            justify-content: center;
            padding: 5px;
        }
        .product-info img {
            max-height: 50px; 
            margin-bottom: 5px; 
            display: block; 
            vertical-align: middle;
            background-color: #f0f4f8; /* Sfondo chiaro per immagini */
            padding: 2px;
            border-radius: 4px;
        }
        .product-info strong, .product-info small {
            display: block; 
            color: #333333; /* Testo scuro */
            white-space: normal; 
            word-wrap: break-word; 
        }
        
        /* Larghezze indicative per le colonne (non percentuali rigide) */
        .col-descrizione { min-width: 300px; max-width: 400px; } 
        .col-diluizione { min-width: 250px; }  
        .col-codice-articolo { width: 100px; min-width: 100px; } 

        /* ROTAZIONE A 90° e MAX COMPATTAMENTO (Header) */
        .rotated-header-90 {
            width: 30px !important; 
            padding: 0;
            margin: 0;
            height: 150px; 
            position: relative; 
            overflow: visible; 
            z-index: 11; 
        }
        .rotated-header-90 > div {
            position: absolute;
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%) rotate(-90deg); 
            
            width: 150px; 
            padding: 0;
            text-align: center; 
            white-space: nowrap;
        }
        
        /* ROTAZIONE A 90° e MAX COMPATTAMENTO (Dati) */
        .rotated-cell {
            width: 30px !important; 
            padding: 0; 
            vertical-align: middle;
            text-align: center;
            position: relative;
        }

        .rotated-cell > div {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150px; 
            line-height: 1.2;
            padding: 0;
            margin: 0;
            white-space: nowrap;
            text-align: center;
            transform: translate(-50%, -50%) rotate(-90deg);
        }
    </style>
</head>
<body>
    <div class="header-controls">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Cerca...">
        <h1>📊 TABELLA RIEPILOGATIVA PRODOTTI</h1>
        
        <div class="right-controls">
            <form method="post" style="margin: 0;">
                <button type="submit" name="update_data" class="control-btn update-btn">Aggiorna i dati</button>
            </form>
            
            <!-- PULSANTE con Data/Ora e reindirizzamento -->
            <a href="http://schede/SitoSchede/pro_ita/PRODOTTI/tutto.html" id="currentDateBtn" class="control-btn">
                <span id="dateTime"></span>
            </a>
        </div>
    </div>

    <?php if (!empty($update_message)): ?>
        <div class="update-message"><?= $update_message ?></div>
    <?php endif; ?>

<div class="table-container">
    <table class="products-table">
        <thead>
            <tr>
                <th class="col-prodotto">PRODOTTO</th>
                <?php 
                $rotated_headers = ['ASPETTO', 'PESO SPECIFICO', 'RESIDUO SECCO', 'PERMEABILITA', 'PRESA DI SPORCO', "LAVABILITA'"];
                foreach ($headers as $header): 
                    $class = 'col-normale';
                    if ($header == 'DESCRIZIONE') $class = 'col-descrizione';
                    else if ($header == 'DILUIZIONE') $class = 'col-diluizione';
                    else if ($header == 'CODICE ARTICOLO') $class = 'col-codice-articolo'; 
                    
                    if (in_array($header, $rotated_headers)):
                ?>
                        <th class="rotated-header-90"><div><?= htmlspecialchars($header) ?></div></th>
                <?php else: ?>
                        <th class="<?= $class ?>"><?= htmlspecialchars($header) ?></th>
                <?php endif; endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $dir => $product): ?>
                <tr>
                    <td class="product-info col-prodotto">
                        <?php 
                        $logo_filename = htmlspecialchars($product['LOGO_FILENAME'] ?? '');
                        $img_filename = htmlspecialchars($product['IMMAGINE_FILENAME'] ?? '');
                        $product_dir = htmlspecialchars($dir);
                        
                        // Logica per mostrare logo E/O immagine
                        if (!empty($logo_filename)) {
                            $logo_path = $web_base_path . $product_dir . '/' . $logo_filename;
                            echo '<img src="' . $logo_path . '" alt="Logo Prodotto">';
                        }
                        if (!empty($img_filename)) {
                            $img_path = $web_base_path . $product_dir . '/' . $img_filename;
                            // Mostra l'immagine, a meno che non sia esattamente lo stesso nome del logo
                            if (empty($logo_filename) || $logo_filename != $img_filename) {
                                echo '<img src="' . $img_path . '" alt="Immagine Prodotto">';
                            }
                        }
                        ?>
                        <strong><?= htmlspecialchars($product['NOME_PRODOTTO'] ?? '') ?></strong>
                        <small>Scheda: <?= htmlspecialchars($product['SCHEDA_NUMERO'] ?? '') ?></small>
                    </td>
                    <?php foreach ($headers as $header): ?>
                        <?php 
                            $key_in_data = $header_keys_map[$header] ?? $header;
                            $value = clean_final_value($product['dettagli'][$key_in_data] ?? ''); 
                            $class = 'col-normale';
                            if ($header == 'DESCRIZIONE') $class = 'col-descrizione';
                            else if ($header == 'DILUIZIONE') $class = 'col-diluizione';
                            else if ($header == 'CODICE ARTICOLO') $class = 'col-codice-articolo';
                            
                            // LOGICA DI TRASFORMAZIONE PER CODICE ARTICOLO
                            if ($header == 'CODICE ARTICOLO' && strpos($value, ';') !== false) {
                                $value = str_replace(';', "\n", $value);
                            }
                        ?>
                        <?php if (in_array($header, $rotated_headers)): ?>
                             <td class="rotated-cell">
                                <div><?= nl2br(htmlspecialchars($value)) ?></div>
                             </td>
                        <?php else: ?>
                            <td class="<?= $class ?>">
                                <?= nl2br(htmlspecialchars($value)) ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Funzione per aggiornare l'ora e la data nel pulsante
    function updateDateTime() {
        const now = new Date();
        const dateOptions = { day: '2-digit', month: '2-digit', year: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
        const dateStr = now.toLocaleDateString('it-IT', dateOptions);
        const timeStr = now.toLocaleTimeString('it-IT', timeOptions);
        
        document.getElementById('dateTime').textContent = `${dateStr} ${timeStr}`;
    }

    // Aggiorna subito e poi ogni secondo
    updateDateTime();
    setInterval(updateDateTime, 1000); 

    // Logica di ricerca
    document.getElementById("searchInput").addEventListener("input", function(e) {
        const filter = e.target.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Messaggio di aggiornamento
    const updateMessage = document.querySelector('.update-message');
    if(updateMessage) {
        setTimeout(() => {
            updateMessage.style.display = 'none';
        }, 5000);
    }
</script>
</body>
</html>