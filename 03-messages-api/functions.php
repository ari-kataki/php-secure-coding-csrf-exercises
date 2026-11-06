<?php

function generateToken($userId, $secretKey) {
    $payload = base64_encode(json_encode([
        'id' => $userId,
        'expiringTime' => time() + 3600,
    ]));
    $signature = hash_hmac('sha256', $payload, $secretKey);
    return "$payload.$signature";
}

function validateToken($token, $secretKey) {
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return false;
    }

    [$payload, $signature] = $parts;
    $expectedSignature = hash_hmac('sha256', $payload, $secretKey);

    if (!hash_equals($expectedSignature, $signature)) {
        return false;
    }

    $data = json_decode(base64_decode($payload), true);
    if (!isset($data['id']) || $data['expiringTime'] < time()) {
        return false;
    }

    return $data['id'];
}
?>
