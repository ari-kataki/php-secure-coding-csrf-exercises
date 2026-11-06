<?php
declare(strict_types=1);
session_start();
require_once "dbconnector.php";

// Controllo sessione
if (!isset($_SESSION['username']) || $_SESSION['username'] === '') {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['csrf_logout'])) {
    $_SESSION['csrf_logout'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['csrf_insert'])) {
    $_SESSION['csrf_insert'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['csrf_delete'])) {
    $_SESSION['csrf_delete'] = bin2hex(random_bytes(32));
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$db = new DbConnector("localhost", "root", "", "dbsam");

// LOGOUT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit();
}

// SELECT messaggi NON cancellati dell'utente
$sql = "SELECT messages.id, messages.text, messages.sending_date, messages.user_id, users.user AS username
        FROM messages
        JOIN users ON messages.user_id = users.id
        WHERE messages.deleted = 0 
        ORDER BY messages.sending_date DESC
        LIMIT 10";

$stmt = $db->prepare($sql);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css_messages.css">
</head>
<body>
<h1>Benvenuto, <?php echo htmlspecialchars($user); ?>!</h1>
<p>Questa è la tua dashboard privata.</p>
<br>

<!-- Form Logout -->
<form method="post" action="">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_logout']); ?>">
    <button type="submit" name="logout">Logout</button>
</form>


<!-- Form Inserimento Messaggio -->
<h2>A cosa stai pensando?</h2>
    <?php

    // Recupero il ruolo dell’utente loggato dal DB
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT ruolo FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $ruolo = $stmt->fetchColumn();

    if ($ruolo == 'AUTHOR' || $ruolo == 'ADMIN'): ?>
    <form method="post" action="insert.php">
        <textarea name="message_text" rows="4" cols="50" placeholder="Scrivi qui il tuo messaggio..." required></textarea>
        <!-- required = obbliga l'utente a scrivere qualcosa -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_insert']); ?>">
        <br>
        <?php else: ?>
            <textarea name="message_text" rows="4" cols="50" placeholder="L'EDITOR non può scrivere messaggi" disabled></textarea>
        <?php endif; ?>
        <?php
    // Se l’utente è EDITOR il bottone ''invia'' viene disabilitato
    if ($ruolo !== 'EDITOR'): ?>
    <button type="submit" name="send">Invia</button>
    <?php endif; ?>

</form>

<h2>Ultimi messaggi:</h2>
<div class="messages-container">
    <?php if ($messages): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-box">
                <span class="username"><?php echo htmlspecialchars($msg['username']); ?>:</span>
                <span class="text"><?php echo htmlspecialchars($msg['text']); ?></span>
                <span class="date"><?php echo date('d/m/Y H:i', strtotime($msg['sending_date'])); ?></span>

                <!-- Delete button (solo per i messaggi dell'utente)
                <?php if ($msg['user_id'] == $user_id): ?>
                    <form method="post" action="delete.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_logout']); ?>">
                        <input type="hidden" name="delete_id" value="<?php echo (int)$msg['id']; ?>">
                        <button type="submit" name="delete">Elimina</button>
                    </form>
                <?php endif; ?>
                 Delete button -->
                <?php
                // Recupero il ruolo dell’utente loggato dal DB
                $user_id = $_SESSION['user_id'];
                $stmt = $db->prepare("SELECT ruolo FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $ruolo = $stmt->fetchColumn();

                // Se l’utente è ADMIN mostro il bottone elimina
                if ($ruolo === 'ADMIN' || $msg['user_id'] == $user_id): ?>
                    <form method="post" action="delete.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_logout']); ?>">
                        <input type="hidden" name="delete_id" value="<?php echo (int)$msg['id']; ?>">
                        <button type="submit" name="delete">Elimina</button>
                    </form>
                <?php endif; ?>


            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nessun messaggio trovato.</p>
    <?php endif; ?>
</div>
</body>
</html>