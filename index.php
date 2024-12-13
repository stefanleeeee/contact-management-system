<?php

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if (isset($_SESSION['user_id']))
            echo file_get_contents("./main.html");
        else
            echo file_get_contents("./login.html");
    } else if ($_SERVER["REQUEST_METHOD"] === "POST"){
        $db = new mysqli("localhost", "root", "", "Vova");
        if (isset($_POST['isLogin'])) {
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';
        
            // Проверка входных данных
            if (empty($login) || empty($password)) {
                echo json_encode(['error' => 'Логін та пароль не можуть бути пустими']);
                exit;
            }
        
            // Подготовленный запрос для предотвращения SQL-инъекций
            $stmt = $db->prepare("SELECT id, pass FROM Users WHERE username = ?");
            if (!$stmt) {
                echo json_encode(['error' => 'Помилка створення запиту до БД']);
                exit;
            }
        
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
        
                // Сравнение пароля
                if (password_verify($password, $user['pass'])) {
                    $_SESSION['user_id'] = $user['id']; // Сохраняем user_id в сессии
                    echo json_encode(['status' => 'success', 'id' => $user['id']]);
                    exit;
                }
            }
        
            echo json_encode(['error' => 'Невірний логін або пароль']);
            exit;
        } else if (isset($_POST['isRegister'])) {
            $login = $_POST['login'] ?? '';
            $password = $_POST['password'] ?? '';
        
            if (empty($login) || empty($password)) {
                echo json_encode(['error' => 'Логін та пароль не можуть бути пустими']);
                exit;
            }
        
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
            $stmt = $db->prepare("INSERT INTO Users (username, pass) VALUES (?, ?)");
            if (!$stmt) {
                echo json_encode(['error' => 'Ошибка подготовки запроса']);
                exit;
            }
        
            $stmt->bind_param("ss", $login, $hashedPassword);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['error' => 'Ошибка регистрации']);
            }
        
            exit;
        } else if (isset($_SESSION["user_id"])) {
            $user_id = $_SESSION["user_id"];
        
            if(isset($_POST["isExit"])) {
                session_unset();
                session_destroy();
                echo "succes";
                exit;
            } else if (isset($_POST["isCreate"])) {
                $telephone_number = $db->real_escape_string($_POST['phone']);
                $firstname = $db->real_escape_string($_POST['firstname']);
                $lastname = $db->real_escape_string($_POST['lastname']);
                $description = $db->real_escape_string($_POST['description']);
        
                $uploadDir = 'uploads/';
                if (isset($_FILES['avatar'])) {
                    $fileTmpPath = $_FILES['avatar']['tmp_name'];
                    $fileName = uniqid() . basename(str_replace(" ", "", $_FILES['avatar']['name']));
                    $uploadFilePath = $uploadDir . $fileName;
        
                    if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                        $avatar = $fileName;
                    } else {
                        echo json_encode(["success" => false, "message" => "Failed to upload avatar."]);
                        exit;
                    }
                } else {
                    $avatar = null;
                }
        
                $sql = "INSERT INTO Contacts (user_id, telephone_number, firstname, lastname, description, avatar) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("isssss", $user_id, $telephone_number, $firstname, $lastname, $description, $avatar);
        
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "Contact created successfully."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to create contact."]);
                }
        
                $stmt->close();
        
            } else if (isset($_POST["isRead"])) {
                $sql = "SELECT id, telephone_number, firstname, lastname, description, avatar FROM Contacts WHERE user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $user_id);
        
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    $contacts = $result->fetch_all(MYSQLI_ASSOC);
                    echo json_encode(["success" => true, "contacts" => $contacts]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to read contacts."]);
                }
        
                $stmt->close();
        
            } else if (isset($_POST["isUpdate"])) {
                $contact_id = (int)$_POST['id'];
                $telephone_number = $db->real_escape_string($_POST['phone']);
                $firstname = $db->real_escape_string($_POST['firstname']);
                $lastname = $db->real_escape_string($_POST['lastname']);
                $description = $db->real_escape_string($_POST['description']);
        
                $uploadDir = 'uploads/';
                if (isset($_FILES['avatar'])) {
                    $fileTmpPath = $_FILES['avatar']['tmp_name'];
                    $fileName = uniqid() . basename(str_replace(" ", "", $_FILES['avatar']['name']));
                    $uploadFilePath = $uploadDir . $fileName;
        
                    if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                        $avatar = $fileName;
                    } else {
                        echo json_encode(["success" => false, "message" => "Failed to upload avatar."]);
                        exit;
                    }
                } else {
                    $avatar = null;
                }
        
                $sql = "UPDATE Contacts SET telephone_number = ?, firstname = ?, lastname = ?, description = ?, avatar = COALESCE(?, avatar) WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("sssssii", $telephone_number, $firstname, $lastname, $description, $avatar, $contact_id, $user_id);
        
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    echo json_encode(["success" => true, "message" => "Contact updated successfully. $avatar"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to update contact."]);
                }
        
                $stmt->close();
        
            } else if (isset($_POST["isDelete"])) {
                $contact_id = (int)$_POST['id'];
        
                $sql = "DELETE FROM Contacts WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ii", $contact_id, $user_id);
        
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    echo json_encode(["success" => true, "message" => "Contact deleted successfully."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to delete contact."]);
                }
        
                $stmt->close();
            }
        } else {
            echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        }
    }

?>