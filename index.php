<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo file_get_contents('main.html');
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
