<?php

namespace App\Controllers;
use Core\Controller as Controller;

class Error404Controller extends Controller {

    public function defaultAction() {
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        echo "404";
    }

}