<?php
session_start();
require 'header.php';
require 'db_connection.php';

// Ensure session variables exist
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access!");
}

$loggedInUserId = $_SESSION['user_id'];
$loggedInUserRole = $_SESSION['role'];

// Fetch Customers
if ($loggedInUserRole == 'admin') {
    $sql = "SELECT * FROM customers ORDER BY id DESC"; // Admin sees all customers
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM customers WHERE registered_by_staff = ? ORDER BY id DESC"; // Staff sees only their customers
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loggedInUserId);
}

$stmt->execute();
$result = $stmt->get_result();
$hasCustomers = ($result->num_rows > 0); // Check if there are customers
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <?php
    if (!$hasCustomers) {
        echo "<p class='alert alert-warning'>No customers found.</p>";
    }
    ?>


    <?php if ($hasCustomers) : ?>
        <center><h1>Customers List</h1></center>

        <input type="text" id="search" class="form-control" placeholder="Search customers...">
        <div id="searchResults"></div>

        <div class="table-responsive">
            <table class="table table-striped table-sm table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Username</th>
                        <th class="d-none d-sm-table-cell">Phone Number</th>
                        <th>Status</th>
                        <th class="d-none d-md-table-cell">Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sn = 1; // Initialize Serial Number
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo strtoupper(htmlspecialchars($row['username'])); ?></td>
                            <td class="d-none d-sm-table-cell"><?php echo $row['phonenumber']; ?></td>
                            <td><?php echo $row['user_status']; ?></td>
                            <td class="d-none d-md-table-cell"><?php echo $row['payment_status']; ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editCustomerModal<?php echo $row['id']; ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                <!-- Delete Button (Only for Admins) -->
                                <?php if ($_SESSION['role'] === 'admin') : ?>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal<?php echo $row['id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                <?php endif; ?>

                                <!-- Update Status Button -->
                                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?php echo $row['id']; ?>">
                                    <i class="bi bi-arrow-repeat"></i> Update Status
                                </button>
                            </td>
                        </tr>
                              <!-- Edit Customer Modal -->
  <div class="modal fade" id="editCustomerModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editCustomerLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="edit_customer.php" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phonenumber" value="<?php echo $row['phonenumber']; ?>" required>
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
                        <form action="delete_customer.php" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete customer <strong><?php echo $row['username']; ?></strong>?</p>
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

                <!-- Update Status Modal -->
                <div class="modal fade" id="updateStatusModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updateStatusLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="update_status.php" method="post">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Status</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Customer Status</label>
                                        <select class="form-control" name="user_status">
                                            <option value="Active" <?php if ($row['user_status'] == 'Active') echo 'selected'; ?>>Active</option>
                                            <option value="Inactive" <?php if ($row['user_status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                                            <option value="New Customer" <?php if ($row['user_status'] == 'New Customer') echo 'selected'; ?>>New Customer</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Payment Status</label>
                                        <select class="form-control" name="payment_status">
                                            <option value="Paid" <?php if ($row['payment_status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                            <option value="Unpaid" <?php if ($row['payment_status'] == 'Unpaid') echo 'selected'; ?>>Unpaid</option>
                                            <option value="Partial Paid" <?php if ($row['payment_status'] == 'Partial Paid') echo 'selected'; ?>>Partial Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-warning">Update</button>
                                </div>
                            </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
