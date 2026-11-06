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

// SOFT DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $deleteSql = "UPDATE messages SET deleted = 1 WHERE id = :id AND user_id = :user_id";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
    $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $deleteStmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Redirect alla dashboard
header("Location: dashboard.php");
exit();
?>





