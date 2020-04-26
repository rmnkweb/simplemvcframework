<?php

namespace App\Controllers;
use Core\Controller as Controller;

class AuthorizationController extends Controller {
    private $users = [
        [
            "username" => "admin",
            "password" => "admin123",
            "group" => 1,
        ],
    ];


    public function defaultAction($request = null) {
        $errors = [];
        $formSent = false;
        $userID = 0;
        if ((isset($this->request["username"])) OR (isset($this->request["password"]))) {
            $formSent = true;
            $username = htmlspecialchars($this->request["username"]);
            $password = htmlspecialchars($this->request["password"]);
            if ((!empty($this->request["username"])) AND (!empty($this->request["password"]))) {
                foreach ($this->users as $id => $user) {
                    if (($username === $user["username"]) AND ($password === $user["password"])) {
                        $userID = $id;
                        $userType = $user["group"];
                    }
                }
            } else {
                $errors[] = "Все поля обязательны для заполнения";
            }
        }


        if (($formSent) AND (empty($errors))) {
            $_SESSION[$this->config['sessionName']] = array(
                "userID" => $userID,
                "userType" => $userType,
            );
            header("Location: /");
            die();
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
}