<?php

namespace Web\Domain;

use Slim\PDO\Database;

abstract class Mapper
{
    /**
     * @var Database
     */
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}
