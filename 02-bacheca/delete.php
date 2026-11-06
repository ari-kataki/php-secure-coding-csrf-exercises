<?php
session_start();

header('Content-Type: text/plain');

require_once "DbConnector.php";

if (!isset($_SESSION['users_id'])) {
    http_response_code(401);
    echo "Utente non autenticato. Effettua il login.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Metodo non valido.";
    exit();
}

if (!isset($_POST['delete_id'], $_POST['delete_token'])) {
    http_response_code(400);
    echo "Dati mancanti.";
    exit();
}

if (!isset($_SESSION['delete_token'])) {
    http_response_code(403);
    echo "Token di cancellazione non configurato.";
    exit();
}

if ($_POST['delete_token'] !== $_SESSION['delete_token']) {
    http_response_code(403);
    echo "Token di cancellazione non valido!";
    exit();
}

$id = (int)$_POST['delete_id'];
if ($id <= 0) {
    http_response_code(400);
    echo "ID messaggio non valido.";
    exit();
}

$connector = (new DbConnector("localhost", "root", "root", "bacheca"));

$stmt = $connector->prepare("SELECT delection_date FROM messages WHERE id = ?");
$stmt->execute([$id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$msg) {
    http_response_code(404);
    echo "Messaggio non trovato.";
    exit();
}

if ($msg['delection_date'] !== '2038-01-19 03:14:07') {
    http_response_code(409);
    echo "Messaggio già cancellato.";
    exit();
}

$now = date('Y-m-d H:i:s');

$stmt = $connector->prepare("UPDATE messages SET delection_date = ? WHERE id = ?");
if ($stmt->execute([$now, $id])) {
    echo "Messaggio marcato come cancellato con successo.";
} else {
    http_response_code(500);
    echo "Errore durante la cancellazione.";
}
