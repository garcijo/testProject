<?php
abstract class Feed {
    protected $spotify;
    public function __construct($spotify) {
        $this->spotify = $spotify;
    }
}