<?php
require 'header.php';

require 'db_connection.php';

$query = "SELECT * FROM scheduled_messages WHERE status = 'pending'";
$messages = $conn->query($query);

?>

<?php if (isset($_GET['status'])) { ?>
    <div class="alert alert-<?php echo $_GET['status'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php
        if ($_GET['action'] === 'edit') {
            echo "Template updated successfully!";
        } elseif ($_GET['action'] === 'delete') {
            echo "Template deleted successfully!";
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodically Messages To Send </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Message Templates</h2>
        <div class="table-responsive">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="d-none d-sm-table-cell">Message</th>
                    <th>Schedule</th>
                    <th class="d-none d-sm-table-cell">Next Run</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($msg = $messages->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $msg['id']; ?></td>
                        <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($msg['message']); ?></td>
                        <td><?php echo ucfirst($msg['schedule_type']); ?></td>
                        <td class="d-none d-sm-table-cell"><?php echo $msg['next_run']; ?></td>
                        <td>
                            <form method="POST" action="cancel_message.php">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-danger">Cancel</button>
                            </form>
                        </td>
                    
                    </tr>

                   
                <?php } ?>
            </tbody>
        </table>
    </div>
    </div>

</body>
</html>
<?php
require 'footer.php';
?>
