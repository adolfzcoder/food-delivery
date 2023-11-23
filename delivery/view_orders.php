<?php
include 'db_conn.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];

// Retrieve user details from the database
$getUserDetailsQuery = "SELECT * FROM users WHERE id = $userId";
$getUserDetailsResult = mysqli_query($db, $getUserDetailsQuery);

if (!$getUserDetailsResult || mysqli_num_rows($getUserDetailsResult) == 0) {
    echo "Error fetching user details";
    exit;
}

$userDetails = mysqli_fetch_assoc($getUserDetailsResult);
$isDriver = $userDetails['is_driver'];

// Check if the user is a driver
if (!$isDriver) {
    header("Location: login.php"); // Redirect non-drivers to login page
    exit;
}

// Retrieve orders from the database
$getOrdersQuery = "SELECT * FROM orders";
$getOrdersResult = mysqli_query($db, $getOrdersQuery);

if (!$getOrdersResult) {
    echo "Error fetching orders: " . mysqli_error($db);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            table {
                border: 0;
            }

            table, th, td {
                display: block;
            }

            th, td {
                border: none;
                padding: 8px;
                text-align: left;
            }
        }
    </style>
</head>
<body>

<h2>View Orders</h2>

<table>
    <tr>
        <th>ID</th>
        <th>User Order</th>
        <th>Total</th>
        <th>Quantity</th>
        <th>Name</th>
        <th>Location</th>
        <th>User ID</th>
    </tr>

    <?php
    // Display orders
    while ($orderRow = mysqli_fetch_assoc($getOrdersResult)) {
        echo "<tr>";
        echo "<td>{$orderRow['id']}</td>";
        echo "<td>{$orderRow['user_order']}</td>";
        echo "<td>{$orderRow['total']}</td>";
        echo "<td>{$orderRow['quantity']}</td>";
        echo "<td>{$orderRow['name']}</td>";
        echo "<td>{$orderRow['location']}</td>";
        echo "<td>{$orderRow['user_id']}</td>";
        echo "<td>{$orderRow['order_timestamp']}</td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
