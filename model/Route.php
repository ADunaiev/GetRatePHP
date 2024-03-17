<?php

class Route {
    private $id;
    private $transport_type_trinity_code;
    private $start_point_name;
    private $start_point_type;
    private $start_point_code;
    private $end_point_name;
    private $end_point_type;
    private $end_point_code;

    function get_transport_type_trinity_code() {
        return $this->transport_type_trinity_code;
    }
    function set_date($transport_type_trinity_code) {
        $this->transport_type_trinity_code = $transport_type_trinity_code;
    }

    function get_start_point_name() {
        return $this->start_point_name;
    }
    function set_start_point_name($name) {
        $this->start_point_name = $name;
    }

    function get_start_point_type() {
        return $this->start_point_type;
    }
    function set_start_point_type($type) {
        $this->start_point_type = $type;
    }

    function get_start_point_code() {
        return $this->start_point_code;
    }
    function set_start_point_code($code) {
        $this->start_point_name = $code;
    }

    function get_end_point_name() {
        return $this->end_point_name;
    }
    function set_end_point_name($name) {
        $this->end_point_name = $name;
    }

    function get_end_point_type() {
        return $this->end_point_type;
    }
    function set_end_point_type($type) {
        $this->end_point_type = $type;
    }

    function get_end_point_code() {
        return $this->end_point_code;
    }
    function set_end_point_code($code) {
        $this->end_point_name = $code;
    }
}