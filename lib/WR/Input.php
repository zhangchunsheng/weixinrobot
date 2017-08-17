<?php

namespace WR;

class Input {
    static public function get($name, $default = '') {
        if(!empty($_GET[$name])&&is_array($_GET[$name])){
            return $_GET[$name];
        }
        return isset($_GET[$name]) ? trim($_GET[$name]) : $default;
    }

    static public function post($name, $default = '') {
        if(!empty($_POST[$name])&&is_array($_POST[$name])){
            return $_POST[$name];
        }
        return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
    }

    static public function request($name, $default = '') {
        if(!empty($_REQUEST[$name])&&is_array($_REQUEST[$name])){
            return $_REQUEST[$name];
        }
        return isset($_REQUEST[$name]) ? trim($_REQUEST[$name]) : $default; 
    }

    static public function int($name, $default = 0) {
        return (int) self::request($name, $default);
    }

    static public function email($name, $default = '') {
        return isset($_POST[$name]) ? filter_input(INPUT_POST, $name, FILTER_VALIDATE_EMAIL) : $default;
    }

}
