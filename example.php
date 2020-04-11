<?php

require "function.php";

$spotify = new spotify();

$email = "exampleregister@yandex.com";
$password = "password6969";
$nama = $spotify->nama();
$register = $spotify->createAccount($email, $nama, $password);

var_dump($register);

?>