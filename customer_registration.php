<?php
require 'header.php';
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate user input
    $username = trim($_POST['username']);
    $phonenumber = trim($_POST['phonenumber']);

    if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
        $error = "Invalid username. Only letters and spaces are allowed.";
    } elseif (!preg_match('/^\d{10}$/', $phonenumber)) { // Check if phone number has 9 digits (Tanzania format)
        $error = "Invalid phone number. Ensure it has 10 digits and no special characters. ";
    } else {
        // Check if the phone number already exists
        $checkStmt = $conn->prepare("SELECT id FROM customers WHERE phonenumber = ?");
        $checkStmt->bind_param("s", $phonenumber);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "This phone number is already registered. Please use a different number.";
        } else {
            // Default values for new fields
            $user_status = 'New Customers'; // Inactive
            $payment_status = 'Unpaid'; // Unpaid

            // Insert the new user if phone number doesn't exist
            $stmt = $conn->prepare("INSERT INTO customers (username, phonenumber, user_status, payment_status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $username, $phonenumber, $user_status, $payment_status);

            if ($stmt->execute()) {
                $success = "New customer recorded successfully";
            } else {
                $error = "Error: Unable to save the record. Please try again later.";
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
    <title>Register Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <link href="cardstyle.css" rel="stylesheet">

</head>
<body>
   

<div class="container">
            <h1 class="text-center mb-4">Register New Customer</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Enter username eg:doe" required>
            </div>
            <div class="mb-3">
                <label for="phonenumber" class="form-label">Phonenumber</label>
                <input type="phonenumber" name="phonenumber" class="form-control" id="phonenumber" placeholder="Enter phonenumber eg: 713200200" required>
            </div>
           
            <button type="submit" class="btn btn-custom w-100">Record Customer</button>
        </form>
      
    </div>
</body>
</html>

<?php
require 'footer.php';
?> 