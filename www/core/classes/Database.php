<?php

namespace Core;

use Exception;
use PDO;
use PDOException;

/**
 * Class Database
 * @package Core
 * Using for direct database usage
 */
class Database {

    /**
     * @var object PDO object connected to database.
     */
    protected $db;

    function __construct () {

        if (PHP_SAPI === 'cli') {
            global $cli;

            $this->db = &$cli->db;
        } else {
            global $app;

            $this->db = &$app->db;
        }

    }
}