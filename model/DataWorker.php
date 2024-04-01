<?php 

class DataWorker {

    // підключення до бд
    public function connect_db_or_exit() {
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

    // міста
    public function get_all_cities() {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT `name` FROM trinity_cities
                UNION
                SELECT `name` FROM trinity_ports
                UNION
                SELECT `name` FROM trinity_bcp
                ORDER BY `name`";

        try {
            $prep = $db->prepare($sql);
            $prep->execute();

            $cities = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $cities;
    }

    // постачальники
    public function get_all_suppliers() {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT * FROM trinity_suppliers
                ORDER BY `name`";

        try {
            $prep = $db->prepare($sql);
            $prep->execute();

            $suppliers = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $suppliers;
    }

    // види транспорту
    public function get_all_transport_types() {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT * FROM transport_type
                ORDER BY `name`";

        try {
            $prep = $db->prepare($sql);
            $prep->execute();

            $transport_types = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $transport_types;
    }

    // вантажи
    public function get_all_cargo() {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT `id`, `custom_code`, `name` 
                FROM cargo_trinity";

        try {
            $prep = $db->prepare($sql);
            $prep->execute();

            $cargo = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $cargo;
    }

    // валюти
    public function get_all_currencies(){
        $db = $this->connect_db_or_exit();
        $sql = "SELECT * 
                FROM currencies";

        try {
            $prep = $db->prepare($sql);
            $prep->execute();

            $currencies = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $currencies;
    }

    public function get_cargo_name_by_id($id) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT `name` 
                FROM cargo_trinity
                WHERE id = ?";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([$id]);

            $cargo = $prep->fetch();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $cargo['name'];
    }   

    public function get_currency_cc_by_r030($r030) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT cc 
                FROM currencies
                WHERE r030 = ?";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([$r030]);

            $cargo = $prep->fetch();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $cargo['cc'];
    }   

    public function get_currency_by_r030($r030) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT * 
                FROM currencies
                WHERE r030 = ?";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([$r030]);

            $cargo = $prep->fetch();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $cargo;
    }   

    // пошук ставок по маршруту з сортування по вартості
    public function get_route_rates($start_point_name, $end_point_name, $transport_type_trinity_code) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT s.name, cast(r.date AS date) as rate_day, r.amount, r.currency_r030, rs.transit_time, rs.unit_payload, r.validity\n"
                . "FROM `rates` as r \n"
                . "LEFT JOIN routes as rt\n"
                . "ON r.route_id = rt.id\n"
                . "LEFT JOIN trinity_suppliers as s\n"
                . "ON r.supplier_trinity_code = s.trinity_code\n"
                . "LEFT JOIN routes_suppliers as rs\n"
                . "ON rt.id = rs.route_id AND s.trinity_code = rs.supplier_id\n"
                . "WHERE 	rt.start_point_name = ? AND\n"
                . "		    rt.end_point_name = ? AND"
                . "         rt.transport_type_trinity_code = ?\n"
                . "ORDER BY r.amount;";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $start_point_name,
                $end_point_name,
                $transport_type_trinity_code
            ]);

            $rates = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $rates;
    }

