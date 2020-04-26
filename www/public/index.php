<?php

ini_set('display_errors', 1);
define("URI", $_SERVER['REQUEST_URI']);
define("ROOT", realpath(__DIR__ . '/..'));


require ROOT . '/core/App.php';

$app = new App();
$app->init();