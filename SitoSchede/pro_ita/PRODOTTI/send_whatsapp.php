<?php
require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

if(isset($_GET['file'])) {
    $file_path = $_GET['file'];

    $account_sid = 'YOUR_ACCOUNT_SID';
    $auth_token = 'YOUR_AUTH_TOKEN';
    $twilio_number = 'YOUR_TWILIO_NUMBER';
    $to_number = 'RECIPIENT_NUMBER';

    $client = new Client($account_sid, $auth_token);

    try {
        // Aggiornamento del percorso del file PDF
        $message = $client->messages->create(
            "whatsapp:$to_number",
            array(
                "from" => "whatsapp:$twilio_number",
                "body" => "Invio file PDF via WhatsApp",
                "mediaUrl" => ["http://schede/SitoSchede/pro_ita/PRODOTTI/$file_path"]
            )
        );

        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        echo 'Messaggio non inviato. Errore: ', $e->getMessage();
    }
} else {
    echo 'File non specificato.';
}
?>
