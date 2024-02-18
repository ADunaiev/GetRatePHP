<?php

include_once "ApiController.php";

class AuthController extends ApiController {

    protected function do_get() {
        $db = $this->connect_db_or_exit();
        // виконання запитів
        $sql = "CREATE TABLE IF NOT EXISTS Users (
            `id`          CHAR(36)        PRIMARY KEY DEFAULT (UUID()),
            `email`       VARCHAR(128)    NOT NULL,
            `name`        VARCHAR(64)     NOT NULL,
            `password`    CHAR(32)        NOT NULL COMMENT 'Hash of password', 
            `avatar`      VARCHAR(128)    NULL
        )ENGINE = INNODB, DEFAULT CHARSET = utf8mb4";
        try {
            $db->query($sql);
        }
        catch(PDOException $ex) {
            http_response_code(500);
            echo "Connection error: " . $ex->getMessage();
            exit;
        }
        echo "Hello from do_get - Query OK"; 
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
                'message' => ""
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

            


        }

        $result['data']['status'] = 1;
        $result['data']['message'] = "Signup OK";
        $this->end_with($result);

    }
}