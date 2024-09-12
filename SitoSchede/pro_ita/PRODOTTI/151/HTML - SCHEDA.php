<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <?php
    header('Content-Type: text/html; charset=utf-8');
    $fileHandle = fopen("SCHEDA.csv", "r");
    if (!$fileHandle) {
        echo "<title>Impossibile aprire il file</title>";
    } else {
        $data = [];
        while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
            foreach ($row as $key => $value) {
                $row[$key] = mb_convert_encoding($value, "UTF-8", "ISO-8859-1");
            }
            $data[] = $row;
        }
        fclose($fileHandle);

        // Estrazione della prima voce dal file CSV
        $schedaNumero = htmlspecialchars($data[0][0]); // La prima voce del file CSV
        echo "<title>Scheda " . $schedaNumero . "</title>";
    }
    ?>
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
            border-collapse: collapse;
            background-color: transparent;
            color: white;
        }

        .header-table {
            table-layout: auto;
        }

        .header-table .left-column {
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
            table-layout: fixed;
        }

        .info-table th, .info-table td {
            padding: 10px;
            border-bottom: 2px solid #008000;
            border-top: 2px solid #008000;
            vertical-align: top;
            word-break: break-word;
            white-space: normal;
            text-align: left;
        }

        .info-table th {
            width: 180px;
            min-width: 180px;
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

        @media (max-width: 600px) {
            .info-table th, .info-table td {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<?php
if (isset($data)) {
    // Estrazione dei dati dall'intestazione
    $logoFile = htmlspecialchars($data[1][0]);
    $title = htmlspecialchars($data[2][0]);
    $schedaImg = htmlspecialchars($data[3][0]);
?>

<table class="header-table">
    <tr>
        <td class="left-column">
            <span>SCHEDA NUMERO: <?php echo $schedaNumero; ?></span><br>
            <img src="<?php echo $logoFile; ?>" alt="Logo della scheda" class="scheda-logo">
        </td>
        <td style="text-align: center;">
            <h1><?php echo $title; ?></h1>
        </td>
        <td style="text-align: right;">
            <img src="<?php echo $schedaImg; ?>" alt="Immagine della scheda" class="scheda-img">
        </td>
    </tr>
</table>

<table class="info-table">
    <?php
    for ($i = 4; $i < count($data); $i++) {
        $row = $data[$i];

        // Filtra righe vuote
        $filtered_row = array_filter($row, fn($value) => !empty(trim($value)));

        if (count($filtered_row) > 1) {
            echo '<tr>';
            echo '<th>' . htmlspecialchars(array_shift($filtered_row)) . '</th>';
            echo '<td>';
            // Unisci tutte le colonne non vuote in una sola stringa separata da un salto di riga
            echo nl2br(htmlspecialchars(implode("\n", $filtered_row)));
            echo '</td>';
            echo '</tr>';
        }
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
