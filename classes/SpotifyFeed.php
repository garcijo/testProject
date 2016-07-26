<?php
class SpotifyFeed extends Feed
{
    //Looks up a new song through Spotify's api
    public function getPlaylist($user) {
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
        return $playlist;
    }
    
    private function verifySong($song_id, $user){
        $sql = "SELECT * FROM likes WHERE user =:user AND songId =:song_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user" => $user, "song_id" => $song_id,]);
        if(!empty($stmt->fetch())){
            $exists = true;
        } else {
            $exists = false;
        }
    }
    
    //
    public function setSong($playlist) {
        $song = $playlist->tracks[0];
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
    
    public function getSong($playlist):array {
        $song = $playlist->tracks[0];
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