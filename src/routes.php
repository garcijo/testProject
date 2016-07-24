<?php
// Routes
$app->get('/login', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'login.phtml', $args);
});

$app->get('/home', function ($request, $response, $args) {
    // Verify if user is authenticated
    if (isset($_SESSION['user'])) {
        $spotify = new SpotifyWebAPI\SpotifyWebAPI();
        $session = new SpotifyWebAPI\Session('5057ad4ae2c84bd9be39b03e6e72f4dc', 
        '850e396f29264b718937c88f376acaea', 'http://localhost:8080/home');
        $scopes = array();
        $session->requestCredentialsToken($scopes);
        $accessToken = $session->getAccessToken();
        $spotify->setAccessToken($accessToken);

        $playlist = $spotify->getRecommendations(array(
          'limit' => 1,
          'seed_genres' => array('indie'),
          'market' => 'CA',
        ));
        
        $song = $playlist->tracks[0];
        $song_name = $song->name;
        $_SESSION['song_name'] = $song_name;
        $artist = $song->artists[0]->name;
        $_SESSION['artist'] = $artist;
        $song_id =  $song->id;
        $_SESSION['song_id'] = $song_id;
        $song_link =  $song->preview_url;
        $_SESSION['song_link'] = $song_link;
        $song_img =  $song->album->images[1]->url;
        $_SESSION['song_img'] = $song_img;
        $song_width =  $song->album->images[1]->width;
        $_SESSION['song_width'] = $song_width;
        
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

$app->get('/logout', function ($request, $response, $args) {
    session_unset();
    session_destroy();
    return $this->renderer->render($response, 'login.phtml', $args);
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

$app->post('/ajax', function($request, $response) {
    $data = $request->getParsedBody();
    $user_data = [];
    $action = filter_var($data['action'], FILTER_SANITIZE_STRING);
    $song_id = filter_var($data['id'], FILTER_SANITIZE_STRING);
    $user = filter_var($data['user'], FILTER_SANITIZE_STRING);
    
    if($action == 'like'){
        $sql = "INSERT INTO likes(user, songId) VALUES
            (:user, :songId)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "user" => $user,
            "songId" => $song_id,
        ]);
        if(!$result) {
            throw new Exception("Could not save song!");
        }
    }
    
    $spotify = new SpotifyWebAPI\SpotifyWebAPI();
    $session = new SpotifyWebAPI\Session('5057ad4ae2c84bd9be39b03e6e72f4dc', 
    '850e396f29264b718937c88f376acaea', 'http://localhost:8080/home');
    $scopes = array();
    $session->requestCredentialsToken($scopes);
    $accessToken = $session->getAccessToken();
    $spotify->setAccessToken($accessToken);

    $playlist = $spotify->getRecommendations(array(
      'limit' => 1,
      'seed_genres' => array('indie'),
      'market' => 'CA',
    ));

    $song = $playlist->tracks[0];
    $song_name = $song->name;
    $artist = $song->artists[0]->name;
    $song_id =  $song->id;
    $song_link =  $song->preview_url;
    $song_img =  $song->album->images[1]->url;
    $song_width =  $song->album->images[1]->width;
    $new_song[0] = $song_name;
    $new_song[1] = $artist;
    $new_song[2] = $song_id;
    $new_song[3] = $song_img;
    $new_song[4] = $song_link;

    echo json_encode($new_song);
});

/**
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
**/