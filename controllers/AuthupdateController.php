<?php

include_once "ApiController.php";

class AuthupdateController extends ApiController {

    // оновлення даних користувача
    protected function do_post() {

        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'authupdate',
                'service' => 'update_profile',
                'time' => time()
            ],
            'data' => [
                'message' => $_POST
            ]
        ] ;
        
        $filename = "";
        if(! empty($_FILES['updated-avatar'])){
            // файл опціональний, але якщо переданий, то перевіряємо його
            if (
                $_FILES['updated-avatar']['error'] != 0 || 
                $_FILES['updated-avatar']['size'] == 0
            ){
                $result['data']['message'] = "File upload error";
                $this->end_with($result);
            } 
            // перевіряємо тип файлу (розширення) на перелік допустимих
            $ext = pathinfo($_FILES['updated-avatar']['name'], PATHINFO_EXTENSION);
            
            if (strpos(".png.jpg.bmp.jpeg", $ext) === false) {
                $result['data']['message'] = "File type error";
                $this->end_with($result);
            }

            // генеруємо іи'я для збереження, залишаємо розширення
            do {
                $filename = uniqid(). "." . $ext;
            } // перевіряємо чи не потрапили в існуючий файл 
            while (is_file("./wwwroot/avatar/" . $filename)) ;

            // переносимо завантаженний файл до нового розміщення
            move_uploaded_file(
                $_FILES['updated-avatar']['tmp_name'], 
                "./wwwroot/avatar/" . $filename
            );
             
        }

        // валідація ім'я
        if(empty($_POST['updated-name'])) {
            $result['data']['message'] = "Name could not be empty";
            $this->end_with($result);
        }
        else if (preg_match("/\d/i", $_POST['updated-name'])) {
            $result['data']['message'] = "Name can't contain digits";
            $this->end_with($result);        
        }
        else if (preg_match("/[^a-zа-яіЇє'ґ ]/i", $_POST['updated-name'])) {
            $result['data']['message'] = "Name can't contain special characters";
            $this->end_with($result);        
        }
        $name = trim($_POST['updated-name']);

        // валідація email
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
        if(empty($_POST['updated-email'])) {
            $result['data']['message'] = "Email could not be empty";
            $this->end_with($result);
        }
        else if (!preg_match($email_regex, $_POST['updated-email'])) {
            $result['data']['message'] = "Wrong email format!";
            $this->end_with($result);        
        }
        $email = trim($_POST['updated-email']);

        // валідація пароля
        $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#\-_)\^(0?&])[A-Za-z\d@$!\^)(0_%*#?\-&]{8,}$/i";
        if(empty($_POST['updated-password'])) {
            $result['data']['message'] = "Password could not be empty";
            $this->end_with($result);
        }
        else if (!preg_match($password_regex, $_POST['updated-password'])) {
            $result['data']['message'] = "Minimum eight characters, at least one letter, one number and one special character!";
            $this->end_with($result);        
        }
        $password = $_POST['updated-password'];

        // валідація id
        if(empty($_POST['user-id'])) {
            $result['data']['message'] = "Id could not be empty";
            $this->end_with($result);
        }
        $id = $_POST['user-id'];
        
        
        $db = $this->connect_db_or_exit();

        $sql = "UPDATE users 
                SET
                    `email`     = ?, 
                    `name`      = ?, 
                    `password`  = ?, 
                    `avatar`    = ?
                WHERE `id` = ?" ;

        try {
            $prep = $db->prepare($sql);
            
            $prep->execute([
                $email,
                $name,
                md5($password),
                $filename,
                $id
            ]);

        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        
        
        session_start();
        
        $_SESSION['user']['id'] = $id;
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['avatar'] = $filename;       
        $_SESSION['auth-moment'] = time(); 
        
        //$result['data']['message'] = $id;
        $result['status'] = 1;
        //$result['data']['message'] = "Update is successful";
        $this->end_with($result);
    }
}
