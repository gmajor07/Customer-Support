<?php
ob_start(); // Start output buffering

require 'header.php';
require 'db_connection.php';
// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Authentication Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$loggedInUserId = $_SESSION['user_id'];
$loggedInUserRole = $_SESSION['role'];

// Handle Add Customer Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    // Verify CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Sanitize and validate user input
    $username = trim($_POST['username']);
    $phonenumber = trim($_POST['phonenumber']);
    $user_status = $_POST['user_status'];
    $payment_status = $_POST['payment_status'];

    $errors = [];

    // Validation
    if (!preg_match('/^[a-zA-Z\s]+$/', $username)) {
        $errors[] = "Invalid username. Only letters and spaces are allowed.";
    }

    if (!preg_match('/^\d{10}$/', $phonenumber)) { 
        $errors[] = "Invalid phone number. Ensure it has 10 digits and no special characters.";
    }

    // Check if phone number exists only if no other errors
    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT id FROM customers WHERE phonenumber = ?");
        $checkStmt->bind_param("s", $phonenumber);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errors[] = "This phone number is already registered. Please use a different number.";
        }
        $checkStmt->close();
    }

    // If no errors, proceed with insertion
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO customers (username, phonenumber, user_status, payment_status, registered_by_staff) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $phonenumber, $user_status, $payment_status, $loggedInUserId);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "New customer added successfully!";
            // When redirecting
header("Location: customers.php?status=success");
ob_end_flush(); // Send output buffer and turn off buffering
exit();
        } else {
            $errors[] = "Error: Unable to save the record. Please try again later.";
        }
        $stmt->close();
    }

    // Store errors in session if any
    if (!empty($errors)) {
        $_SESSION['error_messages'] = $errors;
    }
}


