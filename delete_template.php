<?php
// Database connection
require 'db_connection.php';


// Handle the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete the template from the database
    $sql = "DELETE FROM templates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to the templates page
        header("Location: templates.php?status=success&action=delete");
        exit();
    } else {
        echo "Error deleting template: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
