<?php
    $tryErrorFile = 'ph_pray_post_error.txt';
    $errorFile = 'upload_error.txt';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'image/') !== false)
    {
        try
        {
            $imageData = file_get_contents('php://input');
            if ($imageData !== false)
            {
                $uploadDirectory = 'images\\';
            
                $filename = uniqid('image_') . '.jpg';
                $filePath = $uploadDirectory . $filename;
                if (file_put_contents($filePath, $imageData) !== false)
                {
                    $command1 = 'C:/xampp/htdocs/GooglePray/.venv/Scripts/activate.bat';
                    $command2 = 'GooglePray\\main.py "' . $filePath . '"';
                    $output = exec($command1 . ' && ' . $command2);

                    $filename = "temp.txt";
                    $file = fopen($filename, 'r');
            
                    $string = "";
            
                    if ($file)
                    {
                        $string = fgets($file);
                        fclose($file);
                        scan($string);
                        unlink($filename);
                    }
                    else
                    {
                        $errorF = fopen($errorFile, 'a');
                        if($errorF)
                        {
                            fwrite($errorF,"Errore lettura file\n");
                            fclose($errorF);
                        }
                    }
                }
                else
                {
                    $errorF = fopen($errorFile, 'a');
                    if($errorF)
                    {
                        fwrite($errorF,"Errore salvataggio immagine\n");
                        fclose($errorF);
                    }
                }
            }
            else
            {
                $errorF = fopen($errorFile, 'a');
                if($errorF)
                {
                    fwrite($errorF,"Nessuna immagine ricevuta\n");
                    fclose($errorF);
                }
            }
        }
        catch (Throwable $th)
        {
            $errorF = fopen($tryErrorFile, 'a');
            if($errorF)
            {
                fwrite($errorF,$th->getMessage());
                fwrite($errorF,$th->getTraceAsString());
                fwrite($errorF,"\n\n");
                fclose($errorF);
            }
        }
    }
    else
    {
        $errorF = fopen($errorFile, 'a');
        if($errorF)
        {
            fwrite($errorF,"Richiesta Invalida");
            fclose($errorF);
        }
    }

    function scan($name)
    {
        $filename = "match.txt";
        $file = fopen($filename, 'w');
        if(is_null($name))
        {
            $name = "";
        }
        $servername = "localhost";
        $username = "root";
        $password = "";
        $db_name = "carte";
        $foundMatch = "-1";
        $conn = mysqli_connect($servername,$username,$password,$db_name);
        if (!$conn)
        {
            die("Connessione fallita: " . mysqli_connect_error());
        }
        $query = "SELECT Nome FROM listacarte WHERE (Nome= ? or Name= ? )";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt,"ss",$name, $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);


        mysqli_close($conn);
        if ($row["Nome"] !== NULL)
        {
            $conn = mysqli_connect($servername,$username,$password,$db_name);
            $insertion_query = 'INSERT INTO carteinpossesso (Nome, Quantità) VALUES (?, "1") ON DUPLICATE KEY UPDATE Quantità = Quantità + 1';
            $stmt = mysqli_prepare($conn, $insertion_query);
            mysqli_stmt_bind_param($stmt,"s",$row["Nome"]);
            mysqli_stmt_execute($stmt);
            mysqli_close($conn);
            $foundMatch = "    Trovato:    ";
        }
        else
        {
            $foundMatch = "  Non trovato:  ";
        }
        if (strlen($name) > 16)
        {
            $name = substr($name, 0, 13);
            $name .= "...";
        }
        else
        {
            for($i = 0; $i < 16 - strlen($name); $i++)
            {
                $name .= " ";
            }
        }
        if ($file)
        {
            fwrite($file, $foundMatch . $name);
            fclose($file);
        }
    }
?>
