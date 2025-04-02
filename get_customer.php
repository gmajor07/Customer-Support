<?php
session_start();
require 'db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) die(json_encode(['error' => 'No ID provided']));

$stmt = $conn->prepare("SELECT username, phonenumber FROM customers WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());
exit();
?>