// Pagination Setup
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Fetch Customers with Pagination
if ($loggedInUserRole == 'admin') {
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM customers ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $perPage, $offset);
} else {
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM customers WHERE registered_by_staff = ? ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $loggedInUserId, $perPage, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$hasCustomers = ($result->num_rows > 0);

// Get total count for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS()");
$totalRows = $totalResult->fetch_row()[0];
$totalPages = ceil($totalRows / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="drop.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">

    <?php if (!$hasCustomers): ?>
        <div class="alert alert-warning">No customers found.</div>
    <?php else: ?>

        <!-- Status Notifications -->
        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-<?php echo $_GET['status'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $_GET['status'] == 'success' ? 'Operation completed successfully!' : 'Error processing your request!'; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Customer Management</h1>
            <div>
                <a href="export_customers.php" class="btn btn-outline-success me-2">
                    <i class="bi bi-download"></i> Export
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="bi bi-plus-circle"></i> Add Customer
                </button>
            </div>
        </div>

        <!-- Search and Filter Section -->
       <!-- Replace your current search card with this -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <input type="text" id="searchInput" class="form-control" placeholder="Search customers...">
            </div>
            <div class="col-md-4">
                <select id="statusFilter" class="form-select">
                    <option value="">All Customer Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="New Customer">New Customer</option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="paymentFilter" class="form-select">
                    <option value="">All Payment Statuses</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partial Paid">Partial Paid</option>
                </select>
            </div>
        </div>
    </div>
</div>

        <!-- Bulk Update Form -->
        <form action="bulk_update_status.php" method="post" id="bulkForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Replace your current bulk action card with this -->
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bulk Actions</h5>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="selectAllSwitch">
            <label class="form-check-label" for="selectAllSwitch">Select All</label>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="form-select" name="bulk_user_status">
                    <option value="">-- No Status Change --</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="New Customer">New Customer</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="bulk_payment_status">
                    <option value="">-- No Payment Change --</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Partial Paid">Partial Paid</option>
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-warning" id="bulkUpdateBtn" disabled>
                    <i class="bi bi-arrow-repeat"></i> Update Selected
                </button>
            </div>
        </div>
    </div>
</div>

            <!-- Customers Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="customersTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50px">SN</th>
                            <th>Username</th>
                            <th class="d-none d-sm-table-cell">Phone</th>
                            <th>Status</th>
                            <th class="d-none d-md-table-cell">Payment</th>
                            <th width="50px">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sn = 1; // Initialize Serial Number

                    ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?php echo $row['id']; ?>">
                        <td><?php echo $sn++; ?></td>                            <td>
                                <strong><?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?></strong>
                                <div class="small text-muted d-block d-sm-none">
                                    <?php echo htmlspecialchars($row['phonenumber'], ENT_QUOTES); ?>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($row['phonenumber'], ENT_QUOTES); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['user_status'] == 'Active' ? 'success' : 
                                         ($row['user_status'] == 'Inactive' ? 'danger' : 'info'); 
                                ?>">
                                    <?php echo htmlspecialchars($row['user_status'], ENT_QUOTES); ?>
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-<?php 
                                    echo $row['payment_status'] == 'Paid' ? 'success' : 
                                         ($row['payment_status'] == 'Unpaid' ? 'danger' : 'warning'); 
                                ?>">
                                    <?php echo htmlspecialchars($row['payment_status'], ENT_QUOTES); ?>
                                </span>
                            </td>
                          
                            <td>
                                <input type="checkbox" class="form-check-input select-customer" 
                                       name="customer_ids[]" value="<?php echo $row['id']; ?>">
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>
<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="add_customer" value="1">
                
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed">
                        <div class="form-text">Only letters and spaces allowed</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phonenumber" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phonenumber" name="phonenumber" required
                               pattern="\d{10}" title="10 digit phone number required">
                        <div class="form-text">10 digits only (e.g., 1234567890)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_status" class="form-label">Status</label>
                            <select class="form-select" id="user_status" name="user_status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="New Customer" selected>New Customer</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select class="form-select" id="payment_status" name="payment_status" required>
                                <option value="Paid">Paid</option>
                                <option value="Unpaid" selected>Unpaid</option>
                                <option value="Partial Paid">Partial Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal (will be populated via JavaScript) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_customer.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="editCustomerId">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="editUsername" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="phonenumber" id="editPhone" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">9
            <form action="update_status.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="statusCustomerId">
                <div class="modal-header">
                    <h5 class="modal-title">Update Customer Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Customer Status</label>
                        <select class="form-select" name="user_status" id="currentStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="New Customer">New Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status" id="currentPayment">
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                            <option value="Partial Paid">Partial Paid</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="delete_customer.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="id" id="deleteCustomerId">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                    <p class="fw-bold">Customer: <span id="deleteCustomerName"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>


<script>


$(document).ready(function() {

        // Combined search and filter function
        function filterCustomers() {
        const searchText = $('#searchInput').val().toLowerCase();
        const statusFilter = $('#statusFilter').val().toLowerCase();
        const paymentFilter = $('#paymentFilter').val().toLowerCase();
        
        $('#customersTable tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const statusText = $(this).find('td:eq(3)').text().toLowerCase(); // Status column
            const paymentText = $(this).find('td:eq(4)').text().toLowerCase(); // Payment column
            
            const matchesSearch = searchText === '' || rowText.includes(searchText);
            const matchesStatus = statusFilter === '' || statusText.includes(statusFilter);
            const matchesPayment = paymentFilter === '' || paymentText.includes(paymentFilter);
            
            $(this).toggle(matchesSearch && matchesStatus && matchesPayment);
        });
    }

    // Trigger filtering on any change
    $('#searchInput, #statusFilter, #paymentFilter').on('input change', filterCustomers);
    // Enable/disable bulk update button
    $('.select-customer').change(function() {
        $('#bulkUpdateBtn').prop('disabled', $('.select-customer:checked').length === 0);
    });

    // Select all functionality
    $('#selectAll, #selectAllSwitch').change(function() {
        $('.select-customer').prop('checked', $(this).is(':checked'));
        $('#bulkUpdateBtn').prop('disabled', !$(this).is(':checked'));
    });

    // Search functionality
    $('#searchInput').keyup(function() {
        const searchText = $(this).val().toLowerCase();
        $('#customersTable tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchText));
        });
    });

    // Filter by status
    $('#statusFilter').change(function() {
        const filterValue = $(this).val().toLowerCase();
        $('#customersTable tbody tr').each(function() {
            if (!filterValue) {
                $(this).show();
                return;
            }
            const statusText = $(this).find('td:eq(3)').text().toLowerCase();
            $(this).toggle(statusText.includes(filterValue));
        });
    });

    // Edit modal population
    $('#editModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const row = button.closest('tr');
        
        $('#editCustomerId').val(id);
        $('#editUsername').val(row.find('td:eq(1)').text().trim());
        $('#editPhone').val(row.find('td:eq(2)').text().trim());
    });

    // Status modal population
    $('#statusModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        
        $('#statusCustomerId').val(id);
        $('#currentStatus').val(button.data('current-status'));
        $('#currentPayment').val(button.data('current-payment'));
    });

    // Delete modal population
    $('#deleteModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const id = button.data('id');
        const row = button.closest('tr');
        
        $('#deleteCustomerId').val(id);
        $('#deleteCustomerName').text(row.find('td:eq(1)').text().trim());
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

<style>
    .badge {
        font-size: 0.85em;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .form-check-input {
        cursor: pointer;
    }
    
    .dropdown-toggle::after {
        margin-left: 0.5em;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        
        .table thead {
            display: none;
        }
        
        .table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table td::before {
            content: attr(data-label);
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .table td:last-child {
            border-bottom: 0;
        }
    }
</style>

<?php
require 'footer.php';
?>