<?php

namespace Core;

class View {

    public $config;

    public function __construct() {
        require ROOT . '/core/config/session.php';
    }

    function render($path, $data = []) {

        if (is_array($data))
            extract($data, EXTR_PREFIX_ALL, "viewdata");

        require(ROOT . '/app/views/' . $path . '.php');

    }

    function checkPermission($group = false) {
        if ((isset($_SESSION[$this->config['sessionName']])) AND (isset($_SESSION[$this->config['sessionName']]["userType"])) AND ($group !== false) AND ($group === $_SESSION[$this->config['sessionName']]["userType"])) {
            return true;
        } else {
            return false;
        }
    }
}