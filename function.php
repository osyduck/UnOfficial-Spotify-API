<?php

class spotify{


public function __construct()
    {        
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    }

private function getCookies()
    {
        $headers = array();
        $headers[] = "Accept-Language: it-IT,it;q=0.8,en-US;q=0.6,en;q=0.4";
        $headers[] = "Upgrade-Insecure-Requests: 1";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
        $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
        $headers[] = "Cache-Control: max-age=0";
        $headers[] = "Connection: keep-alive";
        curl_setopt($this->ch, CURLOPT_URL, 'https://accounts.spotify.com/');
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        $result = curl_exec($this->ch);
        preg_match('/^Set-Cookie:\s*(csrf[^;]*)/mi', $result, $m);
        parse_str($m[1], $cookies);
        $token = $cookies['csrf_token'];
        return $token;
    }

public function tryLogin($username, $password)
    {   
        $token = $this->getCookies();
        $bon_cookie = base64_encode("0|0|0|0|1|1|1|1");
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) FxiOS/1.0 Mobile/12F69 Safari/600.1.4";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: application/json, text/plain";
        $headers[] = "Cookie: sp_t=; sp_new=1; __bon=$bon_cookie; _gat=1; __tdev=VV4fjDj7; __tvis=BGWgw2Xk; spot=; csrf_token=$token; remember=7n4qwa5jrogiu7bysts679i3d";
        curl_setopt($this->ch, CURLOPT_URL, 'https://accounts.spotify.com/api/login');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, "remember=false&username=$username&password=$password&csrf_token=$token&continue=https%3A%2F%2Fopen.spotify.com%2F");
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HEADER, true);

        $result = curl_exec($this->ch);
        
        if(preg_match('/result":"ok/', $result)){
            preg_match_all('/^Set-Cookie:\s*([^;\r\n]*)/mi', $result, $kue);
            $cookie = "";
            for($i=0; $i<count($kue[1])-1; $i++){
                $cookie .= $kue[1][$i].";";
            }
            return array(true, $cookie);
        }else{
            return array(false, $result);
        }

    }
public function getToken($cookie)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) FxiOS/1.0 Mobile/12F69 Safari/600.1.4";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: application/json, text/plain";
        $headers[] = "Cookie: $cookie";
        curl_setopt($this->ch, CURLOPT_URL, 'https://open.spotify.com/get_access_token?reason=transport&productType=web_player');
        curl_setopt($this->ch, CURLOPT_POST, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        if(preg_match("/accessToken/", $result)){
            return array(true, $result);
        }else{
            return array(false);
        }
    }

public function followUser($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following?type=user&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function isFollowUser($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following/contains?type=user&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function unfollowUser($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following?type=user&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }   

public function followArtist($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following?type=artist&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function isFollowArtist($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following/contains?type=artist&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }
    
public function unfollowArtist($token, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/following?type=artist&ids='.$ids);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function followPlaylist($token, $playlist_id)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/playlists/'.$playlist_id.'/followers');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '{"public":false}');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function isFollowPlaylist($token, $playlist_id, $ids)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/playlists/'.$playlist_id.'/followers/contains?ids='.$ids);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }    
 
public function unfollowPlaylist($token, $playlist_id)
    {
        $headers = array();
        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36";
	    $headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: api.spotify.com";
        $headers[] = "Connection: keep-alive";

        curl_setopt($this->ch, CURLOPT_URL, 'https://api.spotify.com/v1/playlists/'.$playlist_id.'/followers');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }
    
public function createAccount($email, $name, $pass)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://spclient.wg.spotify.com:443/signup/public/v1/account/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => "email=$email&password_repeat=$pass&password=$pass&key=142b583129b2df829de3656f9eb484e6&gender=male&platform=Android-ARM&creation_point=client_mobile&birth_day=12&birth_month=5&iagree=true&app_version=849800892&birth_year=1990&displayname=$name",
        CURLOPT_HTTPHEADER => array(
            "Host: spclient.wg.spotify.com",
            "User-Agent: Spotify/8.4.98 Android/26 (Custom Tablet)",
            "Connection: keep-alive",
            "Content-Type: application/x-www-form-urlencoded"
        ),
        ));

        $response = curl_exec($curl);
        return $response;

    }
}
?>
