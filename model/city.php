<?php

@include_once "./controllers/ApiController.php";

class City extends ApiController {
    public $id;
    public $name;
    public $city_trinity_id;
    //public $country_id;

    function get_name() {
        return $this->name;
    }
    function set_name($name) {
        $this->name = $name;
    }
    function get_city_trinity_id() {
        return $this->city_trinity_id;
    }
    function set_city_trinity_id($city_trinity_id) {
        $this->country_id = $city_trinity_id;
    }

    //<option value="">Shanghai</option>
    function get_all_cities() {
        $db = $this->connect_db_or_exit();

        $sql = "SELECT `id`, `name` FROM cities_trinity";

        try {
            $prep = $db->prepare($sql);         
            $prep->execute();

            $result = $prep->fetch();

            echo $result ;
            /*
            foreach($result as $key => $value) {
                echo "<option value='" . $key . "'>" . $value . "</option>" ;
            }*/

        }
        catch(PDOexception $ex) {
            http_response_code(500);
            $result['data']['message'] = "Connection error: " . $ex->getMessage();
            exit;
        }

    }
}