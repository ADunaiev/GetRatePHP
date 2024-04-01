<?php

include_once "ApiController.php";
include_once "./model/DataWorker.php";

class AddrateController extends ApiController {

    protected function do_post() {
        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'addrate',
                'service' => 'addrate',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'avatar' => "",
                'name' => "",
            ],
        ];

        $user_id = $_POST['user-id'];
        $received = date('Y-m-d H:i:s');
        $supplier_trinity_code = $_POST['supplier-trinity-code'];
        $transport_type_trinity_code = $_POST['transport-type-trinity-code'];
        $start_point = $_POST['start-point'];
        $end_point = $_POST['end-point'];
        $transit_time = $_POST['transit-time'];
        $unit_payload = $_POST['unit-payload'];
        $amount = $_POST['amount'];
        $currency = $_POST['currency'];
        $validity = $_POST['validity'];

        // валідуємо отримані дані
        if ($user_id == "") {
            $result['data']['message'] = "Error. You are not signed in!";
            $this->end_with($result);
        }
        if ($supplier_trinity_code == "") {
            $result['data']['message'] = "Error. Supplier could not be empty!";
            $this->end_with($result);
        }
        if ($transport_type_trinity_code == "") {
            $result['data']['message'] = "Error. Transport type could not be empty!";
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
        if ($amount == "") {
            $result['data']['message'] = "Error. Amount could not be empty!";
            $this->end_with($result);
        }
        if ($currency == "") {
            $result['data']['message'] = "Error. Currency could not be empty!";
            $this->end_with($result);
        }

        $dataWorker = new DataWorker();


        // шукаємо route у базі. 
        $route = $dataWorker->get_route(
            $transport_type_trinity_code,
            $start_point,
            $end_point
        );
        $result['data']['message'] = "Route is exist";

        // Якщо такого не існує - додаємо
        if (!$route) {
            $start_point_code = "";
            $start_point_type = "";
            $end_point_code = "";
            $end_point_type = "";

            $start_point_port = $dataWorker->get_port_code_by_name($start_point);
            $start_point_city = $dataWorker->get_city_code_by_name($start_point);
            $start_point_bcp = $dataWorker->get_bcp_code_by_name($start_point);

            if($start_point_port != false) {
                $start_point_code = $start_point_port['id'];
                $start_point_type = "port";
            }

            if($start_point_city != false) {
                $start_point_code = $start_point_city['id'];
                $start_point_type = "city";
            }

            if($start_point_bcp != false) {
                $start_point_code = $start_point_bcp['id'];
                $start_point_type = "bcp";
            }

            $end_point_port_code = $dataWorker->get_port_code_by_name($end_point);
            $end_point_city_code = $dataWorker->get_city_code_by_name($end_point);
            $end_point_bcp_code = $dataWorker->get_bcp_code_by_name($end_point);

            if($end_point_port_code != "") {
                $end_point_code = $end_point_port_code['id'];
                $end_point_type = "port";
            }

            if($end_point_city_code != "") {
                $end_point_code = $end_point_city_code['id'];
                $end_point_type = "city";
            }

            if($end_point_bcp_code != "") {
                $end_point_code = $end_point_bcp_code['id'];
                $end_point_type = "bcp";
            }

            $result['data']['message'] = $dataWorker->create_route(
                $transport_type_trinity_code,
                $start_point,
                $start_point_type,
                $start_point_code,
                $end_point,
                $end_point_type,
                $end_point_code
            );

            $route = $dataWorker->get_route(
                $transport_type_trinity_code,
                $start_point,
                $end_point
            );

        }

        // шукаємо у базі сервіс
        $route_supplier = $dataWorker->get_route_supplier(
            $route['id'],
            $supplier_trinity_code
        );

        // Якщо такого не існує - додаємо
        if (!$route_supplier) {

            if($transit_time <= 0 && $unit_payload <= 0) {
                $result['status'] = 2;
                $result['data']['message'] = "This is a new service. Please add information about transit time and unit payload.";
                $this->end_with($result);
            }
            else {
                
                $result['data']['message'] = $dataWorker->create_route_supplier(
                    $route['id'],
                    $supplier_trinity_code,
                    $transit_time,
                    $unit_payload
                );
                
                $route_supplier = $dataWorker->get_route_supplier(
                    $route['id'],
                    $supplier_trinity_code
                );

            }

        }

        $date = date('Y-m-d H:i:s');
        $source = "Entered on site";
     

        $result['data']['message'] = $dataWorker->create_rate(
            $date,
            $route_supplier['supplier_id'], 
            $route_supplier['route_id'],
            $amount,
            $currency,
            $validity,
            $source
        );

        if ($result['data']['message'] != "Error") {
            $result['status'] = 1;
        }


        $this->end_with($result);

    }

}