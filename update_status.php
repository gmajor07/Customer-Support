<?php
require 'db_connection.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; // Get customer ID
    $user_status = $_POST['user_status']; // Get updated user status
    $payment_status = $_POST['payment_status']; // Get updated payment status

    // SQL Update Query
    $sql = "UPDATE customers SET user_status = ?, payment_status = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $user_status, $payment_status, $id);
        
        if ($stmt->execute()) {
            // Redirect back with a success message
            header("Location: customers.php?status=success");
            exit();
        } else {
            // Redirect back with an error message
            header("Location: customers.php?update=error");
            exit();
        }
    } else {
        // SQL statement preparation failed
        die("Error preparing the statement: " . $conn->error);
    }
}
?>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
