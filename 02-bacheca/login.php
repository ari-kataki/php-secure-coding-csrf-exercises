<?php
require "DbConnector.php";
session_start();

if (!array_key_exists("ciaopippo", $_COOKIE)) {
    setcookie("ciaopippo",'15');
}
$connector = new DbConnector('localhost', 'root', 'root', 'bacheca');
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statement = $connector->prepare('SELECT *
                                            FROM users
                                            WHERE username = :username');
    $statement->bindParam(':username', $_POST['username']);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($_POST['password'], $result['password'])) {
        $_SESSION["users_id"] = $result['users_id'];
        $_SESSION["username"] = $result['username'];
        header("Location: messages.php");
        exit();
    } else {
        echo "Invalid username or password";
    }
}
?>

<html>
    <body>
        <form method="post" action="">
            Username: <input type="text" name="username"><br>
            Password: <input type="password" name="password"><br>
        <button type="submit">Enter</button>
        </form>
    </body>
</html>
