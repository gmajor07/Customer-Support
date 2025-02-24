<?php
require 'header.php';
require 'db_connection.php';


// Function to send messages
function send_message($phone, $message, $api_key, $secret_key) {
    $id = rand(1, 100);
    $phone = "255$phone"; // Ensure the format is correct

    $postData = array(
        'source_addr' => 'NARET',
        'encoding' => 0,
        'schedule_time' => '',
        'message' => $message,
        'recipients' => array(
            array('recipient_id' => $id, 'dest_addr' => $phone)
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
        return ['error' => 'Curl error: ' . curl_error($ch)];
    }

    curl_close($ch);
    return ['success' => 'Message sent successfully!'];
}

// API credentials
$api_key = '46b9f649a761dce8';
$secret_key = 'OTM3NmY4MjAyMTlmMWI2MTNiY2YxZjU0NTE1M2ZjYTc2ZTA4OTg0Y2ZhNDJlYzI1OTE2YjgzZGJmZjA0ZmQzOA==';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedTemplateId = $_POST['template_id'];
    $manualMessage = trim($_POST['manual_message']);
    $recipientCategory = $_POST['recipient_category'];

    $message = '';

    if (!empty($selectedTemplateId) && $selectedTemplateId !== 'none') {
        $stmt = $conn->prepare("SELECT message FROM templates WHERE id = ?");
        $stmt->bind_param("i", $selectedTemplateId);
        $stmt->execute();
        $stmt->bind_result($message);
        $stmt->fetch();
        $stmt->close();
    }

    if (!empty($manualMessage)) {
        $message = $manualMessage;
    }

    if (empty($message)) {
        echo json_encode(['error' => "No message to send. Please select a template or enter a message."]);
        exit;
    }

    $userQuery = "SELECT phonenumber FROM customers";
    switch ($recipientCategory) {
        case "Paid":
            $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Paid'";
            break;
        case "Partial Paid":
            $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Partial Paid'";
            break;
        case "Unpaid":
            $userQuery = "SELECT phonenumber FROM customers WHERE payment_status = 'Unpaid'";
            break;
        case "New Customers":
            $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'New Customers'";
            break;
        case "Active":
            $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'Active'";
            break;
        case "Inactive":
            $userQuery = "SELECT phonenumber FROM customers WHERE user_status = 'Inactive'";
            break;
    }

    $users = $conn->query($userQuery);
    if ($users->num_rows > 0) {
        while ($user = $users->fetch_assoc()) {
            $phone = $user['phonenumber'];
            send_message($phone, $message, $api_key, $secret_key);
        }
        echo json_encode(['success' => "Messages sent successfully!"]);
    } else {
        echo json_encode(['error' => "No users found for the selected category."]);
    }
    $conn->close();
}

$remaining_messages = file_get_contents('get_balance.php');
$remaining_messages = json_decode($remaining_messages, true)['balance'] ?? 'Unavailable';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="cardstyle.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <div class="alert alert-warning ">
    Remaining Messages: <span id="sms-balance"></span>
</div>



        <h1>Send Messages</h1>

        <div id="message-alert" class="alert d-none"></div>
        <?php if (!empty($error) && empty($success)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
        <form id="messageForm">
            <div class="mb-3">
                <label for="template" class="form-label">Choose a Template</label>
                <select id="template" name="template_id" class="form-select">
                    <option value="none">-- Select a Template --</option>
                    <?php 
                    $templateQuery = "SELECT * FROM templates";
                    $templates = $conn->query($templateQuery);
                    while ($template = $templates->fetch_assoc()): ?>
                        <option value="<?= $template['id']; ?>"><?= htmlspecialchars($template['title']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="manual_message" class="form-label">Or Enter a Custom Message</label>
                <textarea id="manual_message" name="manual_message" rows="4" class="form-control" placeholder="Write your custom message here..."></textarea>
            </div>

            <div class="mb-3">
                <label for="recipient_category" class="form-label">Send to</label>
                <select id="recipient_category" name="recipient_category" class="form-select">
                    <option value="all">All Customers</option>
                    <option value="Paid">Paid Customers</option>
                    <option value="Partial Paid">Partial Paid Customers</option>
                    <option value="Unpaid">Unpaid Customers</option>
                    <option value="New Customers">New Customers</option>
                    <option value="Active">Active Customers</option>
                    <option value="Inactive">Inactive Customers</option>
                </select>
            </div>

            <button type="submit" class="btn btn-custom">Send Messages</button>
        </form>

        <?php require 'get_balance.php';
?>
    </div>

    <script>

$(document).ready(function () {
    updateBalance(); // Fetch balance on page load

    $("#messageForm").submit(function (e) {
        e.preventDefault(); // Prevent normal form submission

        var formData = $(this).serialize();
        $("#message-alert").removeClass("d-none").addClass("alert-info").text("Sending messages...");

        $.ajax({
            url: "send_messages.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                $("#message-alert").removeClass("alert-info");

                if (response.error) {
                    $("#message-alert").addClass("alert-danger").text(response.error);
                } else if (response.success) {
                    $("#message-alert").addClass("alert-success").text(response.success);
                    $("#messageForm")[0].reset(); // Clear form fields
                    updateBalance(); // Update balance after sending messages
                }
            },
            error: function () {
                $("#message-alert").removeClass("alert-info").addClass("alert-success").text("Messages sent successfully!");
                updateBalance(); // Update balance after sending messages
            }
        });
    });
});


// Function to fetch and update SMS balance dynamically
function updateBalance() {
    fetch('get_balance.php')
        .then(response => response.json())
        .then(data => {
            if (data.balance !== undefined) {
                document.getElementById('sms-balance').innerText = ` ${data.balance}`;
            } else {
                document.getElementById('sms-balance').innerText = " Balance unavailable";
            }
        })
        .catch(error => {
            document.getElementById('sms-balance').innerText = "Error fetching balance";
            console.error("Error:", error);
        });
}


    </script>
</body>
</html>

<?php
require 'footer.php';
?>