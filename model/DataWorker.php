<?php 

class DataWorker {

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

    public function get_route_rates($start_point_name, $end_point_name, $transport_type_trinity_code) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT s.name, cast(r.date AS date) as rate_day, r.amount, r.currency, rs.transit_time, rs.unit_payload\n"
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

    public function get_route_rates_by_transit_time($start_point_name, $end_point_name, $transport_type_trinity_code) {

        $db = $this->connect_db_or_exit();
        $sql = "SELECT s.name, cast(r.date AS date) as rate_day, r.amount, r.currency, rs.transit_time, rs.unit_payload\n"
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

    // пошук типу транспорту по коду трініті
    public function get_transport_type_name_by_trinity_code($ttype_id) {

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

    }    
    
}