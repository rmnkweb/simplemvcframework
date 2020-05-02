<?php


namespace App\Controllers;
use Core\Controller as Controller;
use App\Models\User;


class UserController extends Controller {

    public function defaultAction() {
        $users = new User();
    }
}