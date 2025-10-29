<?php
// File: invia_email_server.php

// 1. Configurazione del percorso Python
// ASSICURATI che il percorso sia CORRETTO sul tuo server
$python_command = '/usr/bin/python3.8'; 
$python_script = 'send_email.py';
$homepage = 'sfoglia_pdf.php';

// 2. RECUPERO DEI PARAMETRI
$destinatario = filter_input(INPUT_GET, 'to', FILTER_VALIDATE_EMAIL);
$percorso_locale_file = filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING);
$nome_file = filter_input(INPUT_GET, 'filename', FILTER_SANITIZE_STRING);

// 3. VALIDAZIONE
if (!$destinatario || !$percorso_locale_file || !$nome_file || !file_exists($percorso_locale_file)) {
    header('Location: ' . $homepage . '?status=error&msg=' . urlencode('Dati mancanti, indirizzo email non valido, o file non trovato sul server.'));
    exit;
}

// 4. Preparazione del comando Python
// Sanificazione degli input per prevenire 'Shell Injection'
// Usiamo shell_exec per eseguire il comando e catturarne l'output
$escaped_dest = escapeshellarg($destinatario);
$escaped_path = escapeshellarg($percorso_locale_file);
$escaped_name = escapeshellarg($nome_file);

// Il comando completo da eseguire sulla shell
$command = "$python_command $python_script $escaped_dest $escaped_path $escaped_name 2>&1";

// Esecuzione del comando Python
$output = shell_exec($command);

// 5. Gestione del risultato
// Lo script Python deve restituire 0 in caso di successo
// Se l'output è vuoto, assumiamo il successo (o un errore che non ha prodotto output)
if ($output === null || strpos(strtolower($output), 'error') === false) { 
    // Successo
    header('Location: ' . $homepage . '?status=success&msg=' . urlencode('Email inviata con successo a: ' . $destinatario));
    exit;
} else {
    // Errore
    // Logghiamo l'errore per debug se necessario
    // file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " - Error: " . $output . "\n", FILE_APPEND);
    header('Location: ' . $homepage . '?status=error&msg=' . urlencode("Errore durante l'invio dell'email (Dettaglio: Controlla i log di sistema)."));
    exit;
}
?>