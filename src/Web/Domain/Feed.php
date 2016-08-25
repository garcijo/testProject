<?php

namespace Web\Domain;

abstract class Feed
{
    protected $spotify;
    
    public function __construct($spotify, $db)
    {
        $this->spotify = $spotify;
        $this->db = $db;
    }
}
