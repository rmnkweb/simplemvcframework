<?php

namespace App\Controllers;
use Core\Controller as Controller;
use Core\Response as Response;
use App\Models\User;

class AuthorizationController extends Controller {

    const AUTH_ERROR_CODE_WRONG_USER = 1;
    const AUTH_ERROR_CODE_WRONG_PASS = 2;

    public function defaultAction($request = null) {
        $errors = [];
        if ((isset($this->request["username"])) OR (isset($this->request["password"]))) {
            $username = htmlspecialchars($this->request["username"]);
            $password = htmlspecialchars($this->request["password"]);
            if ((!empty($username)) AND (!empty($password))) {
                $response = $this->authorize($this->request["username"], $this->request["password"]);
                if ($response->isSuccess()) {
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
        $_SESSION[$this->config['sessionName']] = [];
        session_unset();
        header('Location: /');
    }

    /**
     * @param $username string
     * @param $password string not encrypted password
     * @return Response
     */
    public function authorize($username, $password) {
        $user = new User();
        $response = $user->getUsers([
            "name" => $username
        ]);
        if ($response->isSuccess()) {
            $users = $response->getData();
            if (($users !== false) AND (count($users) > 0)) {
                if ((array_key_exists("password", $users[0])) AND (password_verify($password, $users[0]["password"]))) {
                    $_SESSION[$this->config['sessionName']] = array(
                        "userID" => $users[0]["id"],
                        "userType" => $users[0]["group"],
                    );
                    return new Response(1);
                } else {
                    return new Response(0, "Password incorrect.", $this::AUTH_ERROR_CODE_WRONG_PASS);
                }
            } else {
                return new Response(0, "User not found", $this::AUTH_ERROR_CODE_WRONG_USER);
            }
        } else {
            return $response;
        }
    }
}