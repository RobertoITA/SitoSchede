<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avvia Backend</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        .message {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .button-container {
            display: flex;
            justify-content: center;
        }
        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 15px;
        }
        .button-container button:hover {
            background-color: #45a049;
        }
        form {
            margin-top: 20px;
        }
        input {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 250px;
        }
    </style>
</head>
<body>
    <?php
    function checkCredentials($username, $password) {
        // Leggi le credenziali dal file accesso.txt
        $credentials = file('/home/roberto/schedepdf/accesso.txt', FILE_IGNORE_NEW_LINES);
        
        // Verifica se le credenziali inserite corrispondono a quelle nel file
        foreach ($credentials as $cred) {
            list($user, $pass) = explode(',', $cred);
            if ($username === trim($user) && $password === trim($pass)) {
                return true;
            }
        }
        return false;
    }

    // Verifica delle credenziali se il modulo di login è stato inviato
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $input_username = $_POST['username'];
        $input_password = $_POST['password'];

        if (checkCredentials($input_username, $input_password)) {
            // Avvia il backend
            $output = shell_exec('node /home/roberto/schedepdf/frontend/backend/index.js > /dev/null 2>&1 &');
            if ($output === null) {
                echo "<div class='message'>COMANDO ESEGUITO CON SUCCESSO</div>";
            } else {
                echo "<div class='message'>Errore nell'avvio del backend!</div>";
            }
        } else {
            echo "<div class='message'>Credenziali errate. Riprova.</div>";
        }
    }
    ?>

    <div class="button-container">
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Avvia Backend</button>
        </form>
    </div>

    <div class="button-container">
        <form action="start.html" method="post">
            <button type="submit">Torna a start.html</button>
        </form>
    </div>
</body>
</html>
