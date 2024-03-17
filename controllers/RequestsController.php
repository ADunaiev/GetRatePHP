<?php

include_once "ApiController.php";
include_once "./model/DataWorker.php";

class RequestsController extends ApiController {

    protected function do_post() {
        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'requests',
                'service' => 'request_receipt',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'avatar' => "",
                'name' => "",
            ],
        ];

        $db = (new DataWorker())->connect_db_or_exit();
        $user_id = $_POST['user-id'];
        $received = date('Y-m-d H:i:s');
        $start_point = $_POST['start-point'];
        $end_point = $_POST['end-point'];
        $cargo_id = $_POST['cargo'];
        $cargo_gw = $_POST['cargo-gw'];

        if ($user_id == "") {
            $result['data']['message'] = "Error. You are not signed in!";
            $this->end_with($result);
        }
        if ($start_point == "") {
            $result['data']['message'] = "Error. Start point could not be empty!";
            $this->end_with($result);
        }
        if ($end_point == "") {
            $result['data']['message'] = "Error. End point could not be empty!";
            $this->end_with($result);
        }
        else if ($start_point == $end_point) {
            $result['data']['message'] = "Error. End point could not be equal to start point!";
            $this->end_with($result);
        }
        if ($cargo_id == "") {
            $result['data']['message'] = "Error. Cargo could not be empty!";
            $this->end_with($result);
        }
        if ($cargo_gw == "") {
            $result['data']['message'] = "Error. Cargo gross weight could not be empty!";
            $this->end_with($result);
        }

        $sql = "INSERT INTO requests 
                    (`start_point_name`, `end_point_name`, `cargo_id`, `cargo_gw`, `user_id`, `received`)
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $start_point,
                $end_point,
                $cargo_id,
                $cargo_gw,
                $user_id,
                $received
            ]);
        }
        catch(PDOException $ex) {
            http_response_code(500);
            $result['status'] = 0;
            $result['data']['message'] = "Error! Request was not added. Please send a message to adunaev@me.com";
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        // запускаємо пошук прямих маршрутів
        $routes = $this->find_simple_routes($start_point, $end_point);



        // шукаємо назви типів транспорту замість кодів
        foreach ( $routes as &$route) {
            $route['transport_type'] = 
                $this->get_transport_type_name_by_trinity_code(
                    $route['transport_type_trinity_code']
                );
            $route['image'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['transport_type_trinity_code']
                );
        }

        // запускаємо пошук 2шагових маршрутів
        $two_items_routes = $this->find_2_item_routes($start_point, $end_point);

        // шукаємо назви типів транспорту замість кодів
        foreach ( $two_items_routes as &$route) {
            $route['image_first'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['first_transport']
                );
            $route['image_second'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['second_transport']
                );
        }

        // запускаємо пошук 3шагових маршрутів
        $three_items_routes = $this->find_3_item_routes($start_point, $end_point);

        // шукаємо назви типів транспорту замість кодів
        foreach ( $three_items_routes as &$route) {
            $route['image_first'] = 
                $this->get_transport_image_by_transport_name(
                    $route['1st_transport']
                );
            $route['image_second'] = 
            $this->get_transport_image_by_transport_name(
                $route['2nd_transport']
            );
            $route['image_third'] = 
            $this->get_transport_image_by_transport_name(
                $route['3rd_transport']
            );
        }


        session_start();
            
        // передаємо знайдені маршрути в сесію
        $_SESSION['start-point'] = $start_point;
        $_SESSION['end-point'] = $end_point;
        $_SESSION['cargo-id'] = $cargo_id;
        $_SESSION['cargo-gw'] = $cargo_gw;
        $_SESSION['found_1item_routes'] = $routes;
        $_SESSION['found_2item_routes'] = $two_items_routes;
        $_SESSION['found_3item_routes'] = $three_items_routes;

        $result['status'] = 1;
        $result['data']['message'] = "Request saved successfully! Start:";

        $this->end_with($result);
    }

    // пошук прямих маршрутів
    protected function find_simple_routes ($start_point_name, $end_point_name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT transport_type_trinity_code, start_point_name, end_point_name
                FROM routes
                WHERE 
                    start_point_name = ? AND 
                    end_point_name = ?
                GROUP BY  transport_type_trinity_code
                ";

        $result = null;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $start_point_name,
                $end_point_name
            ]);
            $result = $prep->fetchAll();
        }
        catch(PDOException $ex) {
            http_response_code(500);
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

    // пошук маршрутів з 2-х відрізков
    protected function find_2_item_routes ($start_point_name, $end_point_name) {
        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "select \n"
                . "    r1.transport_type_trinity_code as first_transport, \n"
                . "    r1.start_point_name, \n"
                . "    r1.end_point_name as middle_point,\n"
                . "    r2.transport_type_trinity_code as second_transport,\n"
                . "    r2.end_point_name\n"
                . "from \n"
                . "	routes as r1,\n"
                . "    (SELECT \n"
                . "     	r.transport_type_trinity_code, \n"
                . "     	r.start_point_name,\n"
                . "     	r.end_point_name\n"
                . "     FROM routes r\n"
                . "     WHERE 	r.end_point_name = ? AND \n"
                . "     		r.start_point_name != r.end_point_name \n"
                . "    ) as r2\n"
                . "where 	r1.start_point_name = ? AND \n"
                . "		r1.start_point_name != r1.end_point_name AND\n"
                . "        r1.end_point_name = r2.start_point_name \n"
                . "group by \n"
                . "	first_transport,\n"
                . "    middle_point,\n"
                . "    second_transport;";           

        $result = null;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $end_point_name,
                $start_point_name
            ]);
            $result = $prep->fetchAll();
        }
        catch(PDOException $ex) {
            http_response_code(500);
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

    // пошук маршрутів з 3-х відрізков
    protected function find_3_item_routes ($start_point_name, $end_point_name) {
        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "select \n"
                . "    t.name as 1st_transport, \n"
                . "    r1.start_point_name, \n"         
                . "    r1.end_point_name as middle_point1,\n"         
                . "    r2.name as 2nd_transport,\n"        
                . "    r2.end_point_name as middle_point2,\n"  
                . "    r3.name as 3rd_transport,\n"        
                . "    r3.end_point_name\n"         
                . "from \n"          
                . "	   routes as r1,\n"           
                . "    transport_type as t,\n"           
                . "    (SELECT \n"           
                . "     	t2.name, \n"         
                . "     	r.start_point_name,\n"           
                . "     	r.end_point_name\n"          
                . "     FROM routes r, transport_type t2\n"         
                . "     WHERE 	\n"         
                . "     		r.start_point_name != r.end_point_name AND\n"         
                . "     		r.transport_type_trinity_code = t2.trinity_code\n"          
                . "    ) r2,\n"
                . "    (SELECT \n"           
                . "     	t3.name, \n"         
                . "     	r.start_point_name,\n"           
                . "     	r.end_point_name\n"          
                . "     FROM routes r, transport_type t3\n"         
                . "     WHERE 	r.end_point_name = ? AND \n"         
                . "     		r.start_point_name != r.end_point_name AND\n"         
                . "     		r.transport_type_trinity_code = t3.trinity_code\n"          
                . "    ) r3\n"            
                . "\n"          
                . "where 	r1.start_point_name = ? AND \n"            
                . "		    r1.start_point_name != r1.end_point_name AND\n"           
                . "         r1.end_point_name = r2.start_point_name AND\n"          
                . "         r1.transport_type_trinity_code = t.trinity_code AND\n"
                . "         r2.end_point_name = r3.start_point_name\n"             
                . "group by \n"           
                . "	    1st_transport,\n"           
                . "     middle_point1,\n"          
                . "     2nd_transport;\n"
                . "     middle_point2,\n"          
                . "     3rd_transport;\n"
                . "     end_point_name;\n";
                

        $result = null;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $end_point_name,
                $start_point_name
            ]);
            $result = $prep->fetchAll();
        }
        catch(PDOException $ex) {
            http_response_code(500);
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

    // пошук типу транспорту по коду трініті
    protected function get_transport_type_name_by_trinity_code($ttype_id) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT `name` FROM transport_type
        WHERE trinity_code = ?";

        $result = "";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $ttype_id
            ]);
            $result = $prep->fetch()[0];
        }
        catch(PDOException $ex) {
            http_response_code(500);
            $result = "Error! City was not found.";
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

    // пошук зображення типу транспорту по коду трініті
    protected function get_transport_image_by_trinity_code($ttype_id) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT `image` FROM transport_type
        WHERE trinity_code = ?";

        $result = "";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $ttype_id
            ]);
            $result = $prep->fetch()[0];
        }
        catch(PDOException $ex) {
            http_response_code(500);
            $result = "Error! City was not found.";
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

    // пошук зображення типу транспорту по назві транспорту
    protected function get_transport_image_by_transport_name($ttype_name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT `image` FROM transport_type
        WHERE `name` = ?
        LIMIT 1";

        $result = "";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $ttype_name
            ]);
            $result = $prep->fetch();
        }
        catch(PDOException $ex) {
            http_response_code(500);
            $result = "Error! City was not found.";
            echo "Connection error: " . $ex->getMessage();
            exit;
        } 

        return $result;
    }

}