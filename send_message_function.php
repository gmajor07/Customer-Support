<?php

function send_message($phone, $message, $api_key, $secret_key) {
    // Generate a random ID for the recipient
    $id = rand(1, 1000);

    // Validate and format phone number
    $phone = ltrim($phone, '0'); // Remove leading zero if present
    $phone = "255$phone";

    // Prepare data for Beem API
    $postData = array(
        'source_addr' => 'RUGAZE ENTP', // Your approved Sender ID
        'encoding' => 0,
        'schedule_time' => '', // Leave empty for immediate delivery
        'message' => $message,
        'recipients' => array(
            array('recipient_id' => $id, 'dest_addr' => $phone)
        )
    );

    $url = 'https://apisms.beem.africa/v1/send';

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => 1
    ));

    // Execute the request
    $response = curl_exec($ch);

    // Handle cURL errors
    if ($response === FALSE) {
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return array(
            'status' => 'error',
            'message' => 'cURL request failed: ' . curl_error($ch)
        );
    }

    // Parse HTTP status code and response
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code != 200) {
        error_log("HTTP Status Code: $http_code. Response: $response");
        return array(
            'status' => 'error',
            'message' => "HTTP error: $http_code",
            'response' => json_decode($response, true)
        );
    }

    // Parse and return the success response
    return array(
        'status' => 'success',
        'response' => json_decode($response, true)
    );
}
