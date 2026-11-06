<?php
declare(strict_types=1); //usa il controllo severo dei tipi ovvero se una funzione accetta int non puoi passarle ad esempio una string "5" altrimenti viene lanciato un errore
require_once "dbconnector.php";

$db = new DbConnector("localhost", "root", "", "dbsam"); // crea un oggetto dbconnector

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];

    $deleteSql = "UPDATE messages SET deleted = 1 WHERE id = :id";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // ricarica la pagina dopo l’operazione
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$sql = "SELECT * 
        FROM messages
        JOIN users ON messages.user_id = users.id
        WHERE messages.deleted = 0
        ORDER BY messages.sending_date DESC
        LIMIT 10 OFFSET 0";

$stmt = $db->prepare($sql); //crea un PDO statement basato sulla query
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// prende tutte le righe restituite dalla query in un colpo solo e ogni riga sarà un array associativo


?>


<html>
<head>
    <title>Messages</title>
    <link rel="stylesheet" href="css_messages.css">
</head>
<body>
<?php if ($messages): ?>
    <div class="messages-container">
        <?php foreach ($messages as $msg): ?>
            <div class="message-box">
                <span class="username"><?php echo htmlspecialchars($msg['user']); ?>:</span>
                <span class="text"><?php echo htmlspecialchars($msg['text']); ?></span>
                <span class="date"><?php echo date('d/m/Y H:i', strtotime($msg['sending_date'])); ?></span>

                <!-- delete button -->
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?php echo (int)$msg['id']; ?>">
                        <button type="submit" name="delete" >🗑 Elimina</button>
                    </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No messages found.</p>
<?php endif; ?>
</body>
</html>
