<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

$currentPath = $_SERVER['PHP_SELF'];
$pathInfo = pathinfo($currentPath);
$hostName = $_SERVER['HTTP_HOST'];
$base = 'https://' . $hostName . $pathInfo['dirname'] . "/";

define('APIKEY', 'secret');
define('BOTTOKEN', '5441105233:AAEbLrN9dIXcfDcYAkFTsPobOczBvWtZJJA');
define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', 'root');
define('DBNAME', 'notivia');
define('TELEGRAMPATH', "https://api.telegram.org/bot" . BOTTOKEN);
define('BASEURL', $base);
