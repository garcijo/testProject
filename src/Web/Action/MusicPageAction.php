<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\SpotifyFeed;
use SpotifyWebAPI\SpotifyWebAPI;
use Slim\PDO\Database;

class MusicPageAction
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
        $songs = $spotify->getMusic($user);

        $songInfo = '';
        foreach ($songs as $song) {
            $results = $this->spotify->getTrack($song['songId']);
            $songInfo .= $spotify->createTable($results);
        }

        $_SESSION['songinfo'] = $songInfo;

        return $this->renderer->render($response, 'music.phtml', $args);
    }
}
