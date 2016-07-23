<?php
// Routes
$app->get('/login', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'login.phtml', $args);
});

$app->get('/home', function ($request, $response, $args) {
    // Render index view
    if (isset($_SESSION['user'])) {
      return $this->renderer->render($response, 'home.phtml', $args);
   } else {
      return $this->renderer->render($response, 'login.phtml', $args);
    }
});

$app->post('/signin', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
    $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);
    // work out the component
    $user_mapper = new UserMapper($this->db);
    $user = $user_mapper->loginUser($user_email, $user_pass);
    $user_name = $user->getName();
    $_SESSION['user'] = $user_name;
    $response = $response->withRedirect("/home");
    return $response;
});

$app->post('/signup', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
    $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);
    // work out the component
    $user_mapper = new UserMapper($this->db);
    $user = $user_mapper->createUser($user_name, $user_email, $user_pass);
    $_SESSION['user'] = $user_name;
    $response = $response->withRedirect("/home");
    return $response;
});

/**
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
**/