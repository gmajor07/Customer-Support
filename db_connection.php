<?php
// Database configuration
$server = "web195";
$username = "naretcot_root";
$password = "1!0jJsqP9ScP0*";
$database = "naretcot_customer";

// Create a connection to the database
$conn = new mysqli($server, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
