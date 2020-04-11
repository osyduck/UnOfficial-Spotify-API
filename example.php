<?php

require "function.php";

$spotify = new spotify();


$username = "";
$password = "";
$cookie = $spotify->getCookies();
$login = $spotify->tryLogin($cookie, $username, $password);
$token = $spotify->getToken($login[1]);

$json = json_decode($token[1], true);

$token = $json['accessToken'];

print_r($spotify->followUser($token, "osyduck"));

?>