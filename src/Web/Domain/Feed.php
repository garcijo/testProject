<?php

namespace Web\Domain;

use SpotifyWebAPI\SpotifyWebAPI;
use Slim\PDO\Database;

abstract class Feed
{
    /**
     * @var SpotifyWebAPI
     */
    protected $spotify;
    /**
     * @var Database
     */
    protected $db;

    public function __construct(SpotifyWebAPI $spotify, Database $db)
    {
        $this->spotify = $spotify;
        $this->db = $db;
    }
}
