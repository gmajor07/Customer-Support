<?php
// Database connection
require 'db_connection.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {

     
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $phone = $_POST['phone']; // Capture phone number

    $sql = "UPDATE users SET username='$username', email='$email', phone='$phone' WHERE id='$id'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $id);

    if ($stmt->execute()) {
        header("Location: admin_panel.php?update=success");

        exit();
    } else {
        echo "Error updating admin: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>