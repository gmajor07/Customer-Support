<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $entered_otp) {
        // OTP is correct, allow password reset
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container text-center">
    <h3 class="mb-4">Enter OTP:</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <input type="number" name="otp" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify OTP</button>
    </form>
</div>
</body>
</html>
