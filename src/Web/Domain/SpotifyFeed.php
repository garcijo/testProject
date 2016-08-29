<?php

namespace Web\Domain;

class SpotifyFeed extends Feed
{
    /**
     * Accept a valid username
     * and obtain a song recommendation from Spotify's API.
     *
     * @param string $user The current user's username
     */
    public function newSong(string $user)
    {
        do {
            $playlist = $this->spotify->getRecommendations(array(
                'limit' => 1,
                'seed_genres' => array('indie'),
                'market' => 'CA',
            ));

            $song = $playlist->tracks[0];
            $songId = $song->id;
            $exists = $this->verifySong($songId, $user);
        } while ($exists);

        return $song;
    }

    /**
     * Accept a valid username and a specific song's id
     * and verify that the song is not already saved by the user.
     *
     * @param string $songId The Spotify song id code
     * @param string $user   The current user's username
     *
     * @return bool $exists
     */
    public function verifySong(string $songId, string $user):bool
    {
        $sql = 'SELECT * FROM likes WHERE user =:user AND songId =:song_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $user, 'song_id' => $songId]);
        if (!empty($stmt->fetch())) {
            $exists = true;
        } else {
            $exists = false;
        }

        return $exists;
    }

    /**
     * Accept a valid username and a specific song's id
     * and save it into the user's liked songs list.
     *
     * @param string $songId The Spotify song id code
     * @param string $user   The current user's username
     */
    public function saveSong(string $songId, string $user)
    {
        $sql = 'INSERT INTO likes(user, songId) VALUES (:user, :songId)';
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['user' => $user, 'songId' => $songId]);
        if (!$result) {
            throw new Exception('Could not save song!');
        }
    }

    /**
     * Accept a valid username and return all the liked songs.
     *
     * @param string $user The current user's username
     *
     * @return array $songs
     */
    public function getMusic(string $user):array
    {
        $sql = 'SELECT * FROM likes WHERE user=:user';
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['user' => $user]);
        $songs = [];
        if ($result) {
            while ($song = $stmt->fetch()) {
                $songs[] = $song;
            }
        }

        return $songs;
    }

    /**
     * Accept a song object and post its values.
     *
     * @param object $song The current song
     */
    public function setSong($song)
    {
        $songId = $song->id;
        $_POST['song_id'] = $songId;
        $songName = $song->name;
        $_POST['song_name'] = $songName;
        $artist = $song->artists[0]->name;
        $_POST['artist'] = $artist;
        $songLink = $song->preview_url;
        $_POST['song_link'] = $songLink;
        $songImg = $song->album->images[1]->url;
        $_POST['song_img'] = $songImg;
        $songWidth = $song->album->images[1]->width;
        $_POST['song_width'] = $songWidth;
    }

    /**
     * Accept a song object and return its values as an array.
     *
     * @param object $song The current song
     *
     * @return array $newSong
     */
    public function getSong($song):array
    {
        $songName = $song->name;
        $artist = $song->artists[0]->name;
        $songId = $song->id;
        $songImg = $song->album->images[1]->url;
        $songLink = $song->preview_url;
        $newSong[0] = $songName;
        $newSong[1] = $artist;
        $newSong[2] = $songId;
        $newSong[3] = $songImg;
        $newSong[4] = $songLink;

        return $newSong;
    }

    /**
     * Accept a song object and return a string with the html
     * table code to display it.
     *
     * @param object $song The current song
     *
     * @return string $songInfo
     */
    public function createTable($song):string
    {
        $songInfo = '<tr id=\''.$song->preview_url.'\'><td style=\'width:125px;\'>
        <center><img src=\''.$song->album->images[0]->url.'\' style=\'width:75px;
        height:75px;\'></center></td><td>'.$song->name.'</td>
        <td>'.$song->artists[0]->name.'</td><td>'.$song->album->name.
        '</td><audio id=\'song\'><source id=\'song_link\' 
        src=\''.$song->preview_url.'\'; ?>.mp3\' type=\'audio/mp3\'>
        </audio></tr>';

        return $songInfo;
    }
}
