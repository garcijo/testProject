<?php

use Web\Action\LoginAction;
use Web\Action\LoginPageAction;
use Web\Action\HomePageAction;
use Web\Action\MusicPageAction;
use Web\Action\LogoutAction;
use Web\Action\SignupAction;
use Web\Action\NewMusicAction;
use Web\Action\MusicAction;

//login & registration page
$app->get('/login', LoginPageAction::class);
$app->get('/home', HomePageAction::class);
$app->get('/music', MusicPageAction::class);
$app->post('/signin', LoginAction::class);
$app->get('/logout', LogoutAction::class);
$app->post('/signup', SignupAction::class);
$app->post('/ajax', NewMusicAction::class);
$app->post('/ajaxMusic', MusicAction::class);

$app->get('/[{name}]', function ($request, $response, $args) {
    $response = $response->withRedirect('/login');

    return $response;
});
