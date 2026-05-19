<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco Schede Prodotti Italmont</title>
<style>
    /* ===== TEMA CHIARO MINIMALE ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Gestione dello sfondo ruotato a 45 gradi */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #dbe2e8; /* Colore di contrasto per far risaltare il logo bianco */
        color: #2c3e50;
        padding: 20px;
        position: relative;
        overflow-x: hidden;
        min-height: 100vh;
    }

    /* Pseudo-elemento per il pattern ripetuto e ruotato */
    body::before {
        content: "";
        position: fixed;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background-image: url('logo_bianco.gif');
        background-size: 120px 75px;
        background-repeat: repeat;
        transform: rotate(45deg);
        z-index: -1; 
        opacity: 0.6; 
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.97); 
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        position: relative;
        z-index: 10;
    }

    /* ===== HEADER STICKY CON BANDIERA FLUIDA ===== */
    /* Animazione per l'effetto fluido continuo */
    @keyframes fluidFlag {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .header {
        /* Gradiente impostato per far vedere sempre tutti e 3 i colori in sequenza morbida */
        background: linear-gradient(90deg, #009246, #ffffff, #ce2b37, #009246, #ffffff);
        background-size: 250% 100%;
        animation: fluidFlag 12s ease-in-out infinite; /* Movimento continuo automatico */
        padding: 20px 30px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* Stile per i titoli con ombreggiatura */
    .title-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .title-main {
        color: #ffffff;
        font-size: 2.2rem;
        font-weight: 900;
        letter-spacing: 4px;
        line-height: 1.1;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.8);
    }

    .title-sub {
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 1.5px;
        margin-top: 5px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
    }

    .search-box {
        padding: 10px 15px;
        border: 2px solid rgba(0,0,0,0.1);
        border-radius: 25px;
        width: 250px;
        font-size: 14px;
        background: white;
        color: #333;
        outline: none;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .search-box:focus {
        border-color: #009246;
        box-shadow: 0 0 0 3px rgba(0, 146, 70, 0.3);
    }

    /* Gruppo Testo + Pulsante a destra */
    .contact-group {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }

    .contact-text {
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
    }

    .date-button {
        background: rgba(0, 0, 0, 0.6); 
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        transition: background 0.2s, transform 0.2s;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .date-button:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: scale(1.02);
    }

    /* ===== TABELLA ===== */
    .table-wrapper {
        padding: 0 20px 20px 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    th {
        background: #ffffff;
        color: #495057;
        padding: 15px 12px;
        text-align: left;
        font-weight: 700;
        border-bottom: 2px solid #e9ecef;
        position: sticky;
        top: 92px; 
        z-index: 990;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #eef2f6;
        vertical-align: top;
    }

    tr:hover {
        background-color: #f0f4f8; 
    }

    .file-link {
        color: #009246; 
        text-decoration: none;
        display: inline-block;
        margin: 2px 0;
        transition: color 0.2s;
        font-weight: 600;
    }

    .file-link:hover {
        color: #ce2b37; 
        text-decoration: underline;
    }

    /* Colonna numero prodotto */
    td:first-child {
        font-weight: 800;
        color: #333;
        font-size: 1.1em;
    }

    /* Footer */
    .footer {
        background: #f8f9fa;
        padding: 15px 20px;
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 12px 12px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .header {
            flex-direction: column;
            padding: 15px;
            position: static; 
        }
        th {
            position: static; 
        }
        .title-main { font-size: 1.8rem; }
        .title-sub { font-size: 0.8rem; }
        .search-box { width: 100%; order: 3; margin-top: 10px; }
        .title-group { order: 1; margin-bottom: 10px; }
        .contact-group { order: 2; align-items: center; }
    }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 Cerca prodotto...">
        
        <div class="title-group">
            <div class="title-main">ITALMONT SRL</div>
            <div class="title-sub">ELENCO SCHEDE PRODOTTI VERNICIANTI</div>
        </div>

        <div class="contact-group">
            <span class="contact-text">Se ha bisogno di essere contattato clicchi qui.</span>
            <!-- Link inserito sul bottone Data/Ora -->
            <a href="http://www.italmont.it/SDS/italmont/register.php" id="dateButton" class="date-button">📅 Caricamento...</a>
        </div>
    </div>

    <div class="table-wrapper">
        <table id="productsTable">
            <thead>
                <tr>
                    <!-- Colonne ridotte per Codice e Descrizione -->
                    <th width="8%">Codice</th>
                    <th width="27%">Descrizione</th>
                    <th width="32.5%">📄 Scheda Tecnica</th>
                    <th width="32.5%">⚠️ SDS Sicurezza</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Trova la cartella corrente
            $current_dir = dirname(__FILE__);
            $dir_path = $current_dir . '/';
            
            // Cerca cartelle
            $subdirs = array_filter(glob($dir_path . '[0-9][0-9][0-9]'), 'is_dir');
            if (empty($subdirs)) {
                $subdirs = array_filter(glob($dir_path . '*'), 'is_dir');
                $subdirs = array_filter($subdirs, function($d) {
                    $name = basename($d);
                    return !in_array($name, ['assets', 'css', 'js', 'img', 'images', 'old', 'OLD']);
                });
            }
            
            // Ordina numericamente
            sort($subdirs, SORT_NATURAL);
            
            foreach ($subdirs as $subdir) {
                $subdir_name = basename($subdir);
                
                // Cerca solo i file PDF
                $pdf_files = glob($subdir . '/*.pdf');
                
                // Filtra Schede Tecniche (ST)
                $pdf_tecniche = array_filter($pdf_files, function($file) {
                    return stripos(basename($file), 'ST -') === 0 || 
                           stripos(basename($file), 'ST-') === 0 ||
                           stripos(basename($file), 'scheda tecnica') !== false;
                });
                
                // Filtra SDS (Schede di Sicurezza)
                $pdf_sds = array_filter($pdf_files, function($file) {
                    return stripos(basename($file), 'SDS -') === 0 ||
                           stripos(basename($file), 'SDS-') === 0 ||
                           stripos(basename($file), 'sicurezza') !== false;
                });
                
                // LEGGI DESCRIZIONE DA CSV
                $descrizione = ""; // Inizializzato vuoto: se non trova nulla, resta pulito
                $csv_file = $subdir . '/SCHEDA.csv';
                $csv_exists = file_exists($csv_file);
                
                if ($csv_exists) {
                    if (($handle = fopen($csv_file, 'r')) !== false) {
                        $riga_num = 0;
                        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                            $riga_num++;
                            if ($riga_num == 3 && isset($data[0])) {
                                $descrizione = trim($data[0]);
                                break;
                            }
                        }
                        fclose($handle);
                    }
                } 
                // Il blocco "else" per il warning è stato eliminato
                
                // Mostra solo se ci sono file pdf
                if (!empty($pdf_files)) {
                    echo '<tr>';
                    
                    // Colonna Codice (Solo TESTO)
                    echo '<td>' . htmlspecialchars($subdir_name) . '</td>';
                    
                    // Colonna Descrizione
                    echo '<td>' . $descrizione . '</td>';
                    
                    // Colonna Schede Tecniche
                    echo '<td>';
                    if ($csv_exists) {
                        foreach ($pdf_tecniche as $pdf) {
                            $nome = basename($pdf);
                            $nome_pulito = preg_replace('/^ST - /i', '', pathinfo($nome, PATHINFO_FILENAME));
                            echo '<a href="' . htmlspecialchars($subdir_name) . '/' . rawurlencode($nome) . '" class="file-link" target="_blank">📄 ' . htmlspecialchars($nome_pulito) . '</a><br>';
                        }
                        if (empty($pdf_tecniche)) echo '<span style="color:#ccc;">—</span>';
                    } else {
                        echo '<span style="color:#ccc;">—</span>';
                    }
                    echo '</td>';
                    
                    // Colonna SDS Sicurezza
                    echo '<td>';
                    if ($csv_exists) {
                        foreach ($pdf_sds as $pdf) {
                            $nome = basename($pdf);
                            $nome_pulito = preg_replace('/^SDS - /i', '', pathinfo($nome, PATHINFO_FILENAME));
                            echo '<a href="' . htmlspecialchars($subdir_name) . '/' . rawurlencode($nome) . '" class="file-link" target="_blank">⚠️ ' . htmlspecialchars($nome_pulito) . '</a><br>';
                        }
                        if (empty($pdf_sds)) echo '<span style="color:#ccc;">—</span>';
                    } else {
                        echo '<span style="color:#ccc;">—</span>';
                    }
                    echo '</td>';
                    
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        Italmont Srl - Schede tecniche e di sicurezza prodotti
    </div>
</div>

<script>
    // Filtro di ricerca live
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('productsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const text = cells[j].textContent || cells[j].innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            rows[i].style.display = found ? '' : 'none';
        }
    });

    // Data e ora nel pulsante
    function updateDateTime() {
        const now = new Date();
        const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
        document.getElementById('dateButton').textContent = '📅 ' + now.toLocaleDateString('it-IT') + ' ' + now.toLocaleTimeString('it-IT');
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>

</body>
</html>