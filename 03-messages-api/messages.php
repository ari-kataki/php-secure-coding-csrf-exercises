<?php
require_once "DbConnector.php";
require_once "functions.php";
require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

$headers = getallheaders();
$auth = $headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
if (!$auth) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Token mancante']);
    exit;
}

list(, $token) = explode(' ', $auth, 2);
$userId = validateToken($token, $secretKey);
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Token non valido']);
    exit;
}

try {
    $db = new DbConnector('localhost', 'root', 'root', 'bacheca');

    $stmt = $db->prepare("
        SELECT 
            m.id,
            m.text_message AS content,
            m.date_message AS creation_date,
            u.username
        FROM messages m
        INNER JOIN users u ON m.id_users = u.users_id
        WHERE m.status = 'publish'
        ORDER BY m.date_message DESC
    ");
    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'items' => $messages]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore DB: ' . $e->getMessage()]);
}
?>
