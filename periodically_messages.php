<?php
require 'db_connection.php';
require 'header.php';


// Fetch all message templates
$templateQuery = "SELECT * FROM templates";
$templates = $conn->query($templateQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $templateId = $_POST['template'];
    $schedule = $_POST['schedule'];
    $recipientCategory = $_POST['recipient_category'];

    // Fetch message from template
    $stmt = $conn->prepare("SELECT message FROM templates WHERE id = ?");
    $stmt->bind_param("i", $templateId);
    $stmt->execute();
    $stmt->bind_result($message);
    $stmt->fetch();
    $stmt->close();

    if (empty($message)) {
        $error = "No message selected.";
    } else {
        // Determine when to send the next message
        $nextRun = date('Y-m-d H:i:s'); // Default is now

        if ($schedule == 'daily') {
            $nextRun = date('Y-m-d H:i:s', strtotime('+1 day'));
        } elseif ($schedule == 'weekly') {
            $nextRun = date('Y-m-d H:i:s', strtotime('+1 week'));
        } elseif ($schedule == 'monthly') {
            $nextRun = date('Y-m-d H:i:s', strtotime('+1 month'));
        }

        // Insert into scheduled_messages table
        $stmt = $conn->prepare("INSERT INTO scheduled_messages (template_id, message, schedule_type, recipient_category, next_run, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("issss", $templateId, $message, $schedule, $recipientCategory, $nextRun);
        $stmt->execute();
        $stmt->close();

        $success = "Message scheduled successfully!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Message Sending</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="cardstyle.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Select Message Template</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="template" class="form-label">Choose a Template:</label>
            <select name="template" id="template" class="form-control" required>
                <?php while ($row = $templates->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['message']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="schedule" class="form-label">Send Messages:</label>
            <select name="schedule" id="schedule" class="form-control" required>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="recipient_category" class="form-label">Send to:</label>
            <select name="recipient_category" id="recipient_category" class="form-select" required>
                <option value="all">All Customers</option>
                <option value="Paid">Paid Customers</option>
                <option value="Partial Paid">Partial Paid Customers</option>
                <option value="Unpaid">Unpaid Customers</option>
                <option value="New Customer">New Customer</option>
                <option value="Active">Active Customers</option>
                <option value="Inactive">Inactive Customers</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Send Messages</button>
    </form>
</div>
</body>
</html>
<?php
require 'footer.php';
?>
<script>
    setInterval(function() {
    fetch('scheduled_send.php');
}, 60000); // Runs every 60 seconds

</script>