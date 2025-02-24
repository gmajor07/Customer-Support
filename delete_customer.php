<?php
// Database connection
require 'db_connection.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];

    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: customers.php?delete=success");
        exit();
    } else {
        echo "Error deleting customer: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
