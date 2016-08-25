<?php

use Slim\Http\Response;
use Slim\Http\Request;

//login & registration page

$app->get('/login', function ($request, $response, $args) {
    // Verify if user is authenticated. If true, redirect to home
    if (isset($_SESSION['user'])) {
        $response = $response->withRedirect("/home");
        return $response;
    } else {

        return $this->renderer->render($response, 'login.phtml', $args);
    }
});

$app->get('/home', function ($request, $response, $args) {
    // Verify if user is authenticated. If false, redirect to login
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $spotify = new SpotifyFeed($this->spotify, $this->db);
        $song = $spotify->newSong($user);
        $spotify->setSong($song);
        return $this->renderer->render($response, 'home.phtml', $args);
    } else {
        $response = $response->withRedirect("/login");
        return $response;
    }
});

$app->get('/music', function ($request, $response, $args) {
    // Verify if user is authenticated. If false, redirect to login
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];

        $spotify = new SpotifyFeed($this->spotify, $this->db);
        $songs = $spotify->getMusic($user);

        $songinfo = "";
        foreach ($songs as $song) {
            $results = $this->spotify->getTrack($song[1]);
            $songinfo .= $spotify->createTable($results);
        }

        $_SESSION['songinfo'] = $songinfo;
        return $this->renderer->render($response, 'music.phtml', $args);
    } else {
        $response = $response->withRedirect("/login");
        return $response;
    }

});

$app->post('/signin', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
    $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);

    if (empty($user_email) || empty($user_pass)) {
        $_POST['error'] = '<p class="error">Incorrect login!</p>';
        return $this->renderer->render($response, 'login.phtml', $args);
    } else {
        $user_mapper = new UserMapper($this->db);
        $user = $user_mapper->loginUser($user_email, $user_pass);
        $user_name = $user->getName();
        if (empty($user_name)) {
            $_POST['error'] = '<p class="error">Incorrect login!</p>';
            return $this->renderer->render($response, 'login.phtml', $args);
        } else {
            $_SESSION['user'] = $user_name;

            $response = $response->withRedirect("/home");
            return $response;
        }
    }
});

$app->get('/logout', function ($request, $response, $args) {
    session_unset();
    session_destroy();
    $response = $response->withRedirect("/login");
    return $response;
});

$app->get('/signup', function ($request, $response, $args) {
    $response = $response->withRedirect("/login");
    return $response;
});

$app->post('/signup', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
    $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);
    // work out the component
    $user_mapper = new UserMapper($this->db);
    //First check the email doesn't exist yet
    $user = $user_mapper->searchUser($user_email);
    if (!empty($user->getName())) {
        $_POST['error'] = '<p class="error">That email address already exists!</p>';
        return $this->renderer->render($response, 'login.phtml', $args);
    } else {
        $user = $user_mapper->createUser($user_name, $user_email, $user_pass);
        $_SESSION['user'] = $user_name;
        $response = $response->withRedirect("/home");
        return $response;
    }
});

$app->post('/ajax', function ($request, $response) {
    $data = $request->getParsedBody();
    $user_data = [];
    $action = filter_var($data['action'], FILTER_SANITIZE_STRING);
    $song_id = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $user = filter_var($data['user'], FILTER_SANITIZE_STRING);

    $spotify = new SpotifyFeed($this->spotify, $this->db);
    if ($action == 'like') {
        $spotify->saveSong($song_id, $user);
    }
    $song = $spotify->newSong($user);
    $new_song = $spotify->getSong($song);

    echo json_encode($new_song);
});

$app->post('/ajaxMusic', function ($request, $response, $a) {
    $data = $request->getParsedBody();
    $user = filter_var($data['user'], FILTER_SANITIZE_STRING);

    $spotify = new SpotifyFeed($this->spotify, $this->db);
    $songs = $spotify->getMusic($user);

    foreach ($songs as $song) {
        $results = $this->spotify->getTrack($song[1]);
        $songinfo .= $spotify->createTable($results);
    }

    echo $songinfo;
});

$app->get('/[{name}]', function ($request, $response, $args) {
    $response = $response->withRedirect("/login");
    return $response;
});