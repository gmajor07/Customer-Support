<?php

function send_message($phone, $message, $api_key, $secret_key) {
    $id = rand(1, 100);
    $phone = "255$phone";

    $postData = array(
        'source_addr' => 'RUGAZE ENTP',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => array(
            array('recipient_id' => $id, 'dest_addr' => $phone)
        )
    );

    $Url = 'https://apisms.beem.africa/v1/send';

    $ch = curl_init($Url);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("$api_key:$secret_key"),
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($postData)
    ));

    $response = curl_exec($ch);

    if ($response === FALSE) {
        die('Curl error: ' . curl_error($ch));
    }

    var_dump($response);
    curl_close($ch);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $api_key = '9238149afaa56300';
    $secret_key = 'ZmY0NTEwZTMzMzAwYTFhNmZmOGI3NTllZGE3NDJhY2M0YWVhNDE5MTVkZDQzZTZkNWEyYjU0YTFiMmE2YTM4NQ==';

    send_message($phone, $message, $api_key, $secret_key);
}

?>
