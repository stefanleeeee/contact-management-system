<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo file_get_contents('main.html');
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operation'])) {
        $db = new mysqli("localhost", "root", "", "vova");

        if($_POST['operation'] === "READ") {
            // Операція READ
            $fetch = $db->query("SELECT * FROM contacts")->fetch_all();
            echo json_encode($fetch);
        } else if($_POST['operation'] === "DELETE") {
            // Операція DELETE
            $id = $_POST["id"];
            $stmt = $db->query("DELETE FROM contacts WHERE id=$id;");
        } else if($_POST['operation'] === "UPDATE") {
            // Операція UPDATE
            $arr_data = [
                $_POST["phone"], 
                $_POST["firstname"], 
                $_POST["lastname"], 
                $_POST["description"], 
                $_POST["id"]
            ];
            $stmt = $db->query("UPDATE contacts SET telephone_number='$arr_data[0]', firstname='$arr_data[1]', lastname='$arr_data[2]', DESCRIPTION='$arr_data[3]' WHERE id=$arr_data[4];");
        } else if($_POST['operation'] === "CREATE") {
            // Операція CREATE
            $arr_data = [
                $_POST["phone"], 
                $_POST["firstname"], 
                $_POST["lastname"], 
                $_POST["description"]
            ];
            $stmt = $db->query("INSERT INTO contacts (telephone_number, firstname, lastname, DESCRIPTION) VALUES ('$arr_data[0]', '$arr_data[1]', '$arr_data[2]', '$arr_data[3]');");
        }
    }

?>
