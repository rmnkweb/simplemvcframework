<?php

namespace App\Controllers;
use Core\Controller as Controller;
use App\Models\User;
use Core\Response as Response;

class UserController extends Controller {

    public function defaultAction() {
        $users = new User();
    }

    /**
     * @param $username string
     * @param $password string not encrypted password
     * @param int $group id of group
     * @return Response
     */
    public function add($username, $password, $group = 0) {
        if ((!empty($username)) AND (!empty($password))) {
            $values = [
                "name" => $username,
                "password" => $password,
                "group" => $group
            ];
            $users = new User();
            if ($users->checkConstraints($values)) {
                return $users->addUser($values);
            } else {
                return new Response(0, "Constraints failure.");
            }
        } else {
            return new Response(0, ((empty($username)) ? "Username is empty. " : "") . ((empty($username)) ? "Password is empty." : ""));
        }
    }
}