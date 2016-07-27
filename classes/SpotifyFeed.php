<?php
class SpotifyFeed extends Feed
{
    /**
     * Accept a valid username
     * and obtain a song recommendation from Spotify's API
     *
     * @param string $user The current user's username
     */
    public function newSong(string $user) {
        do {
            $playlist = $this->spotify->getRecommendations(array(
              'limit' => 1,
              'seed_genres' => array('indie'),
              'market' => 'CA',
            ));

            $song = $playlist->tracks[0];
            $song_id =  $song->id;
            $exists = $this->verifySong($song_id, $user);
        } while($exists);
        return $song;
    }
    
    /**
     * Accept a valid username and a specific song's id
     * and verify that the song is not already saved by the user
     *
     * @param string $song_id The Spotify song id code
     * @param string $user The current user's username
     */
    private function verifySong(string $song_id, string $user){
        $sql = "SELECT * FROM likes WHERE user =:user AND songId =:song_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user" => $user, "song_id" => $song_id,]);
        if(!empty($stmt->fetch())){
            $exists = true;
        } else {
            $exists = false;
        }
    }
    
    /**
     * Accept a song object and post its values
     *
     * @param object $song The current song
     */
    public function setSong($song) {
        $song_id =  $song->id;
        $_POST['song_id'] = $song_id;
        $song_name = $song->name;
        $_POST['song_name'] = $song_name;
        $artist = $song->artists[0]->name;
        $_POST['artist'] = $artist;
        $song_link =  $song->preview_url;
        $_POST['song_link'] = $song_link;
        $song_img =  $song->album->images[1]->url;
        $_POST['song_img'] = $song_img;
        $song_width =  $song->album->images[1]->width;
        $_POST['song_width'] = $song_width;
    }
    
    /**
     * Accept a song object and return its values as an array
     *
     * @param object $song The current song
     */
    public function getSong($song):array {
        $song_name = $song->name;
        $artist = $song->artists[0]->name;
        $song_id =  $song->id;
        $song_link =  $song->preview_url;
        $song_img =  $song->album->images[1]->url;
        $song_width =  $song->album->images[1]->width;
        $new_song[0] = $song_name;
        $new_song[1] = $artist;
        $new_song[2] = $song_id;
        $new_song[3] = $song_img;
        $new_song[4] = $song_link;
        
        return $new_song;
    }
}