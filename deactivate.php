<?php

require_once('constants.php');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.0 405 Method Not Allowed', TRUE, 405);
    exit();
}

if (!isset($_GET['apikey'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

if ($_GET['apikey'] !== APIKEY) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

$path = "https://api.telegram.org/bot" . BOTTOKEN;

header('Content-Type: application/json; charset=utf-8');
echo file_get_contents($path . "/deleteWebhook");
