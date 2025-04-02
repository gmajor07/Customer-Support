<?php
session_start();
require 'db_connection.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}

// Check if at least one customer is selected
if (empty($_POST['customer_ids'])) {
    $_SESSION['error_messages'] = ["No customers selected"];
    header("Location: customer_management.php?status=error");
    exit();
}

// Prepare update parameters
$statusUpdate = !empty($_POST['bulk_user_status']) ? $_POST['bulk_user_status'] : null;
$paymentUpdate = !empty($_POST['bulk_payment_status']) ? $_POST['bulk_payment_status'] : null;

// Build the SQL query dynamically based on what needs updating
$updates = [];
$params = [];
$types = "";

if ($statusUpdate !== null) {
    $updates[] = "user_status = ?";
    $params[] = $statusUpdate;
    $types .= "s";
}

if ($paymentUpdate !== null) {
    $updates[] = "payment_status = ?";
    $params[] = $paymentUpdate;
    $types .= "s";
}

// If neither was selected (should be prevented by JS but good to check)
if (empty($updates)) {
    $_SESSION['error_messages'] = ["No changes selected"];
    header("Location: customer_management.php?status=error");
    exit();
}

// Add customer IDs to parameters
$placeholders = implode(',', array_fill(0, count($_POST['customer_ids']), '?'));
$types .= str_repeat('i', count($_POST['customer_ids']));
$params = array_merge($params, $_POST['customer_ids']);

// Build and execute the query
$sql = "UPDATE customers SET " . implode(', ', $updates) . " WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Updated " . $stmt->affected_rows . " customer(s)";
    header("Location: customer_management.php?status=success");
} else {
    $_SESSION['error_messages'] = ["Error updating customers: " . $conn->error];
    header("Location: customer_management.php?status=error");
}
exit();
?>