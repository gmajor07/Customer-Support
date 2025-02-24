<?php
// Database configuration
$server = "localhost";
$username = "root";
$password = "";
$database = "customer";

// Create a connection to the database
$conn = new mysqli($server, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
