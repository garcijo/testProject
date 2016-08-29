<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\SpotifyFeed;
use SpotifyWebAPI\SpotifyWebAPI;
use Slim\PDO\Database;

class MusicAction
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
        $data = $request->getParsedBody();
        $user = filter_var($data['user'], FILTER_SANITIZE_STRING);

        // get all the user's music
        $spotify = new SpotifyFeed($this->spotify, $this->db);
        $songs = $spotify->getMusic($user);

        // generate songs' table html code
        foreach ($songs as $song) {
            $results = $this->spotify->getTrack($song['songId']);
            $songInfo .= $spotify->createTable($results);
        }

        echo $songInfo;
    }
}
