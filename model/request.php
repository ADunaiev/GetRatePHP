<?php

class Request {
    private $id;
    private $date;
    private $start_point_name;
    private $end_point_name;
    private $cargo_id;
    private $cargo_gw;
    private $user_id;

    function get_date() {
        return $this->date;
    }
    function set_date($date) {
        $this->date = $date;
    }

    function get_start_point_name() {
        return $this->start_point_name;
    }
    function set_start_point_name($name) {
        $this->start_point_name = $name;
    }

    function get_end_point_name() {
        return $this->end_point_name;
    }
    function set_end_point_name($name) {
        $this->end_point_name = $name;
    }

    function get_cargo_id() {
        return $this->cargo_id;
    }
    function set_cargo_id($cargo_id) {
        $this->cargo_id = $cargo_id;
    }

    function get_user_id() {
        return $this->user_id;
    }
    function set_start_point_name($user_id) {
        $this->user_id = $user_id;
    }
}