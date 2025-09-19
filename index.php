<?php
// Error reporting فعال برای توسعه
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/relay_error.log');

// دریافت ورودی JSON از تلگرام
$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "No input received"]);
    exit;
}

$update = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

// بررسی پارامترهای موردنظر
$token   = $update['token']   ?? null;
$chat_id = $update['chat_id'] ?? null;
$text    = $update['text']    ?? null;

if (!$token || !$chat_id || !$text) {
    http_response_code(400);
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// ساخت آدرس API تلگرام
$send_url = "https://api.telegram.org/bot{$token}/sendMessage";

// داده‌هایی که باید ارسال شوند
$post_data = [
    'chat_id' => $chat_id,
    'text'    => $text
];

// ارسال درخواست به تلگرام
$ch = curl_init($send_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    error_log("Curl error: " . curl_error($ch));
    http_response_code(500);
    echo json_encode(["error" => "Curl failed"]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// پاسخ نهایی تلگرام برگردانده می‌شود
http_response_code(200);
header('Content-Type: application/json');
echo $response;
