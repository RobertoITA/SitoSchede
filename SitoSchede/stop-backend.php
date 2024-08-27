<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stop Backend</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        .success-message {
            color: #4CAF50;
            margin-top: 20px;
        }
        .error-message {
            color: #FF5733;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php
    // Ferma il backend
    $output = shell_exec('pkill -f "/home/roberto/schedepdf/frontend/backend/index.js"');
    if ($output === null) {
        echo "<div class='success-message'>Backend fermato con successo!</div>";
    } else {
        echo "<div class='error-message'>Errore nel fermare il backend!</div>";
    }
    ?>
    
    <form action="start.html" method="post">
        <button type="submit">Torna a start.html</button>
    </form>
</body>
</html>
