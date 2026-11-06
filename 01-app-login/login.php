<?php
session_start();
require_once "dbconnector.php";

// Se l'utente è già loggato, reindirizza alla dashboard
if (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
    header('Location: dashboard.php');
    exit();
}

// Genera token CSRF se non esiste
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestione cookie
if (!isset($_COOKIE['username'])) {
    setcookie("username", "mario", time() + 3600, "/");
}

// Inizializza variabile errore
$error = '';

// Se il form viene inviato
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Controllo CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token CSRF non valido.";
    } else {
        $username = $_POST["user"] ?? '';
        $password = $_POST["password"] ?? '';

        // Connessione al database
        $connector = new dbconnector("localhost", "root", "", "dbsam");

        // Prepared statement
        $statement = $connector->prepare("SELECT * FROM users WHERE user = :username");
        $statement->bindParam(':username', $username);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Avvio sessione

            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];

            // Rigenera il token dopo login
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Username o password errati.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
<h2>Login</h2>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>

<form action="login.php" method="POST">
    <div>
        <label for="user">Username:</label>
        <input type="text" id="user" name="user" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <br>
    <div>
        <button type="submit">Accedi</button>
    </div>
</form>
</body>
</html>


