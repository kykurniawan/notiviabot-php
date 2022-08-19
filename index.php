<?php

header('Content-Type: application/json; charset=utf-8');

http_response_code(200);

echo json_encode([
    'name' => 'notiviabot',
    'description' => 'send realtime notification via telegram bot',
    'github' => 'https://github.com/kykurniawan'
]);

exit();
