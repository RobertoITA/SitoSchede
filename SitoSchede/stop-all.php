<?php

// Esegui lo script per fermare il backend
include 'stop-backend.php';

// Esegui lo script per fermare il frontend
include 'stop-frontend.php';

// Reindirizza a http://schede/
header('Location: http://schede/');
exit;

?>
