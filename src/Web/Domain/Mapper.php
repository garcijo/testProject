<?php

namespace Web\Domain;

abstract class Mapper
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
}
