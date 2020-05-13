<?php


namespace App\Controllers;
use Core\Controller as Controller;
use App\Models\User;


class UserController extends Controller {

    public function defaultAction() {
        $users = new User();
    }

    public function add($username, $password, $group = 0) {
        if ((!empty($username)) AND (!empty($password))) {
            $values = [
                "name" => $username,
                "password" => $password,
                "group" => $group
            ];
            $users = new User();
            if ($users->checkConstraints($values)) {
                try {
                    $users->addUser($values);
                    return true;
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                    return false;
                }
            } else {
                echo "Constraints failure." . PHP_EOL;
                return false;
            }
        }
    }
}