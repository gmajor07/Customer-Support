<?php
session_start(); // Ensure session is started
require 'db_connection.php';

$searchText = isset($_POST['query']) ? $_POST['query'] : '';

// Ensure the role is set
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$staffId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; // Assuming staff has a user ID

// Start building SQL query
if (!empty($searchText)) {
    // If staff, filter by assigned customers
    if ($userRole === 'staff') {
        $sql = "SELECT * FROM customers WHERE (username LIKE ? OR phonenumber LIKE ?) AND registered_by = ?";
        $stmt = $conn->prepare($sql);
        $searchPattern = "%$searchText%";
        $stmt->bind_param("ssi", $searchPattern, $searchPattern, $staffId);
    } else {
        // Admins see all customers
        $sql = "SELECT * FROM customers WHERE username LIKE ? OR phonenumber LIKE ?";
        $stmt = $conn->prepare($sql);
        $searchPattern = "%$searchText%";
        $stmt->bind_param("ss", $searchPattern, $searchPattern);
    }
} else {
    if ($userRole === 'staff') {
        // Staff sees only their assigned customers
        $sql = "SELECT * FROM customers WHERE registered_by = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $staffId);
    } else {
        // Admins see all customers
        $sql = "SELECT * FROM customers";
        $stmt = $conn->prepare($sql);
    }
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

$sn = 1; // Initialize Serial Number

while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $sn++; ?></td> <!-- Serial Number -->
        <td><?php echo strtoupper(htmlspecialchars($row['username'])); ?></td>
        <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($row['phonenumber']); ?></td>
        <td><?php echo htmlspecialchars($row['user_status']); ?></td>
        <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($row['payment_status']); ?></td>
        <td>
            <!-- Edit Button -->
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editCustomerModal<?php echo $row['id']; ?>">
                <i class="bi bi-pencil-square"></i> Edit
            </button>

            <!-- Delete Button (Only for Admins) -->
            <?php if ($userRole === 'admin') : ?>
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
<?php } ?>
