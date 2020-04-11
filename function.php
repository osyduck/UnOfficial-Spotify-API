<?php

class spotify{


public function __construct()
    {        
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    }

public function getCookies()
    {
        $headers = array();
        //$headers[] = "Accept-Encoding: gzip, deflate, sdch, br";
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

public function tryLogin($token, $username, $password)
    {   
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
        if(preg_match("/displayName/", $result)){
            preg_match_all('/^Set-Cookie:\s*([^;\r\n]*)/mi', $result, $kue);
            $cookie = "";
            for($i=0; $i<count($kue[1])-1; $i++){
                $cookie .= $kue[1][$i].";";
            }
            return array(true, $cookie);
        }else{
            return array(false);
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

public function followUser($token, $target)
    {
        $headers = array();
        $headers[] = "User-Agent: Spotify/8.5.51 Android/26 (Custom Tablet)";
        $headers[] = "X-Client-Id: 06f21c6a8b7b41279bffabb9537d8286";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: spclient.wg.spotify.com";
        $headers[] = "Connection: close";

        curl_setopt($this->ch, CURLOPT_URL, 'https://spclient.wg.spotify.com/socialgraph/v2/following?format=json');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '{"target_uris": ["spotify:user:'.$target.'"]}');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function followArtist($token, $target)
    {
        $headers = array();
        $headers[] = "User-Agent: Spotify/8.5.51 Android/26 (Custom Tablet)";
        $headers[] = "X-Client-Id: 06f21c6a8b7b41279bffabb9537d8286";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: spclient.wg.spotify.com";
        $headers[] = "Connection: close";

        curl_setopt($this->ch, CURLOPT_URL, 'https://spclient.wg.spotify.com/socialgraph/v2/following?format=json');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '{"target_uris": ["spotify:artist:'.$target.'"]}');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

public function followPlaylist($token, $target)
    {
        $headers = array();
        $headers[] = "User-Agent: Spotify/8.5.51 Android/26 (Custom Tablet)";
        $headers[] = "X-Client-Id: 06f21c6a8b7b41279bffabb9537d8286";
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
        $headers[] = "Accept: text/plain";
        $headers[] = "Spotify-App-Version: 8.5.51";
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Host: spclient.wg.spotify.com";
        $headers[] = "Connection: close";

        curl_setopt($this->ch, CURLOPT_URL, 'https://spclient.wg.spotify.com/playlist-publish/v1/subscription/playlist/37i9dQZF1DXcBWIGoYBM5M');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }

}

?>
