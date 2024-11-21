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

        $entityBody = urldecode(file_get_contents('php://input'));

        logToFile($entityBody);

        $db = new mysqli("localhost", "root", "", "vova");

        if(strcmp(substr($entityBody, 10, 4), 'READ') == 0) {
            $CreateDB = "SELECT * FROM contacts";
            $stmt = $db->query($CreateDB);
            $fetch = $stmt->fetch_all();

            $resp = "";

            foreach ($fetch as $key => $value) {
                foreach ($value as $k => $v)
                    $resp .= $v.",";
                $resp .= ";";
            }
            echo $resp;

        } else if(strcmp(substr($entityBody, 10, 6), 'DELETE') == 0) {
            $id = explode('=', $entityBody)[2];
            $stmt = $db->query("DELETE FROM contacts WHERE id=$id;");
        } else if(strcmp(substr($entityBody, 10, 6), 'UPDATE') == 0) {
            $arr_data = explode('&data[]=', $entityBody);
            $stmt = $db->query("UPDATE contacts SET telephone_number='$arr_data[2]', firstname='$arr_data[3]', lastname='$arr_data[4]', DESCRIPTION='$arr_data[5]' WHERE id=$arr_data[1];");
        } else if(strcmp(substr($entityBody, 10, 6), 'CREATE') == 0) {
            $arr_data = explode('&data[]=', $entityBody);
            $stmt = $db->query("INSERT INTO contacts (telephone_number, firstname, lastname, DESCRIPTION) VALUES ('$arr_data[1]', '$arr_data[2]', '$arr_data[3]', '$arr_data[4]');");
        }
    }

?>
