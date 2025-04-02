<?php
session_start();
require 'db_connection.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// CSV Headers
fputcsv($output, ['SN', 'Username', 'Phone', 'Status', 'Payment Status']);

// Data Query
$sql = ($_SESSION['role'] == 'admin') 
    ? "SELECT * FROM customers ORDER BY id DESC" 
    : "SELECT * FROM customers WHERE registered_by_staff = ? ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($_SESSION['role'] != 'admin') $stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$sn = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $sn++,
        $row['username'],
        $row['phonenumber'],
        $row['user_status'],
        $row['payment_status']
    ]);
}
fclose($output);
exit();
?>