<?php
declare(strict_types=1);
session_start();
require_once "dbconnector.php";

// Controllo sessione
if (!isset($_SESSION['username']) || $_SESSION['username'] === '') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$db = new DbConnector("localhost", "root", "", "dbsam");

// Inserimento messaggio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $text = trim($_POST['message_text']);
    if ($text !== '') {
        $insertSql = "INSERT INTO messages (user_id, text, sending_date, deleted) VALUES (:user_id, :text, NOW(), 0)";
        $stmt = $db->prepare($insertSql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':text', $text, PDO::PARAM_STR);
        $stmt->execute();
    }
}

// Redirect alla dashboard
header("Location: dashboard.php");
exit();
?>
