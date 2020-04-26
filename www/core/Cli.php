<?php

use Core\Config;

class Cli {

    /** @var array Configurations of cli app */
    private $config = [];
    public $db;

    public function init($args) {
        $count_args = count($args);
        if ($count_args === 1) {
            if ($args[0] === "cli") {
                echo "Hello! It's command line tool for this simple MVC implementation." . PHP_EOL;
                $this->commandList();
            }
        } else {
            $this->autoload();
            $this->config();
            $this->databaseConnect();

            if ($args[1] === "commands") {
                $this->commandList();
            } elseif ($args[1] === "user") {
                $this->command_user();
            } elseif ($args[1] === "user:list") {
                $this->command_user_list();
            } elseif ($args[1] === "user:register") {
                $this->command_user_register();
            } elseif ($args[1] === "test") {
                if ((isset($args[2])) AND (!(empty($args[2])))) {
                    $this->command_test($args[2]);
                } else {
                    $this->command_test_empty();
                }
            } else {
                $this->command_404();
            }
        }
    }

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

    private function commandList() {
        echo "Available commands:" . PHP_EOL
            . " commands - list of available commands" . PHP_EOL
            . " user" . PHP_EOL
            . " user:list - list of registred users" . PHP_EOL
            . " user:register [username] [password]" . PHP_EOL
            . " test [TestName]" . PHP_EOL
        ;
    }

    private function command_user() {
        echo "Subcommands for user:" . PHP_EOL
            . " user:list - list of registred users" . PHP_EOL
            . " user:register [username] [password]" . PHP_EOL
        ;
    }
    private function command_user_list() {
        $this->autoload();

        $users = new App\Models\Users();
        $userList = $users->getUser();
        print_r($userList);
    }
    private function command_user_register() {
        $this->autoload();
    }

    private function command_test($classname) {
        $this->autoload();
        $this->db->beginTransaction();

        $filepath = ROOT . "/app/tests/" . $classname . ".php";
        if (file_exists($filepath)) {
            require $filepath;

            $classname = "App\\Tests\\" . $classname;
            if ($testObject = new $classname()) {
                echo "Launching test " . $classname . "." . PHP_EOL;
                $testResult = $testObject->test();
                $testSummary = $this->getTestSummary($testResult);
                echo $testSummary;
            } else {
                die("Unable to resolve class " . $classname . " ." . PHP_EOL);
            }
        }

        $this->db->rollBack();
    }
    private function command_test_empty() {
        echo "test command require parameter TestName (php class file located in /app/tests/)" . PHP_EOL;
        echo "test [TestName]" . PHP_EOL;
    }

    private function command_404() {
        echo "Such command not found";
    }


    private function getTestSummary($testResults) {
        $log = "";
        foreach($testResults as $testName => $testResult) {
            $log .= "Test " . $testName . " results:"  . PHP_EOL;
            foreach($testResult as $caseName => $testCase) {
                if (isset($testCase["status"])) {
                    $log .= " - " . $caseName;
                    if ($testCase["status"] === 1) {
                        $log .= ": success!" . PHP_EOL;
                    } else {
                        if ($testCase["status"] === 0) {
                            $log .= ": error!";
                        } else {
                            $log .= ": warning!";
                        }
                        if (isset($testCase["error"])) {
                            $log .= " ";
                            if (isset($testCase["error"]["code"])) {
                                $log .= "[" . $testCase["error"]["code"] . "] ";
                            }
                            if (isset($testCase["error"]["message"])) {
                                $log .= $testCase["error"]["message"];
                            }
                        }
                        $log .= PHP_EOL;
                    }
                } else {
                    $log .= "- " . $caseName . " failed";
                }
            }
        }

        return $log;
    }
}