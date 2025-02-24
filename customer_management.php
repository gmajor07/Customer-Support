<?php
require 'header.php';
require 'db_connection.php';

// Fetch Customers
$sql = "SELECT * FROM customers ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="drop.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == "success") {
            echo "<div class='alert alert-success' id='notification'>Customer status updated successfully!</div>";
        } elseif ($_GET['status'] == "error") {
            echo "<div class='alert alert-danger' id='notification'>Error updating status. Try again!</div>";
        }
    }
    ?>

    <script>
        setTimeout(function() {
            let notification = document.getElementById('notification');
            if (notification) {
                notification.style.display = 'none';
            }
        }, 5000);
    </script>

    <center><h1>Customers Management</h1></center>
    <br>

   <!-- Bulk Update Section -->
<form action="bulk_update_status.php" method="post">
    <div class="row mb-3">
        <div class="col-12 col-md-4 mb-2">
            <select class="form-select w-100" name="bulk_user_status">
                <option value="">Customers Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="New Customer">New Customer</option>
            </select>
        </div>

        <div class="col-12 col-md-4 mb-2">
            <select class="form-select w-100" name="bulk_payment_status">
                <option value=""> Payment Status</option>
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
                <option value="Partial Paid">Partial Paid</option>
            </select>
        </div>

        <div class="col-12 col-md-4 text-md-end text-center">
            <button type="submit" class="btn btn-warning w-100 w-md-auto" id="bulkUpdateBtn" disabled>
                Update Selected Customers
            </button>
        </div>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="table-responsive">
        <table class="table table-striped table-sm table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th class="d-none d-sm-table-cell">Phone Number</th>
                    <th>Customers Status</th>
                    <th class="d-none d-md-table-cell">Payment Status</th>
                    <th><input type="checkbox" id="selectAll" class="custom-checkbox"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sn = 1;
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td class="d-none d-sm-table-cell"><?php echo $row['phonenumber']; ?></td>
                        <td><?php echo $row['user_status']; ?></td>
                        <td class="d-none d-md-table-cell"><?php echo $row['payment_status']; ?></td>
                        <td>
                            <input type="checkbox" class="selectCustomer custom-checkbox" name="customer_ids[]" value="<?php echo $row['id']; ?>">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</form>

</div>

<script>
    // Enable/Disable Bulk Update Button
    $(document).on('change', '.selectCustomer, #selectAll', function () {
        let checkedCount = $(".selectCustomer:checked").length;
        $("#bulkUpdateBtn").prop("disabled", checkedCount === 0);
    });

    // Select All Checkbox
    $("#selectAll").on("change", function () {
        $(".selectCustomer").prop("checked", this.checked);
        $("#bulkUpdateBtn").prop("disabled", !this.checked);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require 'footer.php';
?>

