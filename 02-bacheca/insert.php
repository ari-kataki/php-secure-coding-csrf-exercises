<?php
session_start();
if($_SESSION['users_id'] == NULL){
    header('Location: login.php');
    exit;
}
require 'DbConnector.php';
$dbConnector = new DbConnector('localhost', 'root', 'root', 'bacheca');
$statement = $dbConnector->prepare('INSERT INTO messages (text_message,date_message,id_users,delection_date)
VALUES (
:userContent,
CURRENT_TIMESTAMP,
:userId,
\'2038-01-19 03:14:07\'
)');
$statement->bindParam('userContent', $_POST['textarea']);
$statement->bindParam('userId', $_SESSION['users_id']);
if($statement->execute()) {
    header("location: messages.php");
    exit;
}
?>
