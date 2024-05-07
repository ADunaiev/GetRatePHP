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

        $user_id = $_POST['user-id'];

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
            $imported_rates = $spreadsheet->getActiveSheet()->toArray();

            $result['data']['result'] = $imported_rates;
            $dataWorker = new DataWorker();
            $check = 1;

            // валідація кожної отриманної строки
            foreach($imported_rates as $rate) {
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

            // перевірка наявності маршрута в базі
            // перевірка наявності сервіса
            // додавання ставки в бд


            // зберігаємо файл з даними
            //$dataWorker = new DataWorker();
            //$date = date('Y-m-d H:i:s');
            //$dataWorker->create_rates_import($date, $user_id, $filename);

            session_start();

            $_SESSION['imported_rates'] = $imported_rates;
            $_SESSION['filename'] = $_FILES['import-rates-file']['name'];
            if ($check == 1) {
                $date = date('Y-m-d H:i:s');
                $source = "Uploaded from excel";
                /*
                foreach($imported_rates as $rate) {
                    $dataWorker->create_rate(
                        $date,
                        $supplier_id, // потрібно корегувати таблицю юзеров
                        $route_id,
                        $amount,
                        $currency,
                        $etd,
                        $validity,
                        $source,
                        $line_id,
                        $container_type_id,
                        $remark
                    );
                }*/
                $result['status'] = 1;
                $_SESSION['validation'] = "successful";
            } 
            $result['data']['message'] = "Rates import is added successfully!";
        }
        else {
            $result['data']['message'] = "File is not loaded";
            $this->end_with($result);
        }


        $this->end_with($result);

    }

}