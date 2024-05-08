<?php

include_once "ApiController.php";
include_once "./model/DataWorker.php";

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportratesController extends ApiController {


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
                'api' => 'import_rates',
                'service' => 'import',
                'time' => time()
            ],
            'data' => [
                'message' => "",
                'result' => "",
                'imported_file' => "",
            ],
        ];
        $dataWorker = new DataWorker();

        $user_id = $_POST['user-id'];
        $supplier_trinity_code = 
        $dataWorker->get_supplier_code_by_user_id($user_id);

        if(! empty($_FILES['import-rates-file'])){
            // файл якщо переданий, то перевіряємо його
            if (
                $_FILES['import-rates-file']['error'] != 0 || 
                $_FILES['import-rates-file']['size'] == 0
            ){
                $result['data']['message'] = "File upload error";
                $result['status'] = -1;
                $this->end_with($result);
            } 
            // перевіряємо тип файлу (розширення) на перелік допустимих
            $ext = pathinfo($_FILES['import-rates-file']['name'], PATHINFO_EXTENSION);
            
            if (strpos(".xlsx", $ext) === false) {
                $result['data']['message'] = "File type error";
                $result['status'] = -1;
                $this->end_with($result);
            }

            // генеруємо іи'я для збереження, залишаємо розширення
            do {
                $filename = uniqid(). "." . $ext;
            } // перевіряємо чи не потрапили в існуючий файл 
            while (is_file("./wwwroot/imported_rates/" . $filename)) ;

            // переносимо завантаженний файл до нового розміщення
            move_uploaded_file(
                $_FILES['import-rates-file']['tmp_name'], 
                "./wwwroot/imported_rates/" . $filename
            );
            
            $result['data']['imported_file'] = $filename;

            $inputFileNamePath = "./wwwroot/imported_rates/" . $filename;
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
            $raw_rates = $spreadsheet->getActiveSheet()->toArray();

            
            
            $check = 1;

            // валідація кожної отриманної строки
            foreach($raw_rates as $rate) {
                if ($rate[0] != "line") {
                    if (
                        !$dataWorker->validate_line($rate[0]) ||
                        !$dataWorker->validate_port($rate[1]) ||
                        !$dataWorker->validate_port($rate[2]) ||
                        !$rate[3] > 0 || !is_numeric($rate[3]) ||
                        !$dataWorker->validate_currency($rate[4]) ||
                        !$dataWorker->validate_container_type($rate[5]) ||
                        !$dataWorker->validateDate($rate[6]) ||
                        !$dataWorker->validateDate($rate[7])
                    ) {
                        $check = 0;
                        break;
                    }
                }
            }

            // формуємо масив з результатом
            $imported_rates = array();

            // перевірка наявності маршрута в базі
            foreach($raw_rates as $rate) {

                $new_rate = array(
                    "rate_item" => $rate,
                    "is_route" => "",
                    "route" => "",
                    "route_message" => "",
                    "is_service" => "",
                    "route_supplier" => "",
                    "service_message" => "",
                    "is_saved" => "",
                    "db_message" => ""
                );

                if ($check == 1) {

                    $route = $dataWorker->get_route(
                        "000000004",
                        $rate[1],
                        $rate[2]
                    );
                    
                    if ($route) {
                        $new_rate['is_route'] = true;
                        $new_rate['route_message'] = "Route is found";
                        $new_rate['route'] = $route;
                    
                        // перевіряємо наявність сервіса
                        $route_supplier = $dataWorker->get_route_supplier(
                            $route['id'],
                            $supplier_trinity_code
                        );

                        if ($route_supplier != null) {
                            $new_rate['is_service'] = true;
                            $new_rate['service_message'] = " Service is found";
                            $new_rate['route_supplier'] = $route_supplier;

                            // додавання ставки в бд

                            $date = date('Y-m-d H:i:s');
                            $source = "Imported from excel";
                            $currecy_r030 = $dataWorker->get_currency_r030_by_cc($rate[4]);
                            $line_id = $dataWorker->get_line_trinity_code_by_name($rate[0]);
                            $container_type_id = $dataWorker->get_container_type_id_by_name($rate[5]);
                            $etd = date("Y-m-d", strtotime($rate[6]));
                            $validity = date( "Y-m-d", strtotime($rate[7]));
                    
                            $new_rate['db_message'] = $dataWorker->create_rate(
                                $date,
                                $supplier_trinity_code, 
                                $route_supplier['route_id'],
                                $rate[3],
                                $currecy_r030,
                                $etd,
                                $validity,
                                $source,
                                $line_id,
                                $container_type_id,
                                $rate[8]
                            );
                    
                            if ($new_rate['db_message'] != "Error") {
                                $new_rate['is_saved'] = true;
                            }
                            else {
                                $new_rate['is_saved'] = false;
                            }
                        }
                        else {
                            $new_rate['is_service'] = false;
                            $new_rate['service_message'] .= " Service is not found";
                        }
                        
                    }
                    else {
                        $new_rate['is_route'] = false;
                        $new_rate['route_message'] = "Route is not found";
                    }
                }

                array_push($imported_rates, $new_rate);
            }



            $result['data']['result'] = $imported_rates;

            session_start();

            $_SESSION['imported_rates'] = $imported_rates;
            $_SESSION['filename'] = $_FILES['import-rates-file']['name'];
            if ($check == 1) {
                $_SESSION['validation'] = "successful";
                $result['status'] = 1;
                $result['data']['message'] = "Rates import is added successfully!";
            } 
            
        }
        else {
            $result['data']['message'] = "File is not loaded";
            $this->end_with($result);
        }


        $this->end_with($result);

    }

}