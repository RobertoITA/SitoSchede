<?php
// File: send_email.php

// Funzione per l'invio di email con allegato
function sendEmailWithAttachment($to, $subject, $message, $file_path) {
    // Verifica se il file esiste
    if (!file_exists($file_path)) {
        return false; // Il file non esiste
    }

    // Contenuto del file
    $file_content = file_get_contents($file_path);
    if ($file_content === false) {
        return false; // Errore nel leggere il file
    }

    // Mime type del file
    $file_mime_type = mime_content_type($file_path);
    if ($file_mime_type === false) {
        return false; // Errore nel determinare il mime type del file
    }

    // Nome del file
    $file_name = basename($file_path);

    // Boundary per separare i diversi parti dell'email
    $boundary = md5(time());

    // Intestazioni dell'email
    $headers = "From: sender@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Corpo dell'email
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "$message\r\n";

    // Allegato
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: $file_mime_type; name=\"$file_name\"\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($file_content)) . "\r\n";

    // Chiusura del boundary
    $body .= "--$boundary--";

    // Invio dell'email
    return mail($to, $subject, $body, $headers);
}

// Esempio di utilizzo della funzione sendEmailWithAttachment
$to = "recipient@example.com";
$subject = "Oggetto dell'email";
$message = "Corpo dell'email.";
$file_path = "/var/www/html/SitoSchede/pro_ita/PRODOTTI/"; // Aggiungi qui il percorso del file da allegare
if (sendEmailWithAttachment($to, $subject, $message, $file_path)) {
    echo "Email inviata con successo.";
} else {
    echo "Errore nell'invio dell'email.";
}
?>
