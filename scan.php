<?php
    if($_SERVER["REQUEST_METHOD"] == "GET")
    {
        $filename = "match.txt";
        $file = fopen($filename, 'r');

        $string = "";

        if ($file)
        {
            $string = fgets($file);
            fclose($file);
        }
        else
        {
            $string = "Errore Lettura File";
        }
        unlink($filename);
        echo $string;
    }
?>