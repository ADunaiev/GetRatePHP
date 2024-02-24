<?php

class ApiController {
    public function serve() {
        $method = strtolower( $_SERVER['REQUEST_METHOD'] ); // метод запиту
        $action = "do_{$method}" ;
        // чи визначений у даному об'єкті метод з іменем $action (do_get)
        if(method_exists($this, $action)) {
            // якщо визначений, то викликаємо 
            $this->$action() ; // у PHP $this - обов'язково 
            // !! назву методу можна передати через змінну
            // $this->$action() == $this.do_get()
        }
        else {
            http_response_code(405);
            echo "Method not allowed";
        }
    }

    protected function connect_db_or_exit() {
        try {
            $settings = parse_ini_file( './config.ini', true);

            return new PDO(
                $settings['db']['mysql'], 
                $settings['db']['user'], 
                $settings['db']['password'], [
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            }
        
        catch (PDOException $ex) {
            http_response_code(500);
            echo "Connection error: " . $ex->getMessage();
            exit;
        }
    }

    protected function end_with($result) {
        header('Content-Type: application/json');

        echo json_encode($result); 
        exit;
    }

}