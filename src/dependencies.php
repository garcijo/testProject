<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// database
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// spotify
$container['spotify'] = function ($c) {
    $spotify = new SpotifyWebAPI\SpotifyWebAPI();
    $session = new SpotifyWebAPI\Session('5057ad4ae2c84bd9be39b03e6e72f4dc', 
    '850e396f29264b718937c88f376acaea', 'http://localhost:8080/home');
    $scopes = array();
    $session->requestCredentialsToken($scopes);
    $accessToken = $session->getAccessToken();
    $spotify->setAccessToken($accessToken);
    return $spotify;
};
