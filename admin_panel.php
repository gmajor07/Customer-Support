<?php
require 'header.php';
require 'db_connection.php';



// Fetch Customers
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
    

<div class="container mt-5">
<?php
if (isset($_GET['update'])) {
    if ($_GET['update'] == "success") {
        echo "<div class='alert alert-success' id='notification'>Admin details updated successfully!</div>";
    }
    elseif ($_GET['update'] == "error") {
        echo "<div class='alert alert-danger' id='notification'>Error updating Admin details. Try again!</div>";
    }
}

if (isset($_GET['delete'])) {
   
if ($_GET['delete'] == "success") {
    echo "<div class='alert alert-success' id='notification'>Admin deleted successfully!</div>";
}
    elseif ($_GET['delete'] == "error") {
        echo "<div class='alert alert-danger' id='notification'>Error deleteing Admin. Try again!</div>";
    }
}


if (isset($_GET['email'])) {
   
    if ($_GET['email'] == "success") {
        echo "<div class='alert alert-success' id='notification'>Admin details updated successfully!</div>";
    }
        elseif ($_GET['delete'] == "error") {
            echo "<div class='alert alert-danger' id='notification'>Error updating details. Try again!</div>";
        }
    }

?>

<script>
    // Hide notification after 3 seconds (3000ms)
    setTimeout(function() {
        let notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }, 5000);
</script>


<center><h1>Admin Panel</h1></center>


<!-- Responsive Table Wrapper -->
<div class="table-responsive">
    <table class="table table-striped table-sm table-bordered" id="dataTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
             $sn = 1; // Initialize Serial Number

            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                <td><?php echo $sn++; ?></td> <!-- Increment SN dynamically -->
                <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
    <!-- Edit Button -->
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editCustomerModal<?php echo $row['id']; ?>">
        <i class="bi bi-pencil-square"></i> Edit
    </button>

    <!-- Delete Button -->
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal<?php echo $row['id']; ?>">
        <i class="bi bi-trash"></i> Delete
    </button>


</td>

                </tr>
                 <!-- Edit Customer Modal -->
  <div class="modal fade" id="editCustomerModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editCustomerLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="edit_admin.php" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Customer Modal -->
                <div class="modal fade" id="deleteCustomerModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deleteCustomerLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="delete_admin.php" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete admin <strong><?php echo $row['username']; ?></strong>?</p>
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Add Bootstrap CSS if not already included -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require 'footer.php';
?>

 


                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
