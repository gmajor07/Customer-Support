<?php
require 'db_connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $messageId = $_POST['message_id'];
    $conn->query("UPDATE scheduled_messages SET status = 'cancelled' WHERE id = $messageId");
}
header("Location: periodically_messages.php");
?>
