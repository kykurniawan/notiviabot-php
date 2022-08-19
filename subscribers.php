<?php
header('Content-Type: application/json; charset=utf-8');

require_once('./constants.php');
require_once('./app/Subscriber.php');

if (!isset($_GET['apikey'])) {
    http_response_code(403);
    echo json_encode([
        'code' => 403,
        'success' => false,
        'message' => 'please provide apikey in query params'
    ]);
    exit();
}

if ($_GET['apikey'] !== APIKEY) {
    http_response_code(403);
    echo json_encode([
        'code' => 403,
        'success' => false,
        'message' => 'invalid apikey'
    ]);
    exit();
}

$subscriber = new Subscriber;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        http_response_code(200);
        echo json_encode([
            'code' => 200,
            'success' => true,
            'message' => 'ok',
            'data' => [
                'subscriber' => $subscriber->find($_GET['id'])
            ],
        ]);
        exit();
    }

    http_response_code(200);
    echo json_encode([
        'code' => 200,
        'success' => true,
        'message' => 'ok',
        'data' => [
            'subscribers' => $subscriber->all()
        ],
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newSubscriber = $subscriber->create();

    http_response_code(201);
    echo json_encode([
        'code' => 201,
        'success' => true,
        'message' => 'subscriber created',
        'data' => [
            'id' => $newSubscriber
        ],
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'code' => 400,
            'success' => false,
            'message' => 'bad request',
            'errors' => [
                'id' => 'please provide id field in query params'
            ]
        ]);
        exit();
    }

    $findSubscriber = $subscriber->find($_GET['id']);

    if ($findSubscriber == null) {
        http_response_code(200);
        echo json_encode([
            'code' => 200,
            'success' => true,
            'message' => 'ok',
            'data' => [
                'deleted' => false,
                'message' => 'subscriber not found',
            ],
        ]);
        exit();
    }

    $deleted = $subscriber->delete($_GET['id']);

    if (!$deleted) {
        http_response_code(200);
        echo json_encode([
            'code' => 200,
            'success' => true,
            'message' => 'ok',
            'data' => [
                'deleted' => $deleted,
                'message' => 'failed to delete subscriber'
            ],
        ]);
        exit();
    }

    http_response_code(200);
    echo json_encode([
        'code' => 200,
        'success' => true,
        'message' => 'ok',
        'data' => [
            'deleted' => $deleted,
            'message' => 'subscriber deleted'
        ],
    ]);
    exit();
}

http_response_code(405);
echo json_encode([
    'code' => 405,
    'success' => false,
    'message' => 'method not allowed',
]);
exit();
