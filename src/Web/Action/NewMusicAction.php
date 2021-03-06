<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\SpotifyFeed;
use SpotifyWebAPI\SpotifyWebAPI;
use Slim\PDO\Database;

class NewMusicAction
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
        $action = filter_var($data['action'], FILTER_SANITIZE_STRING);
        $songId = filter_var($data['id'], FILTER_SANITIZE_STRING);
        $user = filter_var($data['user'], FILTER_SANITIZE_STRING);

        $spotify = new SpotifyFeed($this->spotify, $this->db);
        if ($action == 'like') {
            $spotify->saveSong($songId, $user);
        }
        $song = $spotify->newSong($user);
        $newSong = $spotify->getSong($song);

        echo json_encode($newSong);
    }
}
