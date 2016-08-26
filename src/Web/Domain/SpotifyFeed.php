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
     * @param string $user    The current user's username
     */
    private function verifySong(string $songId, string $user)
    {
        $sql = 'SELECT * FROM likes WHERE user =:user AND songId =:song_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $user, 'song_id' => $songId]);
        if (!empty($stmt->fetch())) {
            $exists = true;
        } else {
            $exists = false;
        }
    }

    /**
     * Accept a valid username and a specific song's id
     * and save it into the user's liked songs list.
     *
     * @param string $songId The Spotify song id code
     * @param string $user    The current user's username
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
        $song_id = $song->id;
        $_POST['song_id'] = $song_id;
        $song_name = $song->name;
        $_POST['song_name'] = $song_name;
        $artist = $song->artists[0]->name;
        $_POST['artist'] = $artist;
        $song_link = $song->preview_url;
        $_POST['song_link'] = $song_link;
        $song_img = $song->album->images[1]->url;
        $_POST['song_img'] = $song_img;
        $song_width = $song->album->images[1]->width;
        $_POST['song_width'] = $song_width;
    }

    /**
     * Accept a song object and return its values as an array.
     *
     * @param object $song The current song
     */
    public function getSong($song):array
    {
        $song_name = $song->name;
        $artist = $song->artists[0]->name;
        $song_id = $song->id;
        $song_link = $song->preview_url;
        $song_img = $song->album->images[1]->url;
        $song_width = $song->album->images[1]->width;
        $new_song[0] = $song_name;
        $new_song[1] = $artist;
        $new_song[2] = $song_id;
        $new_song[3] = $song_img;
        $new_song[4] = $song_link;

        return $new_song;
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
