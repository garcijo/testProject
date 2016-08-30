<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\SpotifyFeed;
use SpotifyWebAPI\SpotifyWebAPI;
use Slim\PDO\Database;

class HomePageAction
{
    /**
     * @var PhpRenderer
     */
    private $renderer;
    /**
     * @var SpotifyWebAPI
     */
    private $spotify;
    /**
     * @var Database
     */
    private $db;

    public function __construct(
        PhpRenderer $renderer,
        SpotifyWebAPI $spotify,
        Database $db
    ) {
        $this->renderer = $renderer;
        $this->spotify = $spotify;
        $this->db = $db;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        // Verify if user is authenticated. If false, redirect to login
        $user = $_SESSION['user'];
        $spotify = new SpotifyFeed($this->spotify, $this->db);
        $song = $spotify->newSong($user);
        //$spotify->setSong($song);

        return $this->renderer->render($response, 'home.phtml', $args);
    }
}
