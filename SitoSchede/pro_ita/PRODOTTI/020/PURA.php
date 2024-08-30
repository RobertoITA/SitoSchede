<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda 020</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(to bottom, #003000, #003f00 15%, #FFFFFF 38%, #FFFFFF 62%, #630000 85%, #630000);
            color: #211c3c;
            text-align: center;
        }

        .header-table, .info-table {
            width: 95%;
            margin: 20px auto;
            box-shadow: 0px 0px 0px rgba(0,0,0,0.1);
            table-layout: auto;
            border-collapse: collapse;
            background-color: transparent;
            color: white;
        }

        .header-table .left-column, .info-table th, .info-table td {
            text-align: left;
            padding-left: 5px;
        }

        .header-table h1 {
            margin: 0;
            font-size: 72px;
        }

        .scheda-logo {
            width: 180px;
            height: 120px;
        }

        .scheda-img {
            width: 140px;
            height: 150px;
        }

        .info-table {
            background-color: rgba(255, 255, 255, 0.7);
            color: black;
            box-shadow: 10px 10px 10px rgba(0,0,0,0.1);
        }

        .info-table th, .info-table td {
            padding: 10px;
            border-bottom: 2px solid #008000;
            border-top: 2px solid #008000;
            vertical-align: top;
        }

        footer {
            background: #630000;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
            color: #888888;
        }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td class="left-column">
            <span>SCHEDA NUMERO: 020</span><br>
            <img src="logo.jpg" alt="Logo della scheda" class="scheda-logo">
        </td>
        <td style="text-align: center;">
            <h1>PURA</h1>
        </td>
        <td style="text-align: right;">
            <img src="020.png" alt="Immagine della scheda" class="scheda-img">
        </td>
    </tr>
</table>

<?php
header('Content-Type: text/html; charset=utf-8');
$fileHandle = fopen("SCHEDA.csv", "r");
if (!$fileHandle) {
    echo "<p>Impossibile aprire il file.</p>";
} else {
    $data = [];
    while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
        foreach ($row as $key => $value) {
            $row[$key] = mb_convert_encoding($value, "UTF-8", "ISO-8859-1");
        }
        $data[] = $row;
    }
    fclose($fileHandle);
?>
<table class="info-table">
    <?php
    foreach ($data as $row) {
        echo '<tr>';
        echo '<th>' . htmlspecialchars($row[0]) . '</th>';
        echo '<td>';
        for ($i = 1; $i < count($row); $i++) {
            echo htmlspecialchars($row[$i]);
            if ($i < count($row) - 1) {
                echo "<br>";
            }
        }
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>

<table class="info-table">
    <tr>
        <th>SCHEDE TECNICHE</th>
        <th>SCHEDE DI SICUREZZA</th>
        <th>CERTIFICATI</th>
    </tr>
    <tr>
        <td>
            <?php
            $files = glob("ST -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    echo "<a href='" . htmlspecialchars($filename) . "' download>" . htmlspecialchars($filename) . "</a><br>";
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
        <td>
            <?php
            $files = glob("SDS -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    echo "<a href='" . htmlspecialchars($filename) . "' download>" . htmlspecialchars($filename) . "</a><br>";
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
        <td>
            <?php
            $files = glob("CER -*.pdf");
            if (!empty($files)) {
                foreach ($files as $filename) {
                    echo "<a href='" . htmlspecialchars($filename) . "' download>" . htmlspecialchars($filename) . "</a><br>";
                }
            } else {
                echo "Nessun file disponibile";
            }
            ?>
        </td>
    </tr>
</table>

<?php
}
?>

<footer>
    Italmont Srl - Via IV Novembre, 13 63078 Pagliare del Tronto - Spinetoli (AP) Part. IVA 01441970447 Tel. 0736899238 Fax 0736899489 <a href="http://www.italmont.it" target="_blank">www.italmont.it</a> E-mail: <a href="mailto:info@italmont.it">info@italmont.it</a>
</footer>

</body>
</html>