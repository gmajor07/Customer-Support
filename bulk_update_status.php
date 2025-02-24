<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['customer_ids'])) {
        $customer_ids = $_POST['customer_ids'];
        $user_status = isset($_POST['bulk_user_status']) ? $_POST['bulk_user_status'] : "";
        $payment_status = isset($_POST['bulk_payment_status']) ? $_POST['bulk_payment_status'] : "";

        $updateFields = [];

        // Add status updates only if they are selected
        if (!empty($user_status)) {
            $updateFields[] = "user_status = '$user_status'";
        }
        if (!empty($payment_status)) {
            $updateFields[] = "payment_status = '$payment_status'";
        }

        if (!empty($updateFields)) {
            $query = "UPDATE customers SET " . implode(", ", $updateFields) . " WHERE id IN (" . implode(",", $customer_ids) . ")";
            if ($conn->query($query)) {
                header("Location: customer_management.php?status=success");
                exit();
            } else {
                header("Location: customer_management.php?status=error");
                exit();
            }
        } else {
            header("Location: customer_management.php?status=error");
            exit();
        }
    } else {
        header("Location: customer_management.php?status=error");
        exit();
    }
}
?>
