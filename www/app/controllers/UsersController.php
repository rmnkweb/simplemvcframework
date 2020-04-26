<?php


namespace App\Controllers;
use Core\Controller as Controller;
use App\Models\Users;


class UsersController extends Controller {

    public function defaultAction() {
        $users = new Users();
    }
}