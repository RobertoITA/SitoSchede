<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco Schede Prodotti Italmont</title>
<style>
    /* ===== TEMA E ANIMAZIONI ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Sfondo grigio-verde scurissimo */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #16241e; /* Grigio-Verde Scurissimo */
        color: #2c3e50;
        padding: 20px;
        position: relative;
        overflow-x: hidden;
        min-height: 100vh;
    }

    /* Livello pattern del logo con effetto movimento al passaggio del mouse */
    #bg-pattern {
        position: fixed;
        /* Creiamo un quadrato enorme basato sulla dimensione massima dello schermo */
        top: -50vmax;
        left: -50vmax;
        width: 200vmax;
        height: 200vmax;
        
        background-image: url('logo_bianco.gif');
        background-size: 120px 75px;
        background-repeat: repeat;
        transform: rotate(45deg);
        z-index: -1; 
        opacity: 0.05; /* Trasparenza delicata */
        transition: transform 0.1s ease-out; /* Fluidità del movimento */
        pointer-events: none;
    }
    /* Container con trasparenza maggiore per far vedere lo sfondo */
    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.85); /* Aumentata trasparenza generale */
        border-radius: 12px;
        backdrop-filter: blur(5px); /* Leggera sfocatura dietro il pannello bianco */
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        position: relative;
        z-index: 10;
    }

    /* ===== HEADER STICKY CON BANDIERA FLUIDA ===== */
    @keyframes fluidFlag {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .header {
        background: linear-gradient(90deg, #009246, #ffffff, #ce2b37, #009246, #ffffff);
        background-size: 250% 100%;
        animation: fluidFlag 12s ease-in-out infinite;
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

    /* Controlli a sinistra (Lingua + Ricerca) */
    .left-controls {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 250px;
    }

    .lang-select {
        background: rgba(255,255,255,0.95);
        border: 2px solid rgba(0,0,0,0.1);
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 13px;
        color: #333;
        transition: all 0.2s;
        width: 100%;
        outline: none;
    }
    .lang-select:focus {
        border-color: #009246;
    }

    .search-box {
        padding: 10px 15px;
        border: 2px solid rgba(0,0,0,0.1);
        border-radius: 25px;
        width: 100%;
        font-size: 14px;
        background: rgba(255,255,255,0.95);
        color: #333;
        outline: none;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .search-box:focus {
        border-color: #009246;
        box-shadow: 0 0 0 3px rgba(0, 146, 70, 0.3);
    }

    /* Stile per i titoli */
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

    /* Pulsante Compatto Contatto e Data - Chiaro e in corsivo */
    .contact-btn {
        background: rgba(255, 255, 255, 0.95);
        color: #333;
        padding: 10px 15px;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 5px;
        transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        width: 220px; /* Mantiene il pulsante compatto a destra */
    }

    .contact-btn:hover {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.25);
    }

    .contact-text {
        font-size: 12px;
        font-weight: 500;
        font-style: italic; /* Corsivo richiesto */
        line-height: 1.3;
        color: #1a2b22;
        text-transform: none; /* Rimosso il maiuscolo */
    }

    .contact-time {
        font-size: 12px;
        font-weight: 700;
        color: #009246; /* Verde bandiera per risaltare */
        margin-top: 2px;
        border-top: 1px solid #ddd;
        padding-top: 4px;
        width: 100%;
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
        background: rgba(255, 255, 255, 0.95);
        color: #495057;
        padding: 15px 12px;
        text-align: left;
        font-weight: 700;
        border-bottom: 2px solid #e9ecef;
        position: sticky;
        top: 110px; 
        z-index: 990;
        backdrop-filter: blur(5px);
    }

    td {
        padding: 12px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        vertical-align: top;
    }

    /* Trasparenza aumentata per i colori alternati */
    tbody tr:nth-child(odd) {
        background-color: rgba(235, 245, 238, 0.3); /* Grigio/verde molto trasparente */
    }
    
    tbody tr:nth-child(even) {
        background-color: rgba(249, 238, 238, 0.3); /* Grigio/rosso molto trasparente */
    }

    tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.7); 
    }

    .file-link {
        color: #222; 
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 4px 0;
        transition: color 0.2s;
        font-weight: 600;
    }

    .file-link:hover {
        color: #ce2b37; 
        text-decoration: underline;
    }

    .file-link svg {
        color: #555;
    }
    
    .file-link:hover svg {
        color: #ce2b37;
    }

    td:first-child {
        font-weight: 800;
        color: #222;
        font-size: 1.1em;
    }

    .footer {
        background: rgba(248, 249, 250, 0.9);
        padding: 15px 20px;
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 12px 12px;
    }

    @media (max-width: 900px) {
        body { padding: 10px; }
        .header { flex-direction: column; padding: 15px; position: static; }
        .title-main { font-size: 1.8rem; }
        .title-sub { font-size: 0.8rem; }
        .left-controls { width: 100%; order: 3; align-items: center; }
        .title-group { order: 1; margin-bottom: 10px; }
        .contact-btn { order: 2; width: 100%; max-width: none; }
        .table-wrapper { padding: 0 10px 15px 10px; }

        /* Trasforma la tabella in un layout a card per mobile/tablet */
        table, thead, tbody, th, td, tr { 
            display: block; 
        }
        thead { 
            display: none; 
        }
        tbody tr { 
            margin-bottom: 15px;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: rgba(255, 255, 255, 0.95) !important;
        }
        td { 
            display: flex;
            flex-direction: column;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.05); 
            padding: 12px 15px;
            text-align: left;
            width: 100% !important;
        }
        td:last-child {
            border-bottom: none;
        }
        td::before { 
            content: attr(data-label);
            font-weight: 700;
            color: #009246;
            margin-bottom: 6px;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Aumenta l'area cliccabile dei link su mobile */
        .file-link {
            padding: 8px 0;
            display: inline-flex;
            width: 100%;
        }
    }
</style>
</head>
<body>

<!-- Livello interattivo per lo sfondo -->
<div id="bg-pattern"></div>

<?php
$svg_st_header = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2b7de9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
$svg_sds_header = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ce2b37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M12 18c-2.3 0-4-1.7-4-4s1.7-4 4-4 4 1.7 4 4-1.7 4-4 4z"></path><line x1="12" y1="13" x2="12" y2="15"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';

$svg_st_small = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';
$svg_sds_small = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M12 18c-2.3 0-4-1.7-4-4s1.7-4 4-4 4 1.7 4 4-1.7 4-4 4z"></path><line x1="12" y1="13" x2="12" y2="15"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
?>

<div class="container">
    <div class="header">
        
        <div class="left-controls">
            <select id="langSelect" class="lang-select" onchange="changeLanguage()">
                <option value="it">🇮🇹 Italiano</option>
                <option value="en">🇬🇧 English</option>
                <option value="fr">🇫🇷 Français</option>
                <option value="de">🇩🇪 Deutsch</option>
                <option value="es">🇪🇸 Español</option>
                <option value="pt">🇵🇹 Português</option>
                <option value="nl">🇳🇱 Nederlands</option>
                <option value="da">🇩🇰 Dansk</option>
                <option value="el">🇬🇷 Ελληνικά</option>
                <option value="sv">🇸🇪 Svenska</option>
            </select>
            <input type="text" id="searchInput" class="search-box" data-i18n="search" placeholder="🔍 Cerca prodotto...">
        </div>
        
        <div class="title-group">
            <div class="title-main">ITALMONT SRL</div>
            <div class="title-sub" data-i18n="subtitle">ELENCO SCHEDE PRODOTTI VERNICIANTI</div>
        </div>

        <a href="http://www.italmont.it/informazioni.html" class="contact-btn">
            <span class="contact-text" data-i18n="contact">Registrati per ricevere un contatto nel più breve tempo possibile, clicca qui.</span>
            <span class="contact-time" id="dateText">📅 Caricamento...</span>
        </a>
    </div>

    <div class="table-wrapper">
        <table id="productsTable">
            <thead>
                <tr>
                    <th width="8%"><span data-i18n="col1">Scheda Numero</span></th>
                    <th width="27%"><span data-i18n="col2">Descrizione</span></th>
                    <th width="32.5%">
                        <span class="st-header" title="Scheda Tecnica">
                            <?php echo $svg_st_header; ?>
                            <span data-i18n="col3">Scheda Tecnica</span>
                        </span>
                    </th>
                    <th width="32.5%">
                        <span class="sds-header" title="Scheda di Sicurezza">
                            <?php echo $svg_sds_header; ?>
                            <span data-i18n="col4">SDS (Scheda di Sicurezza)</span>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            $current_dir = dirname(__FILE__);
            $dir_path = $current_dir . '/';
            
            $subdirs = array_filter(glob($dir_path . '[0-9][0-9][0-9]'), 'is_dir');
            if (empty($subdirs)) {
                $subdirs = array_filter(glob($dir_path . '*'), 'is_dir');
                $subdirs = array_filter($subdirs, function($d) {
                    $name = basename($d);
                    return !in_array($name, ['assets', 'css', 'js', 'img', 'images', 'old', 'OLD']);
                });
            }
            
            sort($subdirs, SORT_NATURAL);
            
            foreach ($subdirs as $subdir) {
                $subdir_name = basename($subdir);
                $pdf_files = glob($subdir . '/*.pdf');
                
                $pdf_tecniche = array_filter($pdf_files, function($file) {
                    return stripos(basename($file), 'ST -') === 0 || 
                           stripos(basename($file), 'ST-') === 0 ||
                           stripos(basename($file), 'scheda tecnica') !== false;
                });
                
                $pdf_sds = array_filter($pdf_files, function($file) {
                    return stripos(basename($file), 'SDS -') === 0 ||
                           stripos(basename($file), 'SDS-') === 0 ||
                           stripos(basename($file), 'sicurezza') !== false;
                });
                
                $descrizione = ""; 
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
                
                if (!empty($pdf_files)) {
                    echo '<tr>';
                    echo '<td data-i18n-label="col1" data-label="Scheda Numero">' . htmlspecialchars($subdir_name) . '</td>';
                    echo '<td data-i18n-label="col2" data-label="Descrizione">' . $descrizione . '</td>';
                    
                    echo '<td data-i18n-label="col3" data-label="Scheda Tecnica">';
                    if ($csv_exists) {
                        foreach ($pdf_tecniche as $pdf) {
                            $nome = basename($pdf);
                            $nome_pulito = preg_replace('/^ST - /i', '', pathinfo($nome, PATHINFO_FILENAME));
                            echo '<a href="' . htmlspecialchars($subdir_name) . '/' . rawurlencode($nome) . '" class="file-link" target="_blank">' . $svg_st_small . ' ' . htmlspecialchars($nome_pulito) . '</a><br>';
                        }
                        if (empty($pdf_tecniche)) echo '<span style="color:#ccc;">—</span>';
                    } else {
                        echo '<span style="color:#ccc;">—</span>';
                    }
                    echo '</td>';
                    
                    echo '<td data-i18n-label="col4" data-label="SDS (Scheda di Sicurezza)">';
                    if ($csv_exists) {
                        foreach ($pdf_sds as $pdf) {
                            $nome = basename($pdf);
                            $nome_pulito = preg_replace('/^SDS - /i', '', pathinfo($nome, PATHINFO_FILENAME));
                            echo '<a href="' . htmlspecialchars($subdir_name) . '/' . rawurlencode($nome) . '" class="file-link" target="_blank">' . $svg_sds_small . ' ' . htmlspecialchars($nome_pulito) . '</a><br>';
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
    
    <div class="footer" data-i18n="footer">
        Italmont Srl - Schede tecniche e di sicurezza prodotti
    </div>
</div>

<script>
    // ===== EFFETTO PARALLASSE SFONDO COL MOUSE =====
    document.addEventListener("mousemove", function(e) {
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        const bg = document.getElementById('bg-pattern');
        // Muove lo sfondo di qualche pixel in base al mouse
        bg.style.transform = `rotate(45deg) translate(-${x * 30}px, -${y * 30}px)`;
    });

    // ===== GESTIONE LINGUE DELLA COMUNITÀ EUROPEA + EN =====
    const dictionary = {
        'it': { 'search': '🔍 Cerca prodotto...', 'subtitle': 'ELENCO SCHEDE PRODOTTI VERNICIANTI', 'contact': 'Registrati per ricevere un contatto nel più breve tempo possibile, clicca qui.', 'col1': 'Scheda Numero', 'col2': 'Descrizione', 'col3': 'Scheda Tecnica', 'col4': 'SDS (Scheda di Sicurezza)', 'footer': 'Italmont Srl - Schede tecniche e di sicurezza prodotti' },
        'en': { 'search': '🔍 Search product...', 'subtitle': 'COATING PRODUCTS DATA SHEETS', 'contact': 'Register to be contacted as soon as possible, click here.', 'col1': 'Sheet Number', 'col2': 'Description', 'col3': 'TDS (Technical Data Sheet)', 'col4': 'SDS (Safety Data Sheet)', 'footer': 'Italmont Srl - Technical and safety product sheets' },
        'fr': { 'search': '🔍 Rechercher...', 'subtitle': 'FICHES TECHNIQUES PRODUITS DE REVÊTEMENT', 'contact': 'Inscrivez-vous pour être contacté dans les plus brefs délais, cliquez ici.', 'col1': 'Numéro de Fiche', 'col2': 'Description', 'col3': 'Fiche Technique', 'col4': 'FDS (Fiche de Données de Sécurité)', 'footer': 'Italmont Srl - Fiches techniques et de sécurité' },
        'de': { 'search': '🔍 Produkt suchen...', 'subtitle': 'DATENBLÄTTER FÜR BESCHICHTUNGSPRODUKTE', 'contact': 'Registrieren Sie sich, um so schnell wie möglich kontaktiert zu werden. Klicken Sie hier.', 'col1': 'Blattnummer', 'col2': 'Beschreibung', 'col3': 'Technisches Datenblatt', 'col4': 'SDB (Sicherheitsdatenblatt)', 'footer': 'Italmont Srl - Technische und Sicherheitsdatenblätter' },
        'es': { 'search': '🔍 Buscar producto...', 'subtitle': 'FICHAS TÉCNICAS DE PRODUCTOS', 'contact': 'Regístrese para ser contactado lo antes posible, haga clic aquí.', 'col1': 'Número de Ficha', 'col2': 'Descripción', 'col3': 'Ficha Técnica', 'col4': 'FDS (Ficha de Datos de Seguridad)', 'footer': 'Italmont Srl - Fichas técnicas y de seguridad' },
        'pt': { 'search': '🔍 Procurar produto...', 'subtitle': 'FICHAS TÉCNICAS DE PRODUTOS', 'contact': 'Registe-se para ser contactado o mais rapidamente possível, clique aqui.', 'col1': 'Número da Ficha', 'col2': 'Descrição', 'col3': 'Ficha Técnica', 'col4': 'FDS (Ficha de Dados de Segurança)', 'footer': 'Italmont Srl - Fichas técnicas e de segurança' },
        'nl': { 'search': '🔍 Product zoeken...', 'subtitle': 'INFORMATIEBLADEN COATINGPRODUCTEN', 'contact': 'Registreer u om zo snel mogelijk gecontacteerd te worden, klik hier.', 'col1': 'Bladnummer', 'col2': 'Beschrijving', 'col3': 'Technisch Informatieblad', 'col4': 'VIB (Veiligheidsinformatieblad)', 'footer': 'Italmont Srl - Technische en veiligheidsinformatiebladen' },
        'da': { 'search': '🔍 Søg produkt...', 'subtitle': 'DATABLADE FOR BELÆGNINGSPRODUKTER', 'contact': 'Registrer dig for at blive kontaktet hurtigst muligt, klik her.', 'col1': 'Bladnummer', 'col2': 'Beskrivelse', 'col3': 'Teknisk Datablad', 'col4': 'SDB (Sikkerhedsdatablad)', 'footer': 'Italmont Srl - Tekniske og sikkerhedsdatablade' },
        'el': { 'search': '🔍 Αναζήτηση...', 'subtitle': 'ΦΥΛΛΑ ΔΕΔΟΜΕΝΩΝ ΠΡΟΪΟΝΤΩΝ', 'contact': 'Εγγραφείτε για να επικοινωνήσουμε μαζί σας το συντομότερο δυνατό, κάντε κλικ εδώ.', 'col1': 'Αριθμός Φύλλου', 'col2': 'Περιγραφή', 'col3': 'Τεχνικό Φυλλάδιο', 'col4': 'SDS (Δελτίο Ασφαλείας)', 'footer': 'Italmont Srl - Τεχνικά δελτία και δελτία ασφαλείας' },
        'sv': { 'search': '🔍 Sök produkt...', 'subtitle': 'DATABLAD FÖR BELÄGGNINGSPRODUKTER', 'contact': 'Registrera dig för att bli kontaktad så snart som möjligt, klicka här.', 'col1': 'Bladnummer', 'col2': 'Beskrivning', 'col3': 'Tekniskt Datablad', 'col4': 'SDB (Säkerhetsdatablad)', 'footer': 'Italmont Srl - Tekniska och säkerhetsdatablad' }
    };

    let currentLang = 'it';

    function changeLanguage() {
        const select = document.getElementById('langSelect');
        currentLang = select.value;
        document.documentElement.lang = currentLang;

        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (element.tagName === 'INPUT') {
                element.placeholder = dictionary[currentLang][key];
            } else {
                element.innerText = dictionary[currentLang][key];
            }
        });

        // Aggiorna i data-label per la visualizzazione mobile
        document.querySelectorAll('[data-i18n-label]').forEach(element => {
            const key = element.getAttribute('data-i18n-label');
            if(dictionary[currentLang][key]) {
                element.setAttribute('data-label', dictionary[currentLang][key]);
            }
        });

        updateDateTime(); // Aggiorna subito il formato data/ora
    }

    // ===== FILTRO DI RICERCA LIVE =====
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
                    found = true; break;
                }
            }
            rows[i].style.display = found ? '' : 'none';
        }
    });

    // ===== DATA E ORA (INCLUSI SECONDI) =====
    function updateDateTime() {
        const now = new Date();
        const optionsDate = { day: '2-digit', month: '2-digit', year: 'numeric' };
        const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        
        let locale = currentLang;
        if(currentLang === 'en') locale = 'en-GB';

        document.getElementById('dateText').innerHTML = 
            '📅 ' + now.toLocaleDateString(locale, optionsDate) + ' &bull; ' + now.toLocaleTimeString(locale, optionsTime);
    }
    
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>

</body>
</html>