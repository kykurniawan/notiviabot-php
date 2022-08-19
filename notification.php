<?php

header('Content-Type: application/json; charset=utf-8');

require_once('constants.php');
require_once('./app/Subscriber.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$data = json_decode(file_get_contents("php://input"), TRUE);

if (!isset($data['body'])) {
    http_response_code(400);
    echo json_encode([
        'code' => 400,
        'success' => false,
        'message' => 'bad request',
        'error' => [
            'body' => 'The notification body is required.',
        ]
    ]);
    exit();
}

if ($data['body'] == '') {
    http_response_code(400);
    echo json_encode([
        'code' => 400,
        'success' => false,
        'message' => 'bad request',
        'error' => [
            'body' => 'The notification body cannot be empty.',
        ]
    ]);
    exit();
}

if (strlen($data['body']) > 2000) {
    http_response_code(400);
    echo json_encode([
        'code' => 400,
        'success' => false,
        'message' => 'bad request',
        'error' => [
            'body' => 'The notification body cannot be longer than 2000 characters.',
        ]
    ]);
    exit();
}

if (!isset($data['subscriber'])) {
    http_response_code(400);
    echo json_encode([
        'code' => 400,
        'success' => false,
        'message' => 'bad request',
        'error' => [
            'subscriber' => 'Subscriber is required.',
        ]
    ]);
    exit();
}

if (is_string($data['subscriber'])) {
    if ($data['subscriber'] == '') {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'success' => false,
            'message' => 'bad request',
            'error' => [
                'subscriber' => 'Subscriber cannot be empty.',
            ]
        ]);
        exit();
    }

    $subscriber = new Subscriber;

    $findSubscriber = $subscriber->find($data['subscriber']);
    if ($findSubscriber == null) {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'success' => false,
            'message' => 'bad request',
            'error' => [
                'subscriber' => 'Subscriber not found.',
            ]
        ]);
        exit();
    }

    if ($findSubscriber->chat_id == null) {
        http_response_code(200);
        echo json_encode([
            'code' => 200,
            'success' => false,
            'message' => 'cannot send notification because subscription was stoped',
        ]);
        exit();
    }

    $textMessage = urlencode($data['body']);
    file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $findSubscriber->chat_id . "&text=" . $textMessage);

    http_response_code(200);
    echo json_encode([
        'code' => 200,
        'success' => true,
        'message' => 'notification sent',
    ]);
    exit();
}

if (is_array($data['subscriber'])) {
    if (sizeof($data['subscriber']) < 1) {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'success' => false,
            'message' => 'bad request',
            'error' => [
                'subscriber' => 'Subscriber cannot be empty.',
            ]
        ]);
        exit();
    }

    $subscriber = new Subscriber;

    foreach ($data['subscriber'] as $subscriberId) {

        $findSubscriber = $subscriber->find($subscriberId);
        if ($findSubscriber != null) {
            if ($findSubscriber->chat_id != null) {
                $textMessage = urlencode($data['body']);
                file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $findSubscriber->chat_id . "&text=" . $textMessage);
            }
        }
    }
    http_response_code(200);
    echo json_encode([
        'code' => 200,
        'success' => true,
        'message' => 'notification sent',
    ]);
    exit();
}
