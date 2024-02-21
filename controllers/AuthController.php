<?php

include_once "ApiController.php";

class AuthController extends ApiController {

    protected function do_get() {

        echo "Hello from do_get" . $_GET['email']; 
    }

    /**
     * Реєстрація нового користувача (Create)
     */

    protected function do_post() {
        /*
        $result = [
            'get' => $_GET,
            'post' => $_POST,
            'files' => $_FILES,
        ];*/
        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'auth',
                'service' => 'signup',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'avatar' => "",
                'name' => "",
            ],
        ];
        if(! empty($_FILES['user-avatar'])){
            // файл опціональний, але якщо переданий, то перевіряємо його
            if (
                $_FILES['user-avatar']['error'] != 0 || 
                $_FILES['user-avatar']['size'] == 0
            ){
                $result['data']['message'] = "File upload error";
                $this->end_with($result);
            } 
            // перевіряємо тип файлу (розширення) на перелік допустимих
            $ext = pathinfo($_FILES['user-avatar']['name'], PATHINFO_EXTENSION);
            
            if (strpos(".png.jpg.bmp", $ext) === false) {
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
                $_FILES['user-avatar']['tmp_name'], 
                "./wwwroot/avatar/" . $filename
            );
            
            $result['data']['avatar'] = $filename;


      
            
        }

        $db = $this->connect_db_or_exit();
        $user_email = $_POST['user-email'];
        $user_name = $_POST['user-name'];
        $user_password = password_hash( $_POST['user-password'], PASSWORD_BCRYPT);
        $user_avatar = $result['data']['avatar'] ;
        $sql = "INSERT INTO Users 
            (`email`, `name`, `password`, `avatar`)
            VALUES 
            (   
                '$user_email',
                '$user_name',
                '$user_password',
                '$user_avatar' 
            )";
        
        try {
            $db->query($sql);
        }
        catch(PDOException $ex) {
            http_response_code(500);
            $result['status'] = 0;
            $result['data']['message'] = "Error! User was not added. Please send a message to adunaev@me.com";
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        $result['status'] = 1;
        $result['data']['message'] = "User is added successfully!";
        
        $result['data']['name'] = $_POST['user-name'];
        $this->end_with($result);

    }
}