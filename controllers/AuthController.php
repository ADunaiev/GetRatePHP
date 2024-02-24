<?php

include_once "ApiController.php";
include_once "MySQLSessionHandler.php";

class AuthController extends ApiController {

    protected function do_get() {

        $auth_result = [
            'status' => 0,
            'meta' => [
                'api' => 'auth',
                'service' => 'signin',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'avatar' => "",
                'name' => "",
                'email' => "",
                'id' => "",
                'is_session' => "",
                'session-id' => "",
            ]
        ] ;

        $search_email = $_GET['email'];
        $search_password = $_GET['password'];

        $db = $this->connect_db_or_exit();

        $sql = "SELECT * FROM Users 
            WHERE `email` = '$search_email'" ;

        try {
            $user_search = $db->prepare($sql);
            $user_search->execute();
            $search_result = $user_search->fetchAll(PDO::FETCH_ASSOC);

            if ($search_result) {
                $hash = $search_result[0]['password'] ;

                if (password_verify($search_password, $hash))
                {
                    $auth_result['data']['message'] = 'Authorisation is successful!';
                    $auth_result['status'] = 1;
                    $auth_result['data']['id'] = $search_result[0]['id'] ;
                    $auth_result['data']['name'] = $search_result[0]['name'] ;
                    $auth_result['data']['email'] = $search_result[0]['email'] ;
                    $auth_result['data']['avatar'] = $search_result[0]['avatar'] ;

                    // цей обробник не працює як слід...
                    //$objSessionHandler = new MySQLSessionHandler();
                    //session_set_save_handler($objSessionHandler, true);
                    session_start();
                    $_SESSION['user-id'] = $auth_result['data']['id'];
                    $_SESSION['user-name'] = $search_result[0]['name'] ;
                    $_SESSION['user-email'] = $search_result[0]['email'] ;
                    $_SESSION['user-avatar'] = $search_result[0]['avatar'] ;

                    
                }
                else {
                    $auth_result['data']['message'] = "Credentials rejected!" ;
                    http_response_code(401);
                }
            } else {
                $auth_result['data']['message'] = "Credentials rejected!" ;
                http_response_code(401);
            }


        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $auth_result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        
        
        $this->end_with($auth_result);

        //echo "Hello from do_get" . $_GET['email']; 
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