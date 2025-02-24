<?php
require 'db_connection.php';

// API credentials
$api_key = '46b9f649a761dce8';
$secret_key = 'OTM3NmY4MjAyMTlmMWI2MTNiY2YxZjU0NTE1M2ZjYTc2ZTA4OTg0Y2ZhNDJlYzI1OTE2YjgzZGJmZjA0ZmQzOA==';

// Fetch messages that need to be sent
$query = "SELECT * FROM scheduled_messages WHERE next_run <= NOW() AND status = 'pending'";
$results = $conn->query($query);

while ($row = $results->fetch_assoc()) {
    $message = $row['message'];
    $recipientCategory = $row['recipient_category'];
    $scheduleType = $row['schedule_type'];
    $id = $row['id'];

    // Get recipients based on category
    $userQuery = "SELECT phonenumber FROM customers"; // Default: All Customers

    if ($recipientCategory == "Paid") {
        $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Paid'";
    } elseif ($recipientCategory == "Unpaid") {
        $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Unpaid'";
    } elseif ($recipientCategory == "Active") {
        $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'Active'";
    }
    elseif ($recipientCategory == "Partial Paid") {
        $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Partial Paid'";
    } elseif ($recipientCategory == "New Customers") {
        $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'New Customers'";
    }
    elseif ($recipientCategory == "Inactive") {
        $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'Inactive'";
    }
    $users = $conn->query($userQuery);

    // Send messages
    while ($user = $users->fetch_assoc()) {
        $phone = "255" . $user['phonenumber'];

        $postData = array(
            'source_addr' => 'RUGAZE ENTP',
            'encoding' => 0,
            'message' => $message,
            'recipients' => array(array('recipient_id' => rand(1, 100), 'dest_addr' => $phone))
        );

        $ch = curl_init('https://apisms.beem.africa/v1/send');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        curl_close($ch);
    }

    // Update next_run time based on schedule
    $newNextRun = "NULL"; // Default: don't repeat

    if ($scheduleType == 'daily') {
        $newNextRun = "DATE_ADD(NOW(), INTERVAL 1 DAY)";
    } elseif ($scheduleType == 'weekly') {
        $newNextRun = "DATE_ADD(NOW(), INTERVAL 1 WEEK)";
    } elseif ($scheduleType == 'monthly') {
        $newNextRun = "DATE_ADD(NOW(), INTERVAL 1 MONTH)";
    }

    $updateQuery = "UPDATE scheduled_messages SET next_run = $newNextRun, status = 'sent' WHERE id = $id";
    $conn->query($updateQuery);
}

$conn->close();
?>
