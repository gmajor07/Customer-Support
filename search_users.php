<?php
require 'db_connection.php';

if (isset($_POST['query'])) {
    $searchText = $_POST['query'];
    $sql = "SELECT * FROM customers WHERE username LIKE '%$searchText%' OR phonenumber LIKE '%$searchText%'";
} else {
    $sql = "SELECT * FROM customers";
}

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['username']}</td>
        <td>{$row['phonenumber']}</td>
        <td>{$row['user_status']}</td>
        <td>{$row['payment_status']}</td>
        <td>
            <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editCustomerModal{$row['id']}'>Edit</button>
            <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteCustomerModal{$row['id']}'>Delete</button>
            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#updateStatusModal{$row['id']}'>Update Status</button>
        </td>
    </tr>";
}
?>
