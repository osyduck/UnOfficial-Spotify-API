<?php

require "function.php";

$spotify = new spotify();


$username = "";
$password = "";
$login = $spotify->tryLogin($username, $password);
$token = $spotify->getToken($login[1]);

$json = json_decode($token[1], true);

$token = $json['accessToken'];

print_r($spotify->followUser($token, "osyduck"));
print_r($spotify->isFollowUser($token, "osyduck"));
?>