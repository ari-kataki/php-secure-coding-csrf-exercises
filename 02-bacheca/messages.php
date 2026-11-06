<?php
session_start();
require "DbConnector.php";

$connector = new DbConnector("localhost", "root", "root", "bacheca");

if (!isset($_SESSION['users_id'])) {
    echo("Utente non autenticato. Effettua il login.");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['delete_token'])) {
    $_SESSION['delete_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['testo'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die ("Token CSRF non valido!");
    }

    $testo = $_POST['testo'];
    $data_invio = date('Y-m-d H:i:s');

    $stmt = $connector->prepare("INSERT INTO messages (text_message, date_message, id_users, delection_date) VALUES (?, ?, ?, '2038-01-19 03:14:07')");
    $stmt->execute([$testo, $data_invio, $_SESSION['users_id']]);
    header("Location: messages.php");
    exit();
}

$stmt_role = $connector->prepare("SELECT role FROM users WHERE users_id = ?");
$stmt_role->execute([$_SESSION['users_id']]);
$role = $stmt_role->fetchColumn();


$sql = 'SELECT messages.id, messages.text_message, messages.date_message, messages.id_users, users.username
        FROM messages
        JOIN users ON messages.id_users = users.users_id
        WHERE messages.delection_date = "2038-01-19 03:14:07"';
if ($role === 'autor'){
    $sql .= ' AND messages.status = "published"';
}
$sql .= ' ORDER BY messages.date_message DESC LIMIT 0,10';

$stmt = $connector->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Bacheca</title>
</head>
<body>

<form method="post" action="">
    <textarea name="testo" required></textarea><br>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit">Enter</button>
</form>

<?php if (!empty($rows)): ?>
    <?php foreach ($rows as $row): ?>
        <div class="message" style=" border:1px solid #ccc; padding:10px; margin:10px 0;">
            <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
            <p><?php echo nl2br(htmlspecialchars($row['text_message'])); ?></p>
            <small>Inviato il <?php echo $row['date_message']; ?></small>
            <?php if ($role === 'admin' || ($row['id_users'] == $_SESSION['users_id'])): ?>
                <form method="post" action="delete.php" style="margin-top:5px;">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="delete_token" value="<?php echo $_SESSION['delete_token']; ?>">
                    <button type="submit">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nessun messaggio trovato.</p>
<?php endif; ?>
</body>
</html>



