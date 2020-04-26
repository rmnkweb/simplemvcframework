<?php

use Core\Config;


class App {

    /** @var array Configurations of cli app */
    private $config = [];

    /** @var object PDO object */
    public $db;

    /** @var object Current main controller class */
    public $controller;

    /** @var callable Current main controller action */
    public $action;

    function __construct() {

    }

    /**
     * Typical initialization scenario.
     */
    public function init() {
        $this->autoload();
        $this->config();
        $this->databaseConnect();
        $this->session();
        $this->routing();
    }

    /**
     * Preload classes from "use".
     */
    private function autoload() {
        spl_autoload_register(function ($class) {
            if (strpos($class, "Core\\") !== false) {
                $class = str_replace("Core\\", "", $class);
                if (file_exists(ROOT . '/core/classes/' . $class . '.php')) {
                    require_once ROOT . '/core/classes/' . $class . '.php';
                }
            } elseif (strpos($class, "App\\") !== false) {
                $converter = array(
                    'App\\' => '/app/',
                    'Controllers\\' => 'controllers/',
                    'Models\\' => 'models/',
                    'Views\\' => 'views/',
                );
                $class = strtr($class, $converter);
                if (file_exists(ROOT . $class . '.php')) {
                    require_once ROOT . $class . '.php';
                }
            }
        });
    }

    /**
     * Get values from configuration files and store them to local array.
     */
    private function config() {
        $config = new Config();
        $this->config = $config->getAllConfigs();
    }

    private function databaseConnect() {
        try {
            $this->db = new PDO('mysql:host=' . $this->config['database']['hostname'] . ';dbname=' . $this->config['database']['dbname'],
                $this->config['database']['username'],
                $this->config['database']['password'],
                [
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
                ]
            );

            $this->db->query('SET NAMES ' . $this->config['database']['charset']);
            $this->db->query('SET CHARACTER SET ' . $this->config['database']['charset']);

            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Database Connection Error: ' . $e->getMessage();
        }
    }

    private function session() {
        session_start();
        session_name($this->config['session']["session_name"]);
    }

    private function routing() {

        $actionName = "defaultAction";
        $appClassPrefix = "App\\Controllers\\";

        $uriParts = explode('/', URI);
        $route = [];
        $routeString = "/";
        foreach($uriParts as $uriPart) {
            if (!empty($uriPart)) {
                $route[] = $uriPart;
                $routeString .= $uriPart . "/";
            }
        }

        $customRouteActive = false;
        if (array_key_exists($routeString, $this->config["custom_routes"])) {
            // Routing from custom_routes configuration file
            $customRouteArray = $this->config["custom_routes"][$routeString];
            if (class_exists($customRouteArray["controller"])) {
                $controllerName = $customRouteArray["controller"];
                $this->controller = new $controllerName();
                $customRouteActive = true;

                if (isset($customRouteArray["action"])) {
                    $actionName = $customRouteArray["action"];
                } else {
                    $actionName = "defaultAction";
                }
                if (method_exists($this->controller, $actionName) && is_callable(array($this->controller, $actionName))) {
                    $this->controller->$actionName();
                } else {
                    echo "[Error] Custom routing: No action was found!";
                }
            }
        }
        if ($customRouteActive === false) {
            // Routing from URI
            // getting controller
            if (count($route) > 0) {
                $controllerName = $appClassPrefix . ucfirst($route[0]) . "Controller";
            } else {
                $controllerName = $appClassPrefix . "MainController";
            }
            if (class_exists($controllerName)) {
                $this->controller = new $controllerName();
            } else {
                $controllerName = $appClassPrefix . "Error404Controller";
            }
            $this->controller = new $controllerName();

            // getting action
            if (count($route) >= 2) {
                $actionName = $route[1] . "Action";
            }
            if (method_exists($this->controller, $actionName) && is_callable(array($this->controller, $actionName))) {
                $this->action = $actionName;
            } else {
                $actionName = "defaultAction";
            }

            $this->controller->$actionName();
        }
    }

    protected function getDatabasePointer() {
        $pointer = &$this->db;
        return $pointer;
    }
    
}