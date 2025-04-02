<?php
require 'header.php';
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; 

    // Validate username (letters and spaces only)
    if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
        $error = "Invalid username. Only letters and spaces are allowed.";
    } 
    // Validate email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = "Invalid email format.";
    } 
    // Validate phone number (digits only, at least 10 characters)
    elseif (!preg_match('/^\d{10,}$/', $phone)) {
        $error = "Invalid phone number. Only digits are allowed, minimum 10 characters.";
    } 
    // Validate password match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // Validate password length
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email or phone already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $checkStmt->bind_param("ss", $email, $phone);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "This email or phone number is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user with role and phone number
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $phone, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! <a href='login.php'>Login</a>";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $checkStmt->close();
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="cardstyle.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Create System User</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" id="phone" placeholder="Enter phone number" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm your password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Select Role</label>
            <select name="role" class="form-control" id="role" required>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-custom w-100">Register</button>
    </form>
</div>
</body>
</html>

<?php
require 'footer.php';
?>
