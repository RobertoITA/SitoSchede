<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Elenco delle Sottocartelle e File</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        flex-direction: column;
        min-height: 100vh;
        background: linear-gradient(to bottom, #003000, #003f00 15%, #FFFFFF 38%, #FFFFFF 62%, #630000 85%, #630000);
        color: #211c3c;
        text-align: center;
        padding-top: 50px; /* Aggiungi padding per evitare sovrapposizioni con intestazione fissa */
    }
    table {
        width: 90%; /* Ho aumentato la larghezza della tabella per adattare più colonne */
        margin: 20px auto; /* Centra la tabella */
        border-collapse: collapse;
        background-color: rgba(0, 0, 0, 0.75); /* Sfondo tabella scuro */
        color: white; /* Testo bianco */
    }
    th, td {
        border: 1px solid white;
        padding: 8px;
        text-align: left; /* Allinea il testo a sinistra */
    }
    th {
        background-color: rgba(1, 1, 1, 0.75);
        color: rgba(255,255,255,0.9);
        position: sticky;
        top: 0;
        z-index: 1;
    }
    tr:nth-child(even) {
        background-color: rgba(64, 64, 64, 0.75);
    }
    tr:nth-child(odd) {
        background-color: rgb(102, 102, 101, 0.75);
    }
    .file-link {
        color: white;
        text-decoration: none;
    }
    .file-link:hover {
        color: yellow;
    }
    .search-box {
        margin-bottom: 20px;
        padding: 10px;
        width: 300px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    h1 {
        color: white; /* Colore bianco per il titolo principale */
        margin-bottom: 30px;
    }
</style>
</head>
<body>

<h1>ELENCO DELLE SOTTOCARTELLE E DEI FILE PRODOTTI</h1>

<input type="text" id="searchInput" class="search-box" placeholder="Cerca nel nome della cartella o del file...">

<table>
    <thead>
        <tr>
            <th>Nome Cartella</th>
            <th>.jpg</th>
            <th>.png</th>
            <th>.docx</th>
            <th>.doc</th>
            <th>.pdf</th>
            <th>.csv</th>
            <th>.php</th>
            <th>.html</th>
            <th>Altro</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Percorso della directory contenente le sottocartelle
    $base_dir_path = '/var/www/html/SitoSchede/pro_ita/PRODOTTI/';

    // Scandisci la directory per trovare le sottocartelle
    $subdirs = array_filter(glob($base_dir_path . '*'), 'is_dir');

    // Definisci le estensioni dei file che ci interessano e il loro ordine
    $extensions = ['jpg', 'png', 'docx', 'doc', 'pdf', 'csv', 'php', 'html'];

    foreach ($subdirs as $subdir) {
        $subdir_name = basename($subdir);
        echo "<tr>";
        echo "<td><a href='explore_directory.php?dir=" . urlencode($subdir_name) . "' class='file-link' target='_blank'>$subdir_name</a></td>";

        // Array per raggruppare i file per estensione
        $grouped_files = [];
        foreach ($extensions as $ext) {
            $grouped_files[$ext] = [];
        }
        $grouped_files['other'] = []; // Per le estensioni "altro"

        // Scandisci i file all'interno di questa sottocartella
        $files_in_subdir = glob($subdir . '/*');
        foreach ($files_in_subdir as $file) {
            if (is_file($file)) {
                $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($file_extension, $extensions)) {
                    $grouped_files[$file_extension][] = basename($file);
                } else {
                    $grouped_files['other'][] = basename($file);
                }
            }
        }

        // Output dei file per ogni colonna
        foreach ($extensions as $ext) {
            echo "<td>";
            if (!empty($grouped_files[$ext])) {
                foreach ($grouped_files[$ext] as $filename) {
                    echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$filename' class='file-link' target='_blank'>$filename</a><br>";
                }
            }
            echo "</td>";
        }

        // Colonna "Altro"
        echo "<td>";
        if (!empty($grouped_files['other'])) {
            foreach ($grouped_files['other'] as $filename) {
                echo "<a href='/SitoSchede/pro_ita/PRODOTTI/$subdir_name/$filename' class='file-link' target='_blank'>$filename</a><br>";
            }
        }
        echo "</td>";

        echo "</tr>";
    }
    ?>
    </tbody>
</table>

<script>
    document.getElementById("searchInput").addEventListener("input", function() {
        var input, filter, table, tr, td_folder_name, td_files, i, j, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector("table");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) { // Inizia da 1 per saltare l'intestazione
            tr[i].style.display = "none"; // Nascondi la riga per default

            // Controlla il nome della cartella (prima colonna)
            td_folder_name = tr[i].getElementsByTagName("td")[0];
            if (td_folder_name) {
                txtValue = td_folder_name.textContent || td_folder_name.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = ""; // Mostra la riga se c'è una corrispondenza nel nome della cartella
                    continue; // Passa alla prossima riga
                }
            }

            // Controlla i nomi dei file nelle altre colonne
            for (j = 1; j < tr[i].getElementsByTagName("td").length; j++) { // Inizia da 1 per saltare la colonna del nome cartella
                td_files = tr[i].getElementsByTagName("td")[j];
                if (td_files) {
                    txtValue = td_files.textContent || td_files.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ""; // Mostra la riga se c'è una corrispondenza in un nome di file
                        break; // Esci dal ciclo delle colonne e passa alla prossima riga
                    }
                }
            }
        }
    });
</script>

</body>
</html>