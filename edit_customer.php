<?php
// Database connection
require 'db_connection.php';


if ($_SERVER["REQUEST_METHOD"] === "POST") {

     
    $id = $_POST['id'];
    $username = $_POST['username'];
    $phonenumber = $_POST['phonenumber'];

    $sql = "UPDATE customers SET username = ?, phonenumber = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $phonenumber, $id);

    if ($stmt->execute()) {
        header("Location: customers.php?update=success");

        exit();
    } else {
        echo "Error updating customer: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>