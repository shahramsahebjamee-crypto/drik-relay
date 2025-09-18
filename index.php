<?php
// index.php - Webhook Relay for Telegram Bot

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["status" => "error", "message" => "No input received"]);
    exit;
}

$token   = $input['token'] ?? '';
$chat_id = $input['chat_id'] ?? '';
$text    = $input['text'] ?? '';

if (!$token || !$chat_id || !$text) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$url = "https://api.telegram.org/bot{$token}/sendMessage";

$data = [
    'chat_id' => $chat_id,
    'text'    => $text
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(["status" => "error", "message" => curl_error($ch)]);
} else {
    echo json_encode(["status" => "success", "telegram_response" => json_decode($response, true)]);
}

curl_close($ch);
