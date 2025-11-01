<?php
// File: invia_email_scheda_server.php

// Questo script gestisce la richiesta AJAX e DEVE restituire un JSON.
header('Content-Type: application/json');

// 1. Configurazione del percorso Python e dello script
// *** AGGIORNARE QUESTO PERCORSO SE python3 NON E' IN /usr/bin/ ***
$python_command = '/usr/bin/python3'; 
$python_script = 'send_email_scheda.py'; 

// 2. RECUPERO DEI PARAMETRI (da POST)
$destinatari_input = filter_input(INPUT_POST, 'recipients', FILTER_SANITIZE_STRING);
$percorso_locale_file = filter_input(INPUT_POST, 'filePathMain', FILTER_SANITIZE_STRING);
$file_aggiuntivi_input = filter_input(INPUT_POST, 'extraFiles', FILTER_SANITIZE_STRING);
$file_principale_nome = filter_input(INPUT_POST, 'fileNameMain', FILTER_SANITIZE_STRING);

// 3. VALIDAZIONE BASE
if (empty($destinatari_input) || empty($percorso_locale_file) || !file_exists($percorso_locale_file)) {
    echo json_encode(['status' => 'error', 'message' => 'Dati mancanti, indirizzo email o percorso file principale non valido.']);
    exit;
}

// 4. PREPARAZIONE DATI PER PYTHON

// a) Destinatari multipli
$destinatari_puliti = preg_split('/[\s,;]+/', $destinatari_input, -1, PREG_SPLIT_NO_EMPTY);
$destinatari_validi = array_filter($destinatari_puliti, 'filter_var', FILTER_VALIDATE_EMAIL);
$destinatari_stringa = implode(',', $destinatari_validi);

if (empty($destinatari_stringa)) { 
    echo json_encode(['status' => 'error', 'message' => 'Nessun indirizzo email valido trovato.']);
    exit;
}

// b) File Aggiuntivi multipli
$dir_file_principale = dirname($percorso_locale_file);
$file_aggiuntivi_nomi = preg_split('/[\s,;]+/', $file_aggiuntivi_input, -1, PREG_SPLIT_NO_EMPTY);
$percorsi_aggiuntivi = [];

foreach ($file_aggiuntivi_nomi as $nome_file) {
    // Ricostruisce il percorso completo per i file aggiuntivi (si presuppone siano nella stessa cartella del file principale)
    $percorso_completo = $dir_file_principale . '/' . trim($nome_file);
    if (file_exists($percorso_completo)) {
        $percorsi_aggiuntivi[] = $percorso_completo;
    }
}

// c) Creazione della lista degli allegati completi (CORRETTO per singolo file)
$allegati_array = [$percorso_locale_file];
$allegati_array = array_merge($allegati_array, $percorsi_aggiuntivi);
$allegati_stringa = implode(',', $allegati_array);

// 5. Preparazione ed Esecuzione del comando Python

// Sanificazione degli input per prevenire 'Shell Injection'
$escaped_dest = escapeshellarg($destinatari_stringa);
$escaped_attachments = escapeshellarg($allegati_stringa);
$escaped_main_filename = escapeshellarg($file_principale_nome);

// *** Importante: Usa il percorso assoluto dello script Python ***
$python_script_path = dirname(__FILE__) . '/' . $python_script;

if (!file_exists($python_script_path)) {
    echo json_encode(['status' => 'error', 'message' => "Errore di sistema: Script Python non trovato al percorso: " . htmlspecialchars($python_script_path)]);
    exit;
}

// Il comando completo da eseguire
$command = "$python_command " . escapeshellarg($python_script_path) . " $escaped_dest $escaped_attachments $escaped_main_filename 2>&1";

// Esecuzione del comando Python
$output = shell_exec($command);

// 6. Gestione del risultato
if ($output === null || strpos(strtolower($output), 'error') === false) { 
    // Successo
    echo json_encode(['status' => 'success', 'message' => 'Email inviata con successo a: ' . htmlspecialchars($destinatari_stringa) . '.']);
} /* else {
    // Errore: l'output di Python contiene un errore
    $detail = trim($output) ?: 'Nessun output di errore specifico dal Python script. Controllare i permessi (chmod).';
    echo json_encode(['status' => 'error', 'message' => "Invio fallito. Dettaglio errore: " . nl2br(htmlspecialchars($detail))]);
}*/ 
else {
    header('Content-Type: text/plain'); // Per vedere l'output grezzo
    echo "Comando: " . $command . "\n\n";
    echo "Output Python:\n" . $output;
    exit;
}
exit;