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
$app->get('/login', LoginPageAction::class)->setName('login');
$app->get('/home', HomePageAction::class)->setName('home');
$app->get('/music', MusicPageAction::class)->setName('music');
$app->post('/signin', LoginAction::class)->setName('signin');
$app->get('/logout', LogoutAction::class)->setName('logout');
$app->post('/signup', SignupAction::class)->setName('signup');
$app->post('/ajax', NewMusicAction::class)->setName('ajax');
$app->post('/ajaxMusic', MusicAction::class)->setName('ajaxMusic');

$app->get('/[{name}]', function ($request, $response, $args) {
    $response = $response->withRedirect('/login');

    return $response;
});
