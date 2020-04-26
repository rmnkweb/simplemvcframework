<?php


namespace Core;


class Config {
    protected $session;
    protected $database;
    protected $custom_routes;

    public function __construct() {
        $this->session = require_once ROOT . '/core/config/session.php';;
        $this->database = require_once ROOT . '/core/config/database.php';;
        $this->custom_routes = require_once ROOT . '/core/config/custom_routes.php';;
    }

    final function getAllConfigs() {
        $configs = [
            "session" => $this->session,
            "database" => $this->database,
            "custom_routes" => $this->custom_routes,
        ];
        return $configs;
    }

    final function getConfigBySlug($slug = "") {
        if ($slug === "session") {
            return $this->session;
        } elseif ($slug === "database") {
            return $this->database;
        } elseif ($slug === "custom_routes") {
            return $this->custom_routes;
        } else {
            return false;
        }
    }
}