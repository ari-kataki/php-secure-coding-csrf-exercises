<?php
require_once "DbConnector.php";
require_once "functions.php";
require_once "config.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $db = new DbConnector('localhost', 'root', 'root', 'bacheca');

    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Inserisci username e password.']);
        exit;
    }

    $stmt = $db->prepare("SELECT users_id, username, password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Utente non trovato.']);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Password errata.']);
        exit;
    }

    $token = generateToken($user['users_id'], $secretKey);

    echo json_encode([
        'success' => true,
        'username' => $user['username'],
        'token' => $token
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Errore server: ' . $e->getMessage()]);
}
?>
