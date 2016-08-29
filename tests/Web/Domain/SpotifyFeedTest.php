<?php

namespace UnitTest\Web\Domain;

use Web\Domain\UserMapper;
use Web\Domain\SpotifyFeed;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\Session;

class SpotifyFeedTest extends \PHPUnit_Framework_TestCase
{
    private $clientID = '8591df8a71ae4cd7b6547adf9048d464';
    private $clientSecret = 'ff05d85649d5455d8966b5897a79e86d';
    private $redirectURI = 'http://test1.loc/home';

    private function setupSpotify()
    {
        $spotify = new SpotifyWebAPI();
        $session = new Session($this->clientID, $this->clientSecret, $this->redirectURI);
        $session->requestCredentialsToken([]);
        $accessToken = $session->getAccessToken();
        $spotify->setAccessToken($accessToken);

        return $spotify;
    }

    public function testNewSongSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testSong = $spotifyFeed->newSong($testUserName);

        $this->assertNotEmpty($testSong);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $testSong;
    }

    public function testSaveSongSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testSong = $spotifyFeed->newSong($testUserName);
        $songId = $testSong->id;

        $spotifyFeed->saveSong($songId, $testUserName);
        $songFound = $spotifyFeed->verifySong($songId, $testUserName);

        $this->assertEquals(true, $songFound);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $testSong;
    }

    public function testGetMusicSuccessHasSongs()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testSong = $spotifyFeed->newSong($testUserName);
        $songId = $testSong->id;

        $spotifyFeed->saveSong($songId, $testUserName);
        $songsFound = $spotifyFeed->getMusic($testUserName);

        $this->assertNotEmpty($songsFound);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $songsFound;
    }

    public function testGetMusicSuccessHasNoSongs()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);

        $songsFound = $spotifyFeed->getMusic($testUserName);

        $this->assertEmpty($songsFound);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $songsFound;
    }

    public function testGetSongSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $newSong = $spotifyFeed->newSong($testUserName);

        $testSong = $spotifyFeed->getSong($newSong);
        $song_name = $newSong->name;
        $artist = $newSong->artists[0]->name;
        $song_id = $newSong->id;
        $song_link = $newSong->preview_url;
        $song_img = $newSong->album->images[1]->url;

        $this->assertNotEmpty($testSong);
        $this->assertEquals($song_name, $testSong[0]);
        $this->assertEquals($artist, $testSong[1]);
        $this->assertEquals($song_id, $testSong[2]);
        $this->assertEquals($song_img, $testSong[3]);
        $this->assertEquals($song_link, $testSong[4]);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $testSong;
    }

    public function testSetSongSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $spotify = $this->setupSpotify();
        $spotifyFeed = new SpotifyFeed($spotify, $db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper = new UserMapper($db);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $newSong = $spotifyFeed->newSong($testUserName);

        $spotifyFeed->setSong($newSong);

        $this->assertNotEmpty($_POST['song_id']);
        $this->assertNotEmpty($_POST['song_name']);
        $this->assertNotEmpty($_POST['artist']);
        $this->assertNotEmpty($_POST['song_link']);
        $this->assertNotEmpty($_POST['song_img']);
        $this->assertNotEmpty($_POST['song_width']);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $newSong;
    }
}
