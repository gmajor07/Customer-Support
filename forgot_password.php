<?php

function send_message($phone, $message, $api_key, $secret_key) {
    $phone = preg_replace('/\D/', '', $phone); // Remove non-numeric characters

    if (strlen($phone) == 9) {
        $phone = "255$phone"; // Convert 789123456 to 255789123456
    } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == "0") {
        $phone = "255" . substr($phone, 1); // Convert 0789123456 to 255789123456
    }

    $postData = array(
        'source_addr' => 'NARET',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => array(
            array('recipient_id' => rand(1, 100), 'dest_addr' => $phone)
        )
    );

    $Url = 'https://apisms.beem.africa/v1/send';

    $ch = curl_init($Url);
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

    curl_close($ch);
    return $response;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    // API credentials
    $api_key = '46b9f649a761dce8';
    $secret_key = 'OTM3NmY4MjAyMTlmMWI2MTNiY2YxZjU0NTE1M2ZjYTc2ZTA4OTg0Y2ZhNDJlYzI1OTE2YjgzZGJmZjA0ZmQzOA==';

    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);
    $message = "Your OTP code is: $otp";

    // Send the OTP message
    $response = send_message($phone, $message, $api_key, $secret_key);

    // Handle response
    if ($response) {
        // Optionally, store the OTP in a session or database for verification
        session_start();
        $_SESSION['otp'] = $otp;

        $_SESSION['phone'] = $phone;

        // Redirect to OTP verification page
        header("Location: verify_otp.php");
        exit();
    } else {
        echo "Failed to send OTP. Please try again.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container text-center">
    <h3 class="mb-4">Forgot Password</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Enter Your Phone Number:</label>
            <input type="number" name="phone" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
