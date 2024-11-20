<?php

    function logToFile($data) : void {
        //Something to write to txt log
        $log  = date("F j, Y, g:i a")." ".$data.PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo file_get_contents('main.html');
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $entityBody = file_get_contents('php://input');

        logToFile($entityBody);

        $db = new mysqli("localhost", "root", "", "vova");

        
        $CreateDB = "SELECT * FROM contacts";
        $stmt = $db->query($CreateDB);
        $fetch = $stmt->fetch_all();

        foreach ($fetch as $key => $value) {
            foreach ($value as $k => $v) {
                echo $v." ";
            }
            echo "<br>";
        }
    }

?>
