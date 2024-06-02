<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Search</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url("https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/310a411f-2225-4702-b559-64f4a6f7eb6a/dgw9n7i-a301d2b5-32d4-4745-82e7-a5b3748f02bc.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcLzMxMGE0MTFmLTIyMjUtNDcwMi1iNTU5LTY0ZjRhNmY3ZWI2YVwvZGd3OW43aS1hMzAxZDJiNS0zMmQ0LTQ3NDUtODJlNy1hNWIzNzQ4ZjAyYmMuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.6KOLliMlHFul8X9JDP4Z98VTIdEY6C_hEYi5HgNJCz4");
            background-size: cover;
            background-position: center;
            overflow: auto;
            font-family: Arial, sans-serif;
            color: #fff;
        }
        
        form {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            width: 500px;
            padding-right: 20px;
        }
        
        label {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }
        
        input[type="text"] {
            width: calc(100% - 20px); 
            padding: 10px;
            font-size: 16px;
            border: 1px solid #fff;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.5);
            color: #333;
            margin-bottom: 10px;
        }
        
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        
        .results-container {
            margin-top: 20px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
        }
    </style>
</head>
<body>
        
    <form action="" method="POST">
        <label for="cardSearch">Cerca la tua carta:</label>
        <input type="text" id="cardSearch" name="cardSearch">
        <input type="hidden" id="hiddeninfo" name="op" value="add">
        <br>
        <button type="submit">Cerca</button>
        <button type="submit" onclick="updateHiddenInput()">Rimuovi</button>

        <?php
            // Check if the value has been submitted
            if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["op"] === "add"){
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "carte";
                $conn = mysqli_connect($servername,$username,$password,$dbname);
                if (!$conn){
                    die("Connessione fallita: " . mysqli_connect_error());
                }
                $query = "SELECT * FROM listacarte WHERE (Name= ? or Nome= ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt,"ss",$_POST["cardSearch"], $_POST["cardSearch"]);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($result);
                if(isset($row)){
                    $query = "SELECT Quantità from carteinpossesso WHERE Nome= ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt,"s",$row["Nome"]);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $quantity = mysqli_fetch_assoc($result);
                    if(!isset($quantity["Quantità"])) $quantity = 0;
                    else $quantity = $quantity["Quantità"];
                        if($row["Attacco"] === "-1") $row["Attacco"] = "?";
                        if($row["Difesa"] === "-1") $row["Difesa"] = "?";
                        echo "<div class='results-container'>";
                        echo "<table>";
                        switch($row["Tipo"]){
                            case "Monster":
                                echo "<tr><td>Name</td><td>" . $row["Nome"] . "</td></tr>";
                                echo "<tr><td>Attribute</td><td>" . $row["Attributo"] . "</td></tr>";
                                echo "<tr><td>Level</td><td>" . $row["Livello"]. "</td></tr>";
                                echo "<tr><td>Properties</td><td>" . $row["Proprietà"] . "</td></tr>";
                                echo "<tr><td>Effect</td><td>" . $row["Effetto"] . "</td></tr>";
                                echo "<tr><td>ATK</td><td>" . $row["Attacco"] . "</td></tr>";
                                echo "<tr><td>DEF</td><td>" . $row["Difesa"] . "</td></tr>";
                                break;
                            case "XYZ":
                                echo "<tr><td>Name</td><td>" . $row["Nome"] . "</td></tr>";
                                echo "<tr><td>Attribute</td><td>" . $row["Attributo"] . "</td></tr>";
                                echo "<tr><td>Rank</td><td>" .  $row["Rango"] . "</td></tr>";
                                echo "<tr><td>Properties</td><td>" . $row["Proprietà"] . "</td></tr>";
                                echo "<tr><td>Effect</td><td>" . $row["Effetto"] . "</td></tr>";
                                echo "<tr><td>ATK</td><td>" . $row["Attacco"] . "</td></tr>";
                                echo "<tr><td>DEF</td><td>" . $row["Difesa"] . "</td></tr>";
                                break;
                            case "Link":
                                echo "<tr><td>Name</td><td>" . $row["Nome"] . "</td></tr>";
                                echo "<tr><td>Attribute</td><td>" . $row["Attributo"] . "</td></tr>";
                                echo "<tr><td>Link Rating</td><td>" . $row["LinkRating"]  . "</td></tr>";
                                echo "<tr><td>Properties</td><td>" . $row["Proprietà"] . "</td></tr>";
                                echo "<tr><td>Effect</td><td>" . $row["Effetto"] . "</td></tr>";
                                echo "<tr><td>ATK</td><td>" . $row["Attacco"] . "</td></tr>";
                                echo "<tr><td>DEF</td><td>" . $row["Difesa"] . "</td></tr>";
                                break;
                            case "Pendulum":
                                echo "<tr><td>Name</td><td>" . $row["Nome"] . "</td></tr>";
                                echo "<tr><td>Attribute</td><td>" . $row["Attributo"] . "</td></tr>";
                                echo "<tr><td>Level</td><td>" . $row["Livello"] . "</td></tr>";
                                echo "<tr><td>Properties</td><td>" . $row["Proprietà"] . "</td></tr>";
                                echo "<tr><td>Pendulum Effect</td><td>" . $row["EffettoPendulum"] . "</td></tr>";
                                echo "<tr><td>Effect</td><td>" . $row["Effetto"] . "</td></tr>";
                                echo "<tr><td>ATK</td><td>" . $row["Attacco"] . "</td></tr>";
                                echo "<tr><td>DEF</td><td>" . $row["Difesa"] . "</td></tr>";
                                break;
                            default:
                                echo "<tr><td>Name</td><td>" . $row["Nome"] . "</td></tr>";
                                echo "<tr><td>Type</td><td>" . $row["TipoMagiaTrappola"] . "</td></tr>";
                                echo "<tr><td>Effect</td><td>" . $row["Effetto"] . "</td></tr>";
                                break;
                        }
                        echo "<tr><td>Quantità</td><td>" . $quantity . "</td></tr>";      
                        echo "</table>";
                        echo "</div>";

                    }
                    else{echo "<div class='results-container'>0 risultati</div>";}
                    mysqli_close($conn);
                }
                
            if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["op"] != 'add'){
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "carte";
                $conn = mysqli_connect($servername,$username,$password,$dbname);
                if (!$conn){
                    die("Connessione fallita: " . mysqli_connect_error());
                }
                        
                $query = "CALL UpdateCardQuantity('" . $_POST["cardSearch"] . "');";
                $result = mysqli_query($conn,$query);
            }
        ?>
    </form>

    <script>
        function updateHiddenInput() {
            var hiddenInput = document.getElementById('hiddeninfo');
        hiddenInput.value = 'remove';
    }
</script>

</body>
</html>

