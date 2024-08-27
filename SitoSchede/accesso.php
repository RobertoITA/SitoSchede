<!DOCTYPE html>
<html lang="it">
<style>
        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle, #333333, #000000);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #container {
            text-align: center;
        }
        button {
            padding: 15px 30px;
            background-color: #333;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {

    margin: 0;
    padding: 0;
    background: radial-gradient(circle, #333333, #000000);
    display: flex;
    justify-content: center;
    align-items: center;
    height:

            font-family: Arial, sans-serif;
            text-align: center;
        }
        #login {
            display: inline-block;
            padding: 20px;
            border: 2px solid #ccc;
        }
        input {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div id="Login">
<font color=#008C00><h1>ACCESSO</h1></font>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Nome dell'utente" required><br>
        <input type="password" name="password" placeholder="Parola d'ordine" required><br>
        <button type="submit" name="submit">ACCEDI</button>
    </form>
</div>

<?php
if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lettura delle credenziali dal file accessi.txt
    $file = fopen("/home/roberto/schedepdf/accessi.txt", "r");
    while(!feof($file)) {
        $line = fgets($file);
        $credentials = explode(",", $line);
        if(trim($credentials[0]) == $username && trim($credentials[1]) == $password) {
            $link = trim($credentials[2]);
            fclose($file);
            header("Location: $link");
            exit();
        }
    }
    fclose($file);
    echo "<p style='color: red;'>Credenziali non valide.</p>";
}
?>

</body>
</html>
