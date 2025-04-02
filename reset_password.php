<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    die("Unauthorized access.");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation
    $errors = [];
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    
    if (!preg_match('/[\W]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $phone = $_SESSION['phone'];

        // Update password in database
        $sql = "UPDATE users SET password = ? WHERE phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $phone);
        
        if ($stmt->execute()) {
            session_destroy();
            header("Location: login.php?reset=success");
            exit();
        } else {
            $error = "Error resetting password. Please try again.";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .password-rules {
            text-align: left;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            color: #dc3545;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card mt-5">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Reset Password</h3>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="resetForm">
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <div class="password-rules">
                                Password must contain:
                                <ul>
                                    <li id="length" class="text-danger">At least 8 characters</li>
                                    <li id="uppercase" class="text-danger">One uppercase letter</li>
                                    <li id="lowercase" class="text-danger">One lowercase letter</li>
                                    <li id="number" class="text-danger">One number</li>
                                    <li id="special" class="text-danger">One special character</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            <div class="invalid-feedback" id="confirmError"></div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const form = document.getElementById('resetForm');
    
    // Real-time password validation
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Validate length
        document.getElementById('length').className = password.length >= 8 ? 'text-success' : 'text-danger';
        
        // Validate uppercase
        document.getElementById('uppercase').className = /[A-Z]/.test(password) ? 'text-success' : 'text-danger';
        
        // Validate lowercase
        document.getElementById('lowercase').className = /[a-z]/.test(password) ? 'text-success' : 'text-danger';
        
        // Validate number
        document.getElementById('number').className = /[0-9]/.test(password) ? 'text-success' : 'text-danger';
        
        // Validate special character
        document.getElementById('special').className = /[\W_]/.test(password) ? 'text-success' : 'text-danger';
    });
    
    // Confirm password validation
    confirmInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.classList.add('is-invalid');
            document.getElementById('confirmError').textContent = 'Passwords do not match';
        } else {
            this.classList.remove('is-invalid');
            document.getElementById('confirmError').textContent = '';
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const password = passwordInput.value;
        
        // Check all requirements
        if (password.length < 8) isValid = false;
        if (!/[A-Z]/.test(password)) isValid = false;
        if (!/[a-z]/.test(password)) isValid = false;
        if (!/[0-9]/.test(password)) isValid = false;
        if (!/[\W_]/.test(password)) isValid = false;
        if (password !== confirmInput.value) isValid = false;
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix all password requirements before submitting.');
        }
    });
});
</script>
</body>
</html>