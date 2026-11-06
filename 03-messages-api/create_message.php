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

$input = json_decode(file_get_contents('php://input'), true);
$content = trim($input['content'] ?? '');
if (empty($content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Il messaggio non può essere vuoto']);
    exit;
}

try {
    $db = new DbConnector('localhost', 'root', 'root', 'bacheca');

    $stmt = $db->prepare("
        INSERT INTO messages (text_message, date_message, id_users, status)
        VALUES (:msg, NOW(), :uid, 'publish')
    ");
    $stmt->bindParam(':msg', $content);
    $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Messaggio pubblicato con successo']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore DB: ' . $e->getMessage()]);
}
?>
