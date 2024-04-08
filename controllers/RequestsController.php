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
        $validRates = $_POST['valid-rates'];
        $sort_by = $_POST['sort-by'];
        $currency = $_POST['currency'];
        $dataWorker = new DataWorker();

        $currency_cc = $dataWorker->get_currency_cc_by_r030($currency);

        // валідація даних
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
        if ($currency == "") {
            $result['data']['message'] = "Error. Currency could not be empty!";
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

        // формуємо масив для передачі результата
        $all_routes_and_rates = array();
        
        // запускаємо пошук прямих маршрутів
        $routes = $dataWorker->find_simple_routes($start_point, $end_point);

        // заповнюємо результуючий масив прямими маршрутами
        foreach ( $routes as &$route) {
            $route['transport_type'] = 
                $dataWorker->get_transport_type_name_by_trinity_code(
                    $route['transport_type_trinity_code']
                );
            $route['image'] = 
                $dataWorker->get_transport_image_by_trinity_code(
                    $route['transport_type_trinity_code']
                );

            $route_rates = $dataWorker->get_route_rates(
                $route['start_point_name'],
                $route['end_point_name'],
                $route['transport_type_trinity_code'],
                $validRates,
                $sort_by
            ); 

            $units_quantity = $route_rates != null ? ceil( $cargo_gw / $route_rates[0]['unit_payload']) : 0;
            $route_rate_amount = $route_rates != null ? $route_rates[0]['amount'] : 0;
            $route_transit_time = $route_rates != null ? $route_rates[0]['transit_time'] : 0;
            $route_rate_currency_cc = $route_rates != null ? $dataWorker->get_currency_cc_by_r030($route_rates[0]['currency_r030']) : "";

            $route_rate_currency_r030 = $route_rates != null ? $route_rates[0]['currency_r030'] : 1;

            $route_sum = $units_quantity * $route_rate_amount;

            // конвертуємо суму в валюту реквеста
            if ($route_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route_sum * $currency_1['rate'] / $currency_2['rate'];
                $route_sum = round($temp, 2);
            }

            // $currency_cc = $dataWorker->get_currency_cc_by_r030($currency);

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
                "route_rate_currency" => $route_rate_currency_cc,
                "route_sum" => $route_sum
            );

            $with_rate =  $route_rate_amount > 0 ? true : false;

            $new_route = array(
                "routes" => array($route_item),
                "total_sum" => $route_sum,
                "currency" => $currency_cc,
                "total_transit_time" => $route_transit_time,
                "with_rate" => $with_rate
                );

            array_push($all_routes_and_rates, $new_route);
            
        }

        // запускаємо пошук 2шагових маршрутів
        $two_items_routes = $dataWorker->find_2_item_routes($start_point, $end_point);

        // додаємо в результуючий масив дані 2хшагових маршрутів
        foreach ( $two_items_routes as &$route) {

            // заповнюємо перший відрізок
            $route['image_first'] = 
                $dataWorker->get_transport_image_by_trinity_code(
                    $route['first_transport']
                );

            $route['first_transport_name'] = 
            $dataWorker->get_transport_type_name_by_trinity_code(
                $route['first_transport']
            );

            $route1_rates = $dataWorker->get_route_rates(
                $route['start_point_name'],
                $route['middle_point'],
                $route['first_transport'],
                $validRates,
                $sort_by
            ); 

            $units_quantity1 = $route1_rates != null ? ceil( $cargo_gw / $route1_rates[0]['unit_payload']) : 0;
            $route1_rate_amount = $route1_rates != null ? $route1_rates[0]['amount'] : 0;
            $route1_transit_time = $route1_rates != null ? $route1_rates[0]['transit_time'] : 0;
            $route1_rate_currency_cc = $route1_rates != null ? $dataWorker->get_currency_cc_by_r030($route1_rates[0]['currency_r030']) : "";

            $route1_rate_currency_r030 = $route1_rates != null ? $route1_rates[0]['currency_r030'] : 1;

            $route1_sum = $units_quantity1 * $route1_rate_amount;

            
            if ($route1_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route1_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route1_sum * $currency_1['rate'] / $currency_2['rate'];
                $route1_sum = round($temp, 2);
            }
            
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
                "route_rate_currency" => $route1_rate_currency_cc,
                "route_sum" => $route1_sum
            );

            // заповнюємо другий відрізок
            $route['image_second'] = 
            $dataWorker->get_transport_image_by_trinity_code(
                $route['second_transport']
            );

            $route['second_transport_name'] = 
            $dataWorker->get_transport_type_name_by_trinity_code(
                $route['second_transport']
            );

            $route2_rates = $dataWorker->get_route_rates(
                $route['middle_point'],
                $route['end_point_name'],
                $route['second_transport'],
                $validRates,
                $sort_by
            ); 

            $units_quantity2 = $route2_rates != null ? ceil( $cargo_gw / $route2_rates[0]['unit_payload']) : 0;
            $route2_rate_amount = $route2_rates != null ? $route2_rates[0]['amount'] : 0;
            $route2_transit_time = $route2_rates != null ? $route2_rates[0]['transit_time'] : 0;
            $route2_rate_currency_cc = $route2_rates != null ? $dataWorker->get_currency_cc_by_r030($route2_rates[0]['currency_r030']) : "";
            
            $route2_rate_currency_r030 = $route2_rates != null ? $route2_rates[0]['currency_r030'] : 1;

            $route2_sum = $units_quantity2 * $route2_rate_amount;

            
            if ($route2_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route2_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route2_sum * $currency_1['rate'] / $currency_2['rate'];
                $route2_sum = round($temp, 2);
            }
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
                "route_rate_currency" => $route2_rate_currency_cc,
                "route_sum" => $route2_sum
            );

            $with_rate = ($route1_rate_amount > 0 && $route2_rate_amount > 0) ? true : false;
            // поєднуємо результати
            $new_route = array("routes" => array($route1_item, $route2_item));
            $new_route['total_sum'] = $route1_sum + $route2_sum;
            $new_route['currency'] = $currency_cc;
            $new_route['total_transit_time'] = $route1_transit_time + $route2_transit_time;
            $new_route['with_rate'] = $with_rate;

            array_push($all_routes_and_rates, $new_route);
        }

        // запускаємо пошук 3шагових маршрутів
        $three_items_routes = $dataWorker->find_3_item_routes($start_point, $end_point);


        //  додаємо в результуючий масив дані 3хшагових маршрутів
        foreach ( $three_items_routes as &$route) {
            // заповнюємо перший відрізок
            $route['image_first'] = 
                $dataWorker->get_transport_image_by_trinity_code(
                    $route['1st_transport']
            );

            $route['first_transport_name'] = 
            $dataWorker->get_transport_type_name_by_trinity_code(
                $route['1st_transport']
            );

            $route1_rates = $dataWorker->get_route_rates(
                $route['start_point_name'],
                $route['middle_point1'],
                $route['1st_transport'],
                $validRates,
                $sort_by
            ); 

            $units_quantity1 = $route1_rates != null ? ceil( $cargo_gw / $route1_rates[0]['unit_payload']) : 0;
            $route1_rate_amount = $route1_rates != null ? $route1_rates[0]['amount'] : 0;
            $route1_transit_time = $route1_rates != null ? $route1_rates[0]['transit_time'] : 0;
            $route1_rate_currency_cc = $route1_rates != null ? $dataWorker->get_currency_cc_by_r030($route1_rates[0]['currency_r030']) : "";
            
            $route1_rate_currency_r030 = $route1_rates != null ? $route1_rates[0]['currency_r030'] : 1;

            $route1_sum = $units_quantity1 * $route1_rate_amount;

            
            if ($route1_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route1_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route1_sum * $currency_1['rate'] / $currency_2['rate'];
                $route1_sum = round($temp, 2);
            }


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
                "route_rate_currency" => $route1_rate_currency_cc,
                "route_sum" => $route1_sum
            );

            // заповнюємо другий відрізок
            $route['image_second'] = 
            $dataWorker->get_transport_image_by_trinity_code(
                $route['2nd_transport']
            );

            $route['second_transport_name'] = 
            $dataWorker->get_transport_type_name_by_trinity_code(
                $route['2nd_transport']
            );

            $route2_rates = $dataWorker->get_route_rates(
                $route['middle_point1'],
                $route['middle_point2'],
                $route['2nd_transport'],
                $validRates,
                $sort_by
            ); 

            $units_quantity2 = $route2_rates != null ? ceil( $cargo_gw / $route2_rates[0]['unit_payload']) : 0;
            $route2_rate_amount = $route2_rates != null ? $route2_rates[0]['amount'] : 0;
            $route2_transit_time = $route2_rates != null ? $route2_rates[0]['transit_time'] : 0;
            $route2_rate_currency_cc = $route2_rates != null ? $dataWorker->get_currency_cc_by_r030($route2_rates[0]['currency_r030']) : "";
            
            $route2_rate_currency_r030 = $route2_rates != null ? $route2_rates[0]['currency_r030'] : 1;

            $route2_sum = $units_quantity2 * $route2_rate_amount;
            
            if ($route2_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route2_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route2_sum * $currency_1['rate'] / $currency_2['rate'];
                $route2_sum = round($temp, 2);
            }

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
                "route_rate_currency" => $route2_rate_currency_cc,
                "route_sum" => $route2_sum
            );

            // заповнюємо третій відрізок
            $route['image_third'] = 
            $dataWorker->get_transport_image_by_trinity_code(
                $route['3rd_transport']
            );

            $route['third_transport_name'] = 
            $dataWorker->get_transport_type_name_by_trinity_code(
                $route['3rd_transport']
            );

            $route3_rates = $dataWorker->get_route_rates(
                $route['middle_point2'],
                $route['end_point_name'],
                $route['3rd_transport'],
                $validRates,
                $sort_by
            ); 

            $units_quantity3 = $route3_rates != null ? ceil( $cargo_gw / $route3_rates[0]['unit_payload']) : 0;
            $route3_rate_amount = $route3_rates != null ? $route3_rates[0]['amount'] : 0;
            $route3_transit_time = $route3_rates != null ? $route3_rates[0]['transit_time'] : 0;
            $route3_rate_currency_cc = $route3_rates != null ? $dataWorker->get_currency_cc_by_r030($route3_rates[0]['currency_r030']) : "";
            
            $route3_rate_currency_r030 = $route3_rates != null ? $route3_rates[0]['currency_r030'] : 1;

            $route3_sum = $units_quantity3 * $route3_rate_amount;

            if ($route3_rate_currency_r030 != $currency) {
                $currency_1 = $dataWorker->get_currency_by_r030($route3_rate_currency_r030);
                $currency_2 = $dataWorker->get_currency_by_r030($currency);

                $temp = $route3_sum * $currency_1['rate'] / $currency_2['rate'];
                $route3_sum = round($temp, 2);
            }

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
                "route_rate_currency" => $route3_rate_currency_cc,
                "route_sum" => $route3_sum
            );

            $with_rate = ($route1_rate_amount > 0 && $route2_rate_amount > 0 && $route3_rate_amount > 0) ? true : false;

            // поєднуємо результати
            $new_route = array("routes" => array($route1_item, $route2_item, $route3_item));
            $new_route['total_sum'] = $route1_sum + $route2_sum + $route3_sum;
            $new_route['currency'] = $currency_cc;
            $new_route['total_transit_time'] =  $route1_transit_time +
                                                $route2_transit_time +
                                                $route3_transit_time;
            $new_route['with_rate'] = $with_rate;

            array_push($all_routes_and_rates, $new_route);

        }

        // робимо сортування
        if($sort_by == "price") {
            $total_sum = array_column($all_routes_and_rates, 'total_sum');
            array_multisort($total_sum, SORT_ASC, $all_routes_and_rates);
        }
        else if ($sort_by == "time") {
            $total_transit_time = array_column($all_routes_and_rates, 'total_transit_time');
            array_multisort($total_transit_time, SORT_ASC, $all_routes_and_rates);
        }

        // реалізуємо фільтрування маршрутів без ставок
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
        $_SESSION['currency'] = $currency;
        $_SESSION['valid-rates'] = $validRates;


        $result['status'] = 1;
        $result['data']['message'] = "Request saved successfully! Start:";

        $this->end_with($result);
    }
}