    // пошук ставок по транзитному часу
    public function get_route_rates_by_transit_time($start_point_name, $end_point_name, $transport_type_trinity_code) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT s.name, cast(r.date AS date) as rate_day, r.amount, r.currency_r030, rs.transit_time, rs.unit_payload, r.validity\n"
                . "FROM `rates` as r \n"
                . "LEFT JOIN routes as rt\n"
                . "ON r.route_id = rt.id\n"
                . "LEFT JOIN trinity_suppliers as s\n"
                . "ON r.supplier_trinity_code = s.trinity_code\n"
                . "LEFT JOIN routes_suppliers as rs\n"
                . "ON rt.id = rs.route_id AND s.trinity_code = rs.supplier_id\n"
                . "WHERE 	rt.start_point_name = ? AND\n"
                . "		    rt.end_point_name = ? AND"
                . "         rt.transport_type_trinity_code = ?\n"
                . "ORDER BY rs.transit_time;";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $start_point_name,
                $end_point_name,
                $transport_type_trinity_code
            ]);

            $rates = $prep->fetchAll();
        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }
        return $rates;
    }

    // пошук коду міста по назві
    public function get_city_code_by_name($name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT id FROM trinity_cities
                    WHERE name = ?";

        $result = "";

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $name
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

    // пошук коду порта по назві
    public function get_port_code_by_name($name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT id FROM trinity_ports
                    WHERE name = ?";

        $result = false;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $name
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

    // пошук коду погранпереходу по назві
    public function get_bcp_code_by_name($name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT id FROM trinity_bcp
                    WHERE name = ?";

        $result = false;

        try {
            $prep = $db->prepare($sql);
            $prep->execute([
                $name
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

    // пошук типу транспорту по коду трініті
    public function get_transport_type_name_by_trinity_code($ttype_id) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT `name` FROM transport_type
        WHERE trinity_code = ?";

        $result = false;

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
    public function get_transport_image_by_trinity_code($ttype_id) {

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
    public function get_transport_image_by_transport_name($ttype_name) {

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

    // пошук прямих маршрутів
    public function find_simple_routes ($start_point_name, $end_point_name) {

        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "SELECT transport_type_trinity_code, 
                       start_point_name, 
                       end_point_name
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
    public function find_2_item_routes ($start_point_name, $end_point_name) {
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
    public function find_3_item_routes ($start_point_name, $end_point_name) {
        $db = (new DataWorker())->connect_db_or_exit();

        $sql = "select 
                        r1.transport_type_trinity_code as 1st_transport,
                        r1.start_point_name,          
                        r1.end_point_name as middle_point1,
                        r2.transport_type_trinity_code as 2nd_transport,        
                        r2.end_point_name as middle_point2, 
                        r3.transport_type_trinity_code as 3rd_transport,       
                        r3.end_point_name
                from routes as r1           
                left join transport_type as t
                on r1.transport_type_trinity_code = t.trinity_code
                inner join (SELECT
                        r.id,
                        r.transport_type_trinity_code,          
                        r.start_point_name,           
                        r.end_point_name          
                        FROM routes r, transport_type t2         
                        WHERE 	         
                        r.start_point_name != r.end_point_name AND         
                        r.transport_type_trinity_code = t2.trinity_code          
                        ) as r2
                on r1.end_point_name = r2.start_point_name
                inner join  (SELECT 
                            r.id,
                            r.transport_type_trinity_code,          
                            r.start_point_name,           
                            r.end_point_name          
                            FROM routes r, transport_type t3         
                            WHERE 	r.end_point_name = ? AND          
                            r.start_point_name != r.end_point_name AND         
                            r.transport_type_trinity_code = t3.trinity_code         
                            ) as r3  
                ON  r2.end_point_name = r3.start_point_name
                where 	r1.start_point_name = ? AND             
                r1.start_point_name != r1.end_point_name
                group by            
                            1st_transport,           
                            middle_point1,          
                            2nd_transport,
                            middle_point2,          
                            3rd_transport,
                            end_point_name;";
                

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

    // пошук маршрута 
    public function get_route(
        $transport_type_trinity_code, 
        $start_point_name, 
        $end_point_name
        ) {
            $db = (new DataWorker())->connect_db_or_exit();

            $sql = "SELECT *
                    FROM routes
                    WHERE 
                        transport_type_trinity_code = ? AND 
                        start_point_name = ? AND 
                        end_point_name = ?
                    ";
    
            $result = null;
    
            try {
                $prep = $db->prepare($sql);
                $prep->execute([
                    $transport_type_trinity_code,
                    $start_point_name,
                    $end_point_name
                ]);
                $result = $prep->fetch();
            }
            catch(PDOException $ex) {
                http_response_code(500);
                echo "Connection error: " . $ex->getMessage();
                exit;
            } 
    
            return $result;
    }

    // створити новий маршрут
    public function create_route(
        $transport_type_trinity_code, 
        $start_point_name, 
        $start_point_type, 
        $start_point_code, 
        $end_point_name,
        $end_point_type,
        $end_point_code
        ) {
            $db = (new DataWorker())->connect_db_or_exit();

            $sql = "INSERT INTO routes 
                        (
                            transport_type_trinity_code,
                            start_point_name,
                            start_point_type,
                            start_point_code,
                            end_point_name,
                            end_point_type,
                            end_point_code
                        )
                    VALUES ( ?, ?, ?, ?, ?, ?, ?)";
    
            $result = "Error!";
    
            try {
                $prep = $db->prepare($sql);
                $prep->execute([
                    $transport_type_trinity_code,
                    $start_point_name,
                    $start_point_type,
                    $start_point_code,
                    $end_point_name,
                    $end_point_type,
                    $end_point_code
                ]);
                $result = "Route is added to database successfully!";
            }
            catch(PDOException $ex) {
                http_response_code(500);
                echo "Connection error: " . $ex->getMessage();
                exit;
            } 
            
            return $result;
    }

    // пошук сервіса 
    public function get_route_supplier(
        $route_id, 
        $supplier_id
        ) {
            $db = (new DataWorker())->connect_db_or_exit();

            $sql = "SELECT *
                    FROM routes_suppliers
                    WHERE 
                        route_id = ? AND 
                        supplier_id = ?
                    ";
    
            $result = null;
    
            try {
                $prep = $db->prepare($sql);
                $prep->execute([
                    $route_id,
                    $supplier_id
                ]);
                $result = $prep->fetch();
            }
            catch(PDOException $ex) {
                http_response_code(500);
                echo "Connection error: " . $ex->getMessage();
                exit;
            } 
    
            return $result;
    }
    
    // створити новий сервіс
    public function create_route_supplier(
        $route_id,
        $supplier_id, 
        $transit_time,
        $unit_payload
        ) {
            $db = (new DataWorker())->connect_db_or_exit();

            $sql = "INSERT INTO routes_suppliers 
                        (
                            route_id,
                            supplier_id,
                            transit_time,
                            unit_payload
                        )
                    VALUES ( ?, ?, ?, ?)";
    
            $result = "Error!";
    
            try {
                $prep = $db->prepare($sql);
                $prep->execute([
                    $route_id,
                    $supplier_id,
                    $transit_time,
                    $unit_payload
                ]);
                $result = "Service is added to database successfully!";
            }
            catch(PDOException $ex) {
                http_response_code(500);
                echo "Connection error: " . $ex->getMessage();
                exit;
            } 
            
            return $result;
    }

    // створити нову ставку
    public function create_rate(
        $date,
        $supplier_id, 
        $route_id,
        $amount,
        $currency,
        $validity,
        $source
        ) {
            $db = (new DataWorker())->connect_db_or_exit();

            $sql = "INSERT INTO rates 
                        (
                            date,
                            supplier_trinity_code,
                            route_id,
                            amount,
                            currency_r030,
                            validity,
                            source
                        )
                    VALUES ( ?, ?, ?, ?, ?, ?, ?)";
    
            $result = "Error";
    
            try {
                $prep = $db->prepare($sql);
                $prep->execute([
                    $date,
                    $supplier_id,
                    $route_id,
                    $amount,
                    $currency,
                    $validity,
                    $source
                ]);
                $result = "Rate is added to database successfully!";
            }
            catch(PDOException $ex) {
                http_response_code(500);
                echo "Connection error: " . $ex->getMessage();
                exit;
            } 
            
            return $result;
    }

}