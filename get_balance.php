<?php
require 'db_connection.php'; // If needed




$api_key = '46b9f649a761dce8';
$secret_key = 'OTM3NmY4MjAyMTlmMWI2MTNiY2YxZjU0NTE1M2ZjYTc2ZTA4OTg0Y2ZhNDJlYzI1OTE2YjgzZGJmZjA0ZmQzOA==';


$Url = 'https://apisms.beem.africa/public/v1/vendors/balance';

$ch = curl_init($Url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt_array($ch, array(
    CURLOPT_HTTPGET => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
        'Content-Type: application/json'
    ),
));

$response = curl_exec($ch);

if ($response === FALSE) {
    echo json_encode(['error' => 'Failed to fetch balance: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);

// Extract balance correctly
if (isset($data['data']['credit_balance'])) {
    echo json_encode(['balance' => $data['data']['credit_balance']]);
} else {
    echo json_encode(['error' => 'Invalid response format', 'raw' => $data]);
}
?>