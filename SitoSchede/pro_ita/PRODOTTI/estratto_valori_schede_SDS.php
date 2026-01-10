<?php
// Configurazione file JSON
$jsonFile = 'riepilogo_sds.json';
$data = [];
$ultimoAggiornamento = "N/D";

// Lettura del JSON
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    
    // Data ultima modifica del file
    $ultimoAggiornamento = date("d/m/Y H:i", filemtime($jsonFile));
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riepilogo Schede SDS</title>
    <style>
        /* --- STILE DARK MODERN --- */
        :root {
            --bg-color: #121212;
            --surface-color: #1e1e1e;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0;
            --accent-color: #00adb5; /* Un bel verde acqua moderno */
            --border-color: #333;
            --hover-row: #2c2c2c;
            --code-bg: #2d2d2d;
            --code-text: #ff9f43;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Intestazione */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        h1 {
            color: var(--accent-color);
            font-weight: 300;
            margin: 0;
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-info {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        /* Tabella */
        .table-wrapper {
            overflow-x: auto; /* Per schermi piccoli */
            background-color: var(--surface-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        thead {
            background-color: #252525;
            border-bottom: 2px solid var(--accent-color);
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: var(--hover-row);
            color: #fff;
            transition: background-color 0.2s;
        }

        /* Badge per i codici pericolo */
        .badge {
            display: inline-block;
            background-color: var(--code-bg);
            color: var(--code-text);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-family: monospace;
            margin-right: 4px;
            margin-bottom: 2px;
            border: 1px solid #444;
        }

        /* Link PDF */
        a.pdf-link {
            text-decoration: none;
            color: var(--text-primary);
            background-color: var(--accent-color);
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
            transition: opacity 0.2s;
            color: #121212; /* Testo scuro su sfondo accent */
        }

        a.pdf-link:hover {
            opacity: 0.8;
        }

        /* Utility per il layout */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { color: #fff; font-weight: 500; }

    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Estratto Dati SDS</h1>
        <div class="meta-info">
            Totale Prodotti: <strong><?php echo count($data); ?></strong> | 
            Aggiornato il: <?php echo $ultimoAggiornamento; ?>
        </div>
    </header>

    <?php if (empty($data)): ?>
        <div style="text-align:center; padding: 40px; background: var(--surface-color); border-radius: 8px;">
            <h3 style="color: #ff6b6b">Nessun dato disponibile</h3>
            <p>Il file <em>riepilogo_sds.json</em> non è stato trovato o è vuoto.<br>
            Assicurati di aver eseguito lo script Python.</p>
        </div>
    <?php else: ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="5%">N.</th>
                        <th width="20%">Prodotto</th>
                        <th width="25%">Etichettatura (Codici)</th>
                        <th width="10%">Peso Spec.<br><small>(g/cm³)</small></th>
                        <th width="10%">VOC<br><small>(g/L)</small></th>
                        <th width="10%">Lim. VOC</th>
                        <th width="15%">Particelle</th>
                        <th width="5%" class="text-center">PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <!-- Numero Scheda (Cartella) -->
                            <td class="text-secondary"><?php echo htmlspecialchars($row['Numero Scheda']); ?></td>
                            
                            <!-- Nome Prodotto -->
                            <td class="font-bold">
                                <?php echo htmlspecialchars($row['Nome Prodotto']); ?>
                                <br>
                                <small style="font-size:0.75em; opacity:0.6;"><?php echo htmlspecialchars($row['Nome File']); ?></small>
                            </td>

                            <!-- Elementi Etichetta (Formattati come badge) -->
                            <td>
                                <?php 
                                    $codici = explode(',', $row['Elementi Etichetta']);
                                    foreach ($codici as $codice) {
                                        $codice = trim($codice);
                                        if(!empty($codice) && $codice !== "Nessun codice rilevato") {
                                            echo '<span class="badge">' . htmlspecialchars($codice) . '</span>';
                                        } elseif ($codice === "Nessun codice rilevato" || $codice === "Non classificato pericoloso") {
                                            echo '<span style="color:#4caf50; font-size:0.85rem;">' . htmlspecialchars($codice) . '</span>';
                                        }
                                    }
                                ?>
                            </td>

                            <!-- Peso Specifico -->
                            <td><?php echo htmlspecialchars($row['Peso Specifico (g/cm3)']); ?></td>

                            <!-- VOC -->
                            <td><?php echo htmlspecialchars($row['VOC Espressi']); ?></td>

                            <!-- Limite VOC -->
                            <td><?php echo htmlspecialchars($row['Limite Massimo VOC']); ?></td>
                            
                            <!-- Particelle -->
                            <td><?php echo htmlspecialchars($row['Caratteristiche Particelle']); ?></td>

                            <!-- Link al PDF -->
                            <td class="text-center">
                                <?php 
                                    // Costruiamo il percorso relativo: ./001/SDS - NOMEFILE.pdf
                                    $linkPdf = "./" . $row['Numero Scheda'] . "/" . $row['Nome File'];
                                ?>
                                <a href="<?php echo $linkPdf; ?>" class="pdf-link" target="_blank">APRI</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

</body>
</html>
