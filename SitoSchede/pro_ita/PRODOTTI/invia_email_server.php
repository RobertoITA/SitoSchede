<?php
/**
 * File: invia_email_server.php
 * Gestisce la richiesta AJAX dall'interfaccia, valida i dati
 * e invoca lo script Python per l'invio dell'email.
 * Restituisce una risposta JSON.
 */

// Imposta l'header per indicare che la risposta è in formato JSON
header('Content-Type: application/json');

// --- CONFIGURAZIONE ---
$python_command = '/usr/bin/python3'; 
$python_script_path = dirname(__FILE__) . '/send_email.py';

// --- FUNZIONE PER RESTITUIRE ERRORI ---
function return_error($message, $details = '') {
    echo json_encode(['status' => 'error', 'message' => $message, 'details' => $details]);
    exit;
}

// --- 1. RECUPERO E VALIDAZIONE DEI DATI (da POST) ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return_error('Metodo di richiesta non valido.');
}

$destinatario = filter_input(INPUT_POST, 'to', FILTER_SANITIZE_EMAIL);
$percorso_file = filter_input(INPUT_POST, 'path', FILTER_SANITIZE_STRING);
$nome_file = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);

// Validazione dell'indirizzo email
if (!$destinatario || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
    return_error('Indirizzo email non valido o mancante.');
}

// Validazione dei percorsi
if (empty($percorso_file) || empty($nome_file)) {
    return_error('Percorso del file o nome del file mancante.');
}

// CONTROLLO DI SICUREZZA FONDAMENTALE: verifica che il file esista sul server
if (!file_exists($percorso_file)) {
    // Logga l'errore per il debug lato server se necessario
    // error_log("Tentativo di invio file non trovato: " . $percorso_file);
    return_error('Il file richiesto non è stato trovato sul server.');
}

// --- 2. PREPARAZIONE ED ESECUZIONE DELLO SCRIPT PYTHON ---

// Sanifica ogni argomento per prevenire attacchi "Shell Injection"
$escaped_dest = escapeshellarg($destinatario);
$escaped_filepath = escapeshellarg($percorso_file);
$escaped_filename = escapeshellarg($nome_file);

// Costruisce il comando completo
// "2>&1" reindirizza l'output di errore (stderr) allo standard output (stdout),
// così possiamo catturare qualsiasi messaggio di errore da Python.
$command = "$python_command " . escapeshellarg($python_script_path) . " $escaped_dest $escaped_filepath $escaped_filename 2>&1";

// Esegue il comando usando 'exec' per ottenere il codice di ritorno
$output = [];
$return_code = 0;
exec($command, $output, $return_code);

// Converte l'array di output in una singola stringa per i dettagli dell'errore
$output_string = implode("\n", $output);

// --- 3. GESTIONE DELLA RISPOSTA ---

// Un codice di ritorno '0' da Python significa che l'esecuzione ha avuto successo.
if ($return_code === 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Email inviata con successo a: ' . htmlspecialchars($destinatario)
    ]);
} else {
    // Se il codice di ritorno è diverso da 0, si è verificato un errore.
    return_error(
        "Si è verificato un errore durante l'invio dell'email.",
        $output_string // Includi l'output di Python per il debug
    );
}
?>