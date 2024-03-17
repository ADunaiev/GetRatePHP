<?php

class CargoTrinity{
    private $id;
    private $name;
    private $custom_code;

    function get_name() {
        return $this->name;
    }
    function set_name($name) {
        $this->name = $name;
    }
    function get_custom_code() {
        return $this->custom_code;
    }
    function set_custom_code($custom_code) {
        $this->custom_code = $custom_code;
    }

}