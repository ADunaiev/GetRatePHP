<?php

include_once "ApiController.php";

class AuthController extends ApiController {

    protected function do_get() {

        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'auth',
                'service' => 'authentication',
                'time' => time()
            ],
            'data' => [
                'message' => $_GET
            ]
        ] ;

        if(empty($_GET['email'])) {
            $result['data']['message'] = "Missing required parameter: 'email'";
            $this->end_with($result);
        }
        $email = trim($_GET['email']);

        if(empty($_GET['password'])) {
            $result['data']['message'] = "Missing required parameter: 'password'";
            $this->end_with($result);
        }        
        $password = $_GET['password'];

        $db = $this->connect_db_or_exit();

        $sql = "SELECT * FROM users u
            WHERE u.email = ? AND u.password = ?" ;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $email,
                md5($password)
            ]);
            $search_result = $prep->fetch();

            if ($search_result === false) {
                $result['data']['message'] = "Credentials rejected!" ;
                $this->end_with($result);
            }
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        
        session_start();
        $_SESSION['user'] = $search_result;
        $result['data']['message'] = $search_result; 
        $_SESSION['auth-moment'] = time();

        $result['status'] = 1;
        $this->end_with($result);
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
                $_FILES['user-avatar']['tmp_name'], 
                "./wwwroot/avatar/" . $filename
            );
            
            $result['data']['avatar'] = $filename;    
        }

        $db = $this->connect_db_or_exit();
        $user_email = $_POST['user-email'];
        $user_name = $_POST['user-name'];
        $user_password = md5( $_POST['user-password']);
        $user_avatar = $result['data']['avatar'] ;
        $sql = "INSERT INTO users 
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


    protected function do_delete() {
        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'auth',
                'service' => 'signout',
                'time' => time()
            ],
            'data' => [
                'message' => "Session is not finished",
            ]
            ];
        
        session_start();
        session_unset();
        if(session_destroy()) {
            $result['status'] = 1;
            $result['data']['message'] = "Session is finished successfully";
        }
        $this->end_with($result);
    }




}