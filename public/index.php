<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($classname) {
    require ("../src/Domain/" . $classname . ".php");
});

session_start();

// Instantiate the app

$app = new \Slim\App();

require __DIR__ . '/../src/config/dependencies.php';
require __DIR__ . '/../src/config/middleware.php';
require __DIR__ . '/../src/config/routes.php';

// Run app
$app->run();
