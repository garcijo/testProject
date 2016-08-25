<?php

use Interop\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Slim\Views\PhpRenderer;
use Slim\PDO\Database;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\Session;
use Symfony\Component\Yaml\Yaml;
use Web\Action\LoginAction;
use Web\Action\LoginPageAction;
use Web\Action\HomePageAction;
use Web\Action\MusicPageAction;
use Web\Action\LogoutAction;
use Web\Action\SignupAction;
use Web\Action\NewMusicAction;
use Web\Action\MusicAction;

// DIC configuration
$container = $app->getContainer();

$container['config'] = function () {
    $configPath = __DIR__ . '/environment/main.yaml';

    if (!file_exists($configPath)) {
        throw new Exception('Config file main.yaml does not exists.');
    }

    return Yaml::parse(file_get_contents($configPath));
};

foreach ($container->get('config')['settings'] as $key => $value) {
    $container['settings'][$key] = $value;
}

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('config');
    $settings = $settings['renderer'];
    $renderer = new PhpRenderer($settings['template_path']);
    return $renderer;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $settings = $settings['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
    return $logger;
};

// database
$container['db'] = function (ContainerInterface $container) {
    $dbConfig = $container->get('config')['db'];
    $dsn = sprintf('%s:host=%s;dbname=%s;charset=%s', $dbConfig['dbType'], $dbConfig['host'], $dbConfig['dbName'],
        $dbConfig['charset']);

    return new Database($dsn, $dbConfig['username'], $dbConfig['password']);
};

// spotify
$container['spotify'] = function (ContainerInterface $container) {
    $spotify = new SpotifyWebAPI();
    $spotifyConfig = $container->get('config')['spotify'];
    $session = new Session($spotifyConfig['clientId'], $spotifyConfig['secretId'], $spotifyConfig['redirectUri']);
    $scopes = array();
    $session->requestCredentialsToken($scopes);
    $accessToken = $session->getAccessToken();
    $spotify->setAccessToken($accessToken);
    return $spotify;
};

// Login page
$container[LoginPageAction::class] = function (ContainerInterface $container) {
    return new LoginPageAction(
        $container->get('renderer')
    );
};

$container[LoginAction::class] = function (ContainerInterface $container) {
    return new LoginAction(
        $container->get('renderer'),
        $container->get('db')
    );
};

// Home page
$container[HomePageAction::class] = function (ContainerInterface $container) {
    return new HomePageAction(
        $container->get('renderer'),
        $container->get('spotify'),
        $container->get('db')
    );
};
$container[NewMusicAction::class] = function (ContainerInterface $container) {
    return new NewMusicAction(
        $container->get('renderer'),
        $container->get('spotify'),
        $container->get('db')
    );
};

// Music page
$container[MusicPageAction::class] = function (ContainerInterface $container) {
    return new MusicPageAction(
        $container->get('renderer'),
        $container->get('spotify'),
        $container->get('db')
    );
};
$container[MusicAction::class] = function (ContainerInterface $container) {
    return new MusicAction(
        $container->get('renderer'),
        $container->get('spotify'),
        $container->get('db')
    );
};

// Logout
$container[LogoutAction::class] = function (ContainerInterface $container) {
    return new LogoutAction(
        $container->get('renderer')
    );
};

// Signup
$container[SignupAction::class] = function (ContainerInterface $container) {
    return new SignupAction(
        $container->get('renderer'),
        $container->get('db')
    );
};
