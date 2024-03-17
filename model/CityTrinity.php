<?php

class CityTrinity{
    private $id;
    private $name;
    private $city_trinity_id;
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

}