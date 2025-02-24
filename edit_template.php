<?php
// Database connection
require 'db_connection.php';


// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $message = $_POST['message'];

    // Update the template in the database
    $sql = "UPDATE templates SET message = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $message, $id);

    if ($stmt->execute()) {
        // Redirect back to the templates page
        header("Location: templates.php?status=success&action=edit");
        exit();
    } else {
        echo "Error updating template: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
