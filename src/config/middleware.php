<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->add(function (Request $request, Response $response, callable $next) {
    $route = $request->getAttribute('route');
    $name = $route->getName();

    if (!isset($_SESSION['user']) && !in_array($name, ['login', 'signin', 'logout', ''])) {
        $response = $response->withRedirect('/login');

        return $response;
    } elseif (isset($_SESSION['user']) && in_array($name, ['login', 'signin', 'signup', ''])) {
        $response = $response->withRedirect('/home');

        return $response;
    }

    return $next($request, $response);
});
