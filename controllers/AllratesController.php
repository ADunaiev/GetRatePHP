<?php

include_once "ApiController.php";
include_once "./model/DataWorker.php";

class AllratesController extends ApiController {

    protected function do_post() {
        $result = [
            'status' => 0,
            'meta' => [
                'api' => 'allrates',
                'service' => 'getrates',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'result' => "",
                'supplier' => "",
            ],
        ];

        $user_id = $_POST['user-id'];
        $supplier_trinity_code = $_POST['supplier-trinity-code'];
        $transport_type_trinity_code = $_POST['transport-type-trinity-code'];
        $start_point = $_POST['start-point'];
        $end_point = $_POST['end-point'];
        $container_type_code = $_POST['container-type'];
        $line_id = $_POST['line'];

        $dataWorker = new DataWorker();

        $all_rates = $dataWorker->get_all_rates();

        // реалізуємо фільтрування ставок по постачальнику
        if ($supplier_trinity_code != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['supplier_code'] ==  $_POST['supplier-trinity-code']) {
                    return true;
                }
                return false;
            });
        }

        // реалізуємо фільтрування ставок по pol
        if ($start_point != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['pol'] ==  $_POST['start-point']) {
                    return true;
                }
                return false;
            });
        }

        // реалізуємо фільтрування ставок по pod
        if ($end_point != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['pod'] ==  $_POST['end-point']) {
                    return true;
                }
                return false;
            });
        }

        // реалізуємо фільтрування ставок по лінії
        if ($line_id != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['line'] == $_POST['line']) {
                    return true;
                }
                return false;
            });
        }

        // реалізуємо фільтрування ставок по типу транспорту
        if ($transport_type_trinity_code != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['transport_type_code'] == $_POST['transport-type-trinity-code']) {
                    return true;
                }
                return false;
            });
        }

        // реалізуємо фільтрування ставок по типу контейнера
        if ($container_type_code != "") {
            $all_rates = array_filter($all_rates, function ($item) {
                if ($item['cont_type'] == $_POST['container-type']) {
                    return true;
                }
                return false;
            });
        }

        session_start();
            
        // передаємо знайдені ставки в сесію
        $_SESSION['start-point'] = $start_point;
        $_SESSION['end-point'] = $end_point;
        $_SESSION['all-rates'] = $all_rates;
        $_SESSION['container-type'] = $container_type_code;
        $_SESSION['supplier-trinity-code'] = $supplier_trinity_code;
        $_SESSION['transport-type-trinity-code'] = $transport_type_trinity_code;
        $_SESSION['line'] = $line_id;
        

        $result['status'] = 1;
        $result['data']['result'] = $all_rates;
        $result['data']['supplier'] = $supplier_trinity_code;
        $result['data']['message'] = "Request is fulfilled successfully!";

        $this->end_with($result);

    }
}