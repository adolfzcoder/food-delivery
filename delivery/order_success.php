<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details and order summary from the database
$userId = $_SESSION['id'];
$getOrderSummaryQuery = "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC LIMIT 1"; // Assuming you want the latest order
$getOrderSummaryResult = mysqli_query($db, $getOrderSummaryQuery);

if (!$getOrderSummaryResult || mysqli_num_rows($getOrderSummaryResult) == 0) {
    echo "Error fetching order details";
    exit;
}

$orderDetails = mysqli_fetch_assoc($getOrderSummaryResult);
$orderReference = $orderDetails['reference_id'];
$totalPrice = $orderDetails['total'];
$totalQuantity = $orderDetails['quantity'];
$location = $orderDetails['location'];
$orderName = $orderDetails['name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <style>
        body {
  font-family: sans-serif;
  margin: 20px;
}

h1 {
  font-size: 24px;
  margin-bottom: 20px;
}

p {
  font-size: 16px;
  line-height: 1.5;
  margin-bottom: 10px;
}

a {
  text-decoration: none;
  color: #337ab7;
}

a:hover {
  color: #225599;
}

    </style>
</head>
<body>

<h1>Order Successful!</h1>

<?php 
// Display the order details
echo '<p>Your order reference: ' . $orderReference . '</p>';
echo '<p>Name: ' . $orderName . '</p>';
echo '<p>Location: ' . $location . '</p>';
echo '<p>Total Quantity: ' . $totalQuantity . '</p>';
echo '<p>Total Price: $' . $totalPrice . '</p>';

// Display links based on user type
echo '<p><a href="view.php">Back to Shops</a></p>';
echo '<p><a href="message_driver.php">Message Driver</a></p>';
?>

</body>
</html>
