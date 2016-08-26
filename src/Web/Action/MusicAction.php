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
     * @var array
     */
    private $renderer;
    private $spotify;
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

        $spotify = new SpotifyFeed($this->spotify, $this->db);
        $songs = $spotify->getMusic($user);

        foreach ($songs as $song) {
            $results = $this->spotify->getTrack($song['songId']);
            $songinfo .= $spotify->createTable($results);
        }

        echo $songinfo;
    }
}
