<?php

namespace Web\Action;

use Monolog\Logger;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\PDO\Database;
use Slim\Views\PhpRenderer;
use SpotifyWebAPI\SpotifyWebAPI;
use Web\Domain\SpotifyFeed;

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
    private $logger;

    public function __construct(
        PhpRenderer $renderer,
        SpotifyWebAPI $spotify,
        Database $db,
        Logger $logger
    ) {
        $this->renderer = $renderer;
        $this->spotify = $spotify;
        $this->db = $db;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        // Verify if user is authenticated. If false, redirect to login
        $user = $_SESSION['user'];
        if (empty($user)) {
            $this->logger->addWarning('User id is empty, user history cannot be loaded!');
        }

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
