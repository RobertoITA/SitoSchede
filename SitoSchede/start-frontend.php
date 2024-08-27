<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avvia Frontend</title>
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
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
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
            // Avvia il frontend
            $output = shell_exec('serve -s /home/roberto/schedepdf/frontend/build > /dev/null 2>&1 &');
            if ($output === null) {
                echo "<div class='success-message'>Frontend avviato con successo!</div>";
            } else {
                echo "<div class='error-message'>Errore nell'avvio del frontend!</div>";
            }
        } else {
            echo "<div class='error-message'>Credenziali errate. Riprova.</div>";
        }
    }
    ?>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Avvia Frontend</button>
    </form>

    <form action="start.html" method="post">
        <button type="submit">Torna a start.html</button>
    </form>
</body>
</html>
