<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scheda 014</title>
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

        .header-table {
          width: 95%;
            margin: 5px auto;
            box-shadow: 0px 0px 0px rgba(0,0,0,0.1);
            width: 95%;
            table-layout: auto;
            border-collapse: collapse;
            background-color: transparent;
            color: white;
        }

        .header-table td {
            border: 0px solid #211c3c; /* Bordo sottile come richiesto */
            vertical-align: middle;
            text-align: center;
        }

        .header-table .left-column {
            text-align: left;
            padding-left: 10px;
        }

        .header-table h1 {
            margin: 0;
            font-size: 72px; /* Adatta la dimensione a seconda delle tue necessità */
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
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            color: black;
            box-shadow: 10px 10px 10px rgba(0,0,0,0.1);
        }

        .info-table th, .info-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #008000;
            border-top: 2px solid #008000;
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
<table class="header-table" style="width: 94%; border-collapse: collapse;">
        <tr>
            <!-- Prima colonna: Scheda Numero e Logo -->
            <td class="left-column" style="vertical-align: top; text-align: left;">
                <div>
                    <span>SCHEDA NUMERO: 014</span><br>
                    <img src="logo.jpg" alt="Logo della scheda" class="scheda-logo">
                </div>
            </td>
            <!-- Seconda colonna: Titolo Centrale -->
            <td style="text-align: center;">
                <h1>AMETYST BIANCO</h1>
            </td>
            <!-- Terza colonna: Immagine della Scheda -->
            <td style="text-align: right;">
                <img src="014.png" alt="Immagine della scheda" class="scheda-img">
            </td>
        </tr>
    </table>
    <table class="info-table">
        <tr>
            <th>DESCRIZIONE</th>
            <td>Idropittura traspirante per interni, caratterizzata da buon potere coprente e buon punto di bianco. Facile da applicare, possiede una ottima pennellabilità buona da usare a rullo ed ha una buona resa, si presta per applicazioni in edifici storici e moderni in cui c'è bisogno di traspirabilità.  </td>
        </tr>
        <tr>
            <th>ASPETTO</th>
            <td>Opaco</td>
        </tr>
        <tr>
            <th>PESO SPECIFICO</th>
            <td>1,54 Kg/l ± 0,05 a 20°C (KGP1014: 1,50 Kg/l ± 0,05 a 20°C)</td>
        </tr>
        <tr>
            <th>RESIDUO SECCO</th>
            <td>57 ± 0,5 % p/p</td>
        </tr>
        <tr>
            <th>PERMEABILITÀ AL VAPORE ACQUEO</th>
            <td>Alta</td>
        </tr>
        <tr>
            <th>PRESA DI SPORCO</th>
            <td>Bassa</td>
        </tr>
        <tr>
            <th>COLORE</th>
            <td>Bianco (Colorabile con Coloranti Universali non associato a sistema tintometrico)</td>
        </tr>
        <tr>
            <th>ESSICCAZIONE</th>
            <td>Per ricopertura: 6 ore; completo: 48 ore (dati riferiti a 20°C e 65% di U.R.)</td>
        </tr>
        <tr>
            <th>RESA PRATICA</th>
            <td>10-12 m2/L per mano in funzione dell’assorbimento del supporto</td>
        </tr>
        <tr>
            <th>DILUIZIONE</th>
            <td>1a mano: aggiungere il 35% di acqua potabile<br>2a mano: aggiungere il 30% di acqua potabile<br>Spruzzo airless:aggiungere tra il 30% e il 40% di acqua potabile. Ugelli consigliati: LP419–LP519–LP619- LP421–LP521–LP621</td>
        </tr>
        <tr>
            <th>ATTREZZI</th>
            <td>Pennello, Rullo, Spruzzo</td>
        </tr>
        <tr>
            <th>SUPPORTI</th>
            <td>Intonaco civile, cartongesso, fibrocemento, pareti rasate a stucco</td>
        </tr>
        <tr>
            <th>SCHEDE TECNICHE</th>
            <td>
                <!-- PHP code to list PDF files -->
                <?php
                  foreach (glob("ST -*.pdf") as $filename) {
                      echo "<a href='$filename' view>" . htmlspecialchars($filename) . "</a><br>";
                  }
                  ?>
            </td>
        </tr>
        <tr>
            <th>SCHEDE DI SICUREZZA</th>
            <td>
                <!-- PHP code to list PDF files -->
                <?php
                  foreach (glob("SDS -*.pdf") as $filename) {
                      echo "<a href='$filename' view>" . htmlspecialchars($filename) . "</a><br>";
                  }
                  ?>
            </td>
        </tr>
    </table>
    <footer>
        Italmont Srl - Via IV Novembre, 13 63078 Pagliare del Tronto - Spinetoli (AP) Part. IVA 01441970447 Tel. 0736899238 Fax 0736899489 <a href="http://www.italmont.it" target="_blank">www.italmont.it</a> E-mail: <a href="mailto:info@italmont.it">info@italmont.it</a>
    </footer>
</body>
</html>
