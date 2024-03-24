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
        $withRates = $_POST['with-rates'];
        $sort_by = $_POST['sort-by'];

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

        // формуємо масив для передачі результата
        $all_routes_and_rates = array();
        $dataWorker = new DataWorker();
        
        // заповнюємо результуючий масив прямими маршрутами
        foreach ( $routes as &$route) {
            $route['transport_type'] = 
                $this->get_transport_type_name_by_trinity_code(
                    $route['transport_type_trinity_code']
                );
            $route['image'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['transport_type_trinity_code']
                );

            if($sort_by == "time") {
                $route_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['start_point_name'],
                    $route['end_point_name'],
                    $route['transport_type_trinity_code']
                ); 
            }
            else {
                $route_rates = $dataWorker->get_route_rates(
                    $route['start_point_name'],
                    $route['end_point_name'],
                    $route['transport_type_trinity_code']
                ); 
            }


            $units_quantity = $route_rates != null ? ceil( $cargo_gw / $route_rates[0]['unit_payload']) : 0;
            $route_rate_amount = $route_rates != null ? $route_rates[0]['amount'] : 0;
            $route_transit_time = $route_rates != null ? $route_rates[0]['transit_time'] : 0;
            
            $route_item = array(
                "start_point_name" => $route['start_point_name'],
                "end_point_name" => $route['end_point_name'],
                "transport_type_name" => $route['transport_type'],
                "transport_type_img" => $route['image'],
                "transport_type_trinity_code" => $route['transport_type_trinity_code'],
                "rates" => $route_rates,
                "units_quantity" => $units_quantity,
                "route_transit_time" => $route_transit_time,
                "route_rate_amount" => $route_rate_amount,
                "route_rate_currency" => $route_rates != null ? $route_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity * $route_rate_amount
            );

            $with_rate =  $route_rate_amount > 0 ? true : false;

            $new_route = array(
                "routes" => array($route_item),
                "total_sum" => $route_rate_amount * $units_quantity,
                "total_transit_time" => $route_transit_time,
                "with_rate" => $with_rate
                );

            array_push($all_routes_and_rates, $new_route);
            
        }

        // запускаємо пошук 2шагових маршрутів
        $two_items_routes = $this->find_2_item_routes($start_point, $end_point);

        // додаємо в результуючий масив дані 2хшагових маршрутів
        foreach ( $two_items_routes as &$route) {

            // заповнюємо перший відрізок
            $route['image_first'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['first_transport']
                );

            $route['first_transport_name'] = 
            $this->get_transport_type_name_by_trinity_code(
                $route['first_transport']
            );

            if ($sort_by == "time") {
                $route1_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['start_point_name'],
                    $route['middle_point'],
                    $route['first_transport']
                ); 
            }
            else {
                $route1_rates = $dataWorker->get_route_rates(
                    $route['start_point_name'],
                    $route['middle_point'],
                    $route['first_transport']
                ); 
            }


            $units_quantity1 = $route1_rates != null ? ceil( $cargo_gw / $route1_rates[0]['unit_payload']) : 0;
            $route1_rate_amount = $route1_rates != null ? $route1_rates[0]['amount'] : 0;
            $route1_transit_time = $route1_rates != null ? $route1_rates[0]['transit_time'] : 0;
            
            $route1_item = array(
                "start_point_name" => $route['start_point_name'],
                "end_point_name" => $route['middle_point'],
                "transport_type_name" => $route['first_transport_name'],
                "transport_type_img" => $route['image_first'],
                "transport_type_trinity_code" => $route['first_transport'],
                "rates" => $route1_rates,
                "units_quantity" => $units_quantity1,
                "route_transit_time" => $route1_transit_time,
                "route_rate_amount" => $route1_rate_amount,
                "route_rate_currency" => $route1_rates != null ? $route1_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity1 * $route1_rate_amount
            );

            // заповнюємо другий відрізок
            $route['image_second'] = 
            $this->get_transport_image_by_trinity_code(
                $route['second_transport']
            );

            $route['second_transport_name'] = 
            $this->get_transport_type_name_by_trinity_code(
                $route['second_transport']
            );

            if ($sort_by == "time") {
                $route2_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['middle_point'],
                    $route['end_point_name'],
                    $route['second_transport']
                ); 
    
            }
            else {
                $route2_rates = $dataWorker->get_route_rates(
                    $route['middle_point'],
                    $route['end_point_name'],
                    $route['second_transport']
                ); 
            }

            $units_quantity2 = $route2_rates != null ? ceil( $cargo_gw / $route2_rates[0]['unit_payload']) : 0;
            $route2_rate_amount = $route2_rates != null ? $route2_rates[0]['amount'] : 0;
            $route2_transit_time = $route2_rates != null ? $route2_rates[0]['transit_time'] : 0;
            
            $route2_item = array(
                "start_point_name" => $route['middle_point'],
                "end_point_name" => $route['end_point_name'],
                "transport_type_name" => $route['second_transport_name'],
                "transport_type_img" => $route['image_second'],
                "transport_type_trinity_code" => $route['second_transport'],
                "rates" => $route2_rates,
                "units_quantity" => $units_quantity2,
                "route_transit_time" => $route2_transit_time,
                "route_rate_amount" => $route2_rate_amount,
                "route_rate_currency" => $route2_rates != null ? $route2_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity2 * $route2_rate_amount
            );

            $with_rate = ($route1_rate_amount > 0 && $route2_rate_amount > 0) ? true : false;
            // поєднуємо результати
            $new_route = array("routes" => array($route1_item, $route2_item));
            $new_route['total_sum'] = $route1_rate_amount * $units_quantity1 + $route2_rate_amount * $units_quantity2;
            $new_route['total_transit_time'] = $route1_transit_time + $route2_transit_time;
            $new_route['with_rate'] = $with_rate;

            array_push($all_routes_and_rates, $new_route);
        }

        // запускаємо пошук 3шагових маршрутів
        $three_items_routes = $this->find_3_item_routes($start_point, $end_point);


        //  додаємо в результуючий масив дані 3хшагових маршрутів
        foreach ( $three_items_routes as &$route) {
            // заповнюємо перший відрізок
            $route['image_first'] = 
                $this->get_transport_image_by_trinity_code(
                    $route['1st_transport']
            );

            $route['first_transport_name'] = 
            $this->get_transport_type_name_by_trinity_code(
                $route['1st_transport']
            );

            if ($sort_by == "time") {
                $route1_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['start_point_name'],
                    $route['middle_point1'],
                    $route['1st_transport']
                ); 
            }
            else {
                $route1_rates = $dataWorker->get_route_rates(
                    $route['start_point_name'],
                    $route['middle_point1'],
                    $route['1st_transport']
                ); 
            }


            $units_quantity1 = $route1_rates != null ? ceil( $cargo_gw / $route1_rates[0]['unit_payload']) : 0;
            $route1_rate_amount = $route1_rates != null ? $route1_rates[0]['amount'] : 0;
            $route1_transit_time = $route1_rates != null ? $route1_rates[0]['transit_time'] : 0;
            
            $route1_item = array(
                "start_point_name" => $route['start_point_name'],
                "end_point_name" => $route['middle_point1'],
                "transport_type_name" => $route['first_transport_name'],
                "transport_type_img" => $route['image_first'],
                "transport_type_trinity_code" => $route['1st_transport'],
                "rates" => $route1_rates,
                "units_quantity" => $units_quantity1,
                "route_transit_time" => $route1_transit_time,
                "route_rate_amount" => $route1_rate_amount,
                "route_rate_currency" => $route1_rates != null ? $route1_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity1 * $route1_rate_amount
            );

            // заповнюємо другий відрізок
            $route['image_second'] = 
            $this->get_transport_image_by_trinity_code(
                $route['2nd_transport']
            );

            $route['second_transport_name'] = 
            $this->get_transport_type_name_by_trinity_code(
                $route['2nd_transport']
            );

            if ($sort_by == "time") {
                $route2_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['middle_point1'],
                    $route['middle_point2'],
                    $route['2nd_transport']
                ); 
            }
            else {
                $route2_rates = $dataWorker->get_route_rates(
                    $route['middle_point1'],
                    $route['middle_point2'],
                    $route['2nd_transport']
                ); 
            }


            $units_quantity2 = $route2_rates != null ? ceil( $cargo_gw / $route2_rates[0]['unit_payload']) : 0;
            $route2_rate_amount = $route2_rates != null ? $route2_rates[0]['amount'] : 0;
            $route2_transit_time = $route2_rates != null ? $route2_rates[0]['transit_time'] : 0;
            
            $route2_item = array(
                "start_point_name" => $route['middle_point1'],
                "end_point_name" => $route['middle_point2'],
                "transport_type_name" => $route['second_transport_name'],
                "transport_type_img" => $route['image_second'],
                "transport_type_trinity_code" => $route['2nd_transport'],
                "rates" => $route2_rates,
                "units_quantity" => $units_quantity2,
                "route_transit_time" => $route2_transit_time,
                "route_rate_amount" => $route2_rate_amount,
                "route_rate_currency" => $route2_rates != null ? $route2_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity2 * $route2_rate_amount
            );

            // заповнюємо третій відрізок
            $route['image_third'] = 
            $this->get_transport_image_by_trinity_code(
                $route['3rd_transport']
            );

            $route['third_transport_name'] = 
            $this->get_transport_type_name_by_trinity_code(
                $route['3rd_transport']
            );

            if ($sort_by == "time") {
                $route3_rates = $dataWorker->get_route_rates_by_transit_time(
                    $route['middle_point2'],
                    $route['end_point_name'],
                    $route['3rd_transport']
                ); 
            }
            else {
                $route3_rates = $dataWorker->get_route_rates(
                    $route['middle_point2'],
                    $route['end_point_name'],
                    $route['3rd_transport']
                ); 
            }


            $units_quantity3 = $route3_rates != null ? ceil( $cargo_gw / $route3_rates[0]['unit_payload']) : 0;
            $route3_rate_amount = $route3_rates != null ? $route3_rates[0]['amount'] : 0;
            $route3_transit_time = $route3_rates != null ? $route3_rates[0]['transit_time'] : 0;
            
            $route3_item = array(
                "start_point_name" => $route['middle_point2'],
                "end_point_name" => $route['end_point_name'],
                "transport_type_name" => $route['third_transport_name'],
                "transport_type_img" => $route['image_third'],
                "transport_type_trinity_code" => $route['3rd_transport'],
                "rates" => $route3_rates,
                "units_quantity" => $units_quantity3,
                "route_transit_time" => $route3_transit_time,
                "route_rate_amount" => $route3_rate_amount,
                "route_rate_currency" => $route3_rates != null ? $route3_rates[0]['currency'] : 0,
                "route_sum" => $units_quantity3 * $route3_rate_amount
            );

            $with_rate = ($route1_rate_amount > 0 && $route2_rate_amount > 0 && $route3_rate_amount > 0) ? true : false;

            // поєднуємо результати
            $new_route = array("routes" => array($route1_item, $route2_item, $route3_item));
            $new_route['total_sum'] = $route1_rate_amount * $units_quantity1 + 
                                      $route2_rate_amount * $units_quantity2 +
                                      $route3_rate_amount * $units_quantity3;
            $new_route['total_transit_time'] =  $route1_transit_time +
                                                $route2_transit_time +
                                                $route3_transit_time;
            $new_route['with_rate'] = $with_rate;

            array_push($all_routes_and_rates, $new_route);

        }


        if($sort_by == "price") {
            $total_sum = array_column($all_routes_and_rates, 'total_sum');
            array_multisort($total_sum, SORT_ASC, $all_routes_and_rates);
        }
        else if ($sort_by == "time") {
            $total_transit_time = array_column($all_routes_and_rates, 'total_transit_time');
            array_multisort($total_transit_time, SORT_ASC, $all_routes_and_rates);
        }

        if ($withRates == "true") {
            $all_routes_and_rates = array_filter($all_routes_and_rates, function ($item) {
                if ($item['with_rate'] == true ) {
                    return true;
                }
                return false;
            });
        }



        session_start();
            
        // передаємо знайдені маршрути в сесію
        $_SESSION['start-point'] = $start_point;
        $_SESSION['end-point'] = $end_point;
        $_SESSION['cargo-id'] = $cargo_id;
        $_SESSION['cargo-gw'] = $cargo_gw;
        $_SESSION['all_routes_and_rates'] = $all_routes_and_rates;
        $_SESSION['with-rates'] = $withRates;
        $_SESSION['sort-by'] = $sort_by;


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

        $sql = "SELECT
        r1.transport_type_trinity_code as first_transport, 
        r1.start_point_name, 
        r1.end_point_name as middle_point,
        r2.transport_type_trinity_code as second_transport,
        r2.end_point_name
        from
        routes as r1,
        (SELECT
                 r.transport_type_trinity_code, 
                 r.start_point_name,
                 r.end_point_name
             FROM routes r
             WHERE 	r.end_point_name = ? AND 
                     r.start_point_name != r.end_point_name 
            ) as r2
        where 	r1.start_point_name = ? AND 
                r1.start_point_name != r1.end_point_name AND
                r1.end_point_name = r2.start_point_name 
        group by 
            first_transport,
             middle_point,
            second_transport;";           

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
                . "    r1.transport_type_trinity_code as 1st_transport, \n"
                . "    r1.start_point_name, \n"         
                . "    r1.end_point_name as middle_point1,\n"         
                . "    r2.transport_type_trinity_code as 2nd_transport,\n"        
                . "    r2.end_point_name as middle_point2,\n"  
                . "    r3.transport_type_trinity_code as 3rd_transport,\n"        
                . "    r3.end_point_name\n"         
                . "from \n"          
                . "	   routes as r1,\n"           
                . "    transport_type as t,\n"           
                . "    (SELECT \n"           
                . "     	r.transport_type_trinity_code, \n"         
                . "     	r.start_point_name,\n"           
                . "     	r.end_point_name\n"          
                . "     FROM routes r, transport_type t2\n"         
                . "     WHERE 	\n"         
                . "     		r.start_point_name != r.end_point_name AND\n"         
                . "     		r.transport_type_trinity_code = t2.trinity_code\n"          
                . "    ) as r2,\n"
                . "    (SELECT \n"           
                . "     	r.transport_type_trinity_code, \n"         
                . "     	r.start_point_name,\n"           
                . "     	r.end_point_name\n"          
                . "     FROM routes r, transport_type t3\n"         
                . "     WHERE 	r.end_point_name = ? AND \n"         
                . "     		r.start_point_name != r.end_point_name AND\n"         
                . "     		r.transport_type_trinity_code = t3.trinity_code\n"          
                . "    ) as r3\n"            
                . "\n"          
                . "where 	r1.start_point_name = ? AND \n"            
                . "		    r1.start_point_name != r1.end_point_name AND\n"           
                . "         r1.end_point_name = r2.start_point_name AND\n"          
                . "         r1.transport_type_trinity_code = t.trinity_code AND\n"
                . "         r2.end_point_name = r3.start_point_name\n"             
                . "group by \n"           
                . "	    1st_transport,\n"           
                . "     middle_point1,\n"          
                . "     2nd_transport,\n"
                . "     middle_point2,\n"          
                . "     3rd_transport,\n"
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