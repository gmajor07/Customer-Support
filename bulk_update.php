<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_customers'])) {
    $selected_customers = $_POST['selected_customers'];
    $user_status = $_POST['user_status'];
    $payment_status = $_POST['payment_status'];

    foreach ($selected_customers as $customer_id) {
        $updateFields = [];

        if (!empty($user_status)) {
            $updateFields[] = "user_status = '$user_status'";
        }
        if (!empty($payment_status)) {
            $updateFields[] = "payment_status = '$payment_status'";
        }

        if (!empty($updateFields)) {
            $query = "UPDATE customers SET " . implode(", ", $updateFields) . " WHERE id = '$customer_id'";
            $conn->query($query);
        }
    }

    header("Location: customers.php?update=success");
} else {
    header("Location: customers.php?update=error");
}
?>
