<?php

namespace App\Controllers;
use Core\Controller as Controller;

class MainController extends Controller {

    /**
     * Action that fires when no controller action is defined
     */
    public function defaultAction() {
        $this->checkAuth();

        $this->view->render('template/header');
        $this->view->render('users/dashboard');
        $this->view->render('template/footer');
    }

}
