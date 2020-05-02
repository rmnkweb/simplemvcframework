<?php

namespace App\Controllers;
use Core\Controller as Controller;
use App\Models\User;

class AuthorizationController extends Controller {

    public function defaultAction($request = null) {
        $errors = [];
        if ((isset($this->request["username"])) OR (isset($this->request["password"]))) {
            $username = htmlspecialchars($this->request["username"]);
            $password = htmlspecialchars($this->request["password"]);
            if ((!empty($username)) AND (!empty($password))) {
                if ($this->authorize($this->request["username"], $this->request["password"])) {
                    header("Location: /");
                    die();
                } else {
                    $errors[] = "Не правильное имя пользователя или пароль.";
                }
            } else {
                $errors[] = "Все поля обязательны для заполнения";
            }
        }

        $this->view->render('template/header');
        $this->view->render('authorization/form', ["errors" => $errors, "form_fields" => $this->request]);
        $this->view->render('template/footer');
    }

    public function logoutAction($request = null) {
        $_SESSION = [];
        session_unset();
        header('Location: /');
    }

    public function authorize($username, $password) {
        $user = new User();
        try {
            $users = $user->getUsers([
                "name" => $username
            ]);
            if (($users !== false) AND (count($users) > 0)) {
                if ((array_key_exists("password", $users[0])) AND (password_verify($password, $users[0]["password"]))) {
                    $_SESSION[$this->config['sessionName']] = array(
                        "userID" => $users[0]["id"],
                        "userType" => $users[0]["group"],
                    );
                    return true;
                } else {
                    echo "Password incorrect." . PHP_EOL;
                }
            } else {
                echo "User not found" . PHP_EOL;
            }

            return false;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            return false;
        }
    }
}