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

        curl_setopt($this->ch, CURLOPT_URL, 'https://spclient.wg.spotify.com/playlist-publish/v1/subscription/playlist/'.$target);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);
        return $result;
    }
public function nama()
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $ex = curl_exec($ch);
    
	preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
	return $name[2][mt_rand(0, 14) ];
    }
    
public function createAccount($email, $nama, $pass)
    {

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://spclient.wg.spotify.com/signup/public/v1/account/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"password_repeat\"\r\n\r\n$pass\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"creation_point\"\r\n\r\nhttps://login.app.spotify.com?utm_source=spotify&utm_medium=desktop-win32-store&utm_campaign=organic\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n$pass\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"referrer\"\r\n\r\n\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"key\"\r\n\r\n4c7a36d5260abca4af282779720cf631\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"gender\"\r\n\r\nmale\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"platform\"\r\n\r\ndesktop\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"birth_day\"\r\n\r\n25\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"birth_month\"\r\n\r\n2\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"creation_flow\"\r\n\r\ndesktop\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"iagree\"\r\n\r\n1\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"birth_year\"\r\n\r\n1990\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP\r\nContent-Disposition: form-data; name=\"displayname\"\r\n\r\n$nama\r\n------WebKitFormBoundaryJtAtiGwAb8W6vxpP--",
        CURLOPT_HTTPHEADER => array(
            "Host: spclient.wg.spotify.com",
            "Connection: keep-alive",
            "Origin: https://login.app.spotify.com",
            "X-Client-Id: 65b708073fc0480ea92a077233ca87bd",
            "Spotify-App-Version: 1.1.22.633.g1bab253a",
            "App-Platform: Win32",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Spotify/1.1.22.633 Safari/537.36",
            "Content-Type: multipart/form-data; boundary=----WebKitFormBoundaryJtAtiGwAb8W6vxpP;charset=utf-8",
            "Accept: */*",
            "Sec-Fetch-Site: same-site",
            "Sec-Fetch-Mode: cors",
            "Referer: https://login.app.spotify.com/index.html",
            "Accept-Language: en",
            "Content-Type: text/plain"
        ),
        ));

        $response = curl_exec($curl);
        return $response;

    }
}
?>
