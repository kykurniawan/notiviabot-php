<?php

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

$subscriber = new Subscriber;

$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

if (strpos($text, "/start") === 0) {
    $replyText = "Hi, please send me your subscription id with format: */subscribe id* to start receiving notification.";
    $replyText .= "\n";
    $replyText .= "Example:";
    $replyText .= "\n";
    $replyText .= "*/subscribe bfr231hi*";
    $replyText .= "\n";
    $replyText .= "\n";
    $replyText .= "Then if you want to stop receiving notification just replace subscribe word to unsubscribe";
    $replyText .= "\n";
    $replyText .= "Example:";
    $replyText .= "\n";
    $replyText .= "*/unsubscribe bfr231hi*";
    $replyText = urlencode($replyText);
    file_get_contents(TELEGRAMPATH . "/sendMessage?parse_mode=markdown&chat_id=" . $chatId . "&text=$replyText");
}

if (strpos($text, "/subscribe") === 0) {
    $id = str_replace("/subscribe ", "", $text);
    $subscriber = new Subscriber;

    $findSubscriber = $subscriber->find($id);

    if ($findSubscriber == null) {
        $replyText = urlencode("Invalid ID");
        file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
    } else {
        if ($findSubscriber->chat_id != null && $findSubscriber->chat_id != $chatId) {
            $replyText = urlencode("ID already used by other telegram account");
            file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
        } elseif ($findSubscriber->chat_id != null && $findSubscriber->chat_id == $chatId) {
            $replyText = urlencode("Already subscribed");
            file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
        } else {
            $findByChatId = $subscriber->findByChatId($chatId);
            if ($findByChatId) {
                $replyText = urlencode("Already subscribed with other ID");
                file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
            } else {
                $success = $subscriber->update($findSubscriber->id, $update["message"]["from"]["first_name"], $chatId);
                $replyText = urlencode("Subscription started");
                file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
            }
        }
    }
}

if (strpos($text, "/unsubscribe") === 0) {
    $id = str_replace("/unsubscribe ", "", $text);

    $findSubscriber = $subscriber->find($id);

    if ($findSubscriber == null) {
        $replyText = urlencode("Invalid ID");
        file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
    } else {
        if ($findSubscriber->chat_id == null) {
            $replyText = urlencode("Already unsubscribed");
            file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
        } elseif ($findSubscriber->chat_id != $chatId) {
            $replyText = urlencode("ID already used by other telegram account");
            file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
        } else {
            $success = $subscriber->update($findSubscriber->id, $update["message"]["chat"]["username"], null);
            $replyText = urlencode("Subscription stoped");
            file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
        }
    }
}

if (strpos($text, "/me") === 0) {
    $findSubscriber = $subscriber->findByChatId($chatId);
    if ($findSubscriber == null) {
        $replyText = urlencode("Sorry, we don\t know about you. Please send your subscription id to start receiving notification.");
        file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
    } else {
        $replyText = "Username: " . $findSubscriber->name;
        $replyText .= "\n";
        $replyText .= "ID: " . $findSubscriber->id;
        $replyText .= "\n";
        $replyText .= "\n";
        $replyText .= "To stop receiving notification. Send command bellow \n/unsubscribe " . $findSubscriber->id;
        $replyText = urlencode($replyText);
        file_get_contents(TELEGRAMPATH . "/sendMessage?chat_id=" . $chatId . "&text=" . $replyText);
    }
}

header('HTTP/1.0 200 OK', TRUE, 200);
exit();
