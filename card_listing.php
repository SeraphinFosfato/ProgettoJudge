<?php
// Dati di connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carte";

// Creazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Imposta la codifica della connessione al database su UTF-8
$conn->set_charset("utf8");

// Esecuzione della query
$sql = "SELECT * FROM carteinpossesso as C JOIN listacarte as L on C.Nome = L.Nome ORDER BY C.Nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Design Responsive Table</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- Material Design CSS -->
    <link rel="stylesheet" href="https://cdn.rawgit.com/zavoloklom/material-design-iconic-font/master/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="https://cdn.rawgit.com/zavoloklom/material-design-lite/mdl/material.min.css">
    <link rel="stylesheet" href="https://cdn.rawgit.com/zavoloklom/material-design-hierarchical-display/master/dist/material-design-hierarchical-display.min.css">
    
    <!-- File CSS locale -->
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            text-align: center;
            margin: 0;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            background-color: #0d47a1; /* Blu scuro */
            color: white; /* Testo bianco */
        }
        .table-responsive-vertical {
            margin-top: 80px; /* spazio per il titolo fisso */
            width: 90%;
        }
        /* Stile per le celle della tabella */
        .table-responsive-vertical table td {
            background-color: white; /* Sfondo bianco */
        }
        /* Stilizza gli header delle colonne */
        .table th {
            background-color: #0D47A1; /* Blu scuro */
            color: #FFFFFF; /* Bianco */
            padding: 8px; /* Spaziatura interna */
        }

        /* Stilizza il testo nell'header della colonna */
        .table th {
            font-weight: bold; /* Testo in grassetto */
            text-align: center; /* Allinea il testo al centro */
        }
        body {
            background-color: #add8e6; /* Colore azzurro chiaro */
            background-position: center; /* Centra lo sfondo */
            background-size: cover; /* Copri l'intera area del body */
            background-attachment: fixed; /* Rendi lo sfondo fisso */
        }
    </style>

    
</head>
<body>
    <div id="demo">
        <h1>Lista delle Carte in Possesso</h1>
    
        <!-- Responsive table starts here -->
        <div class="table-responsive-vertical shadow-z-1">
            <!-- Table starts here -->
            <table id="table" class="table table-hover table-mc-light-blue">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Sottotipo</th>
                        <th>Proprietà</th>
                        <th>Quantità</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output dei dati di ogni riga
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td data-title='Nome'>" . htmlspecialchars($row["Nome"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td data-title='Tipo'>" . htmlspecialchars($row["Tipo"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td data-title='Attributo'>" . htmlspecialchars($row["TipoMagiaTrappola"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td data-title='Proprietà'>" . htmlspecialchars($row["Proprietà"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td data-title='Proprietà'>" . htmlspecialchars($row["Quantità"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nessun risultato trovato</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <!-- Material Design JS -->
    <script src="https://cdn.rawgit.com/zavoloklom/material-design-lite/mdl/material.min.js"></script>
    <script src="https://cdn.rawgit.com/zavoloklom/material-design-hierarchical-display/master/dist/material-design-hierarchical-display.min.js"></script>

    <!-- File JS locale -->
    <script src="function.js"></script>
</body>
</html>
