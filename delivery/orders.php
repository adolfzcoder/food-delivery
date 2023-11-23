<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];

// Fetch user orders
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_reference'])) {
    // If the search form is submitted
    $searchReference = mysqli_real_escape_string($db, $_POST['search_reference']);
    $userOrdersQuery = "SELECT * FROM orders WHERE user_id = $userId AND reference_id LIKE '%$searchReference%' ORDER BY order_timestamp DESC";
} else {
    // Default query without search
    $userOrdersQuery = "SELECT * FROM orders WHERE user_id = $userId ORDER BY order_timestamp DESC";
}

$userOrdersResult = mysqli_query($db, $userOrdersQuery);

if (!$userOrdersResult) {
    die("Error fetching user orders: " . mysqli_error($db));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Your Orders</title>
    <style>
        body {
            font-family: sans-serif;
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
        }

        input[type="submit"] {
            padding: 8px 12px;
            cursor: pointer;
        }
        
h2 {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}



    </style>
</head>
<body>
<?php include"navbar.php"; ?>
<h2>Your Orders</h2>

<!-- Search form -->
<form method="post">
    <label for="search_reference">Search by Reference Number:</label>
    <input type="text" name="search_reference" id="search_reference" placeholder="Enter reference number">
    <input type="submit" value="Search">
</form>

<?php
if (mysqli_num_rows($userOrdersResult) > 0) {
    while ($order = mysqli_fetch_assoc($userOrdersResult)) {
        $referenceID = $order['reference_id'];
        $orderTimestamp = $order['order_timestamp'];
        $orderSummary = $order['user_order'];
        $totalPrice = $order['total'];
        $quantity = $order['quantity'];
        $name = $order['name'];
        $location = $order['location'];
        $deliveryFee = $order['deliveryFee'];

        echo "<h3>Order Reference: $referenceID</h3>";
        echo "<p>Placed on: $orderTimestamp</p>";

        // Display order details in a table
        echo "<table>";
        echo "<tr><th>Food Name</th><th>Quantity</th><th>Total Price</th></tr>";

        $orderItems = explode(',', $orderSummary);
        $consolidatedItems = [];

        foreach ($orderItems as $foodId) {
            $foodId = (int)$foodId;

            $getFoodDetailsQuery = "SELECT name, price FROM foods WHERE id = $foodId";
            $getFoodDetailsResult = mysqli_query($db, $getFoodDetailsQuery);

            if ($getFoodDetailsResult && mysqli_num_rows($getFoodDetailsResult) > 0) {
                $foodDetails = mysqli_fetch_assoc($getFoodDetailsResult);
                $foodName = $foodDetails['name'];
                $foodPrice = $foodDetails['price'];

                if (!isset($consolidatedItems[$foodName])) {
                    $consolidatedItems[$foodName] = [
                        'quantity' => 1,
                        'totalPrice' => $foodPrice
                    ];
                } else {
                    $consolidatedItems[$foodName]['quantity']++;
                    $consolidatedItems[$foodName]['totalPrice'] += $foodPrice;
                }
            }
        }

        foreach ($consolidatedItems as $foodName => $itemDetails) {
            $quantity = $itemDetails['quantity'];
            $totalPrice = $itemDetails['totalPrice'];

            echo "<tr><td>$foodName</td><td>$quantity</td><td>N$ $totalPrice</td></tr>";
        }

        echo "<tr><td colspan='2'>Delivery Fee</td><td>N$ $deliveryFee</td></tr>";
        echo "<tr><td colspan='2'>Total Price</td><td>N$ $totalPrice</td></tr>";
        echo "<tr><td colspan='2'>Deliver to</td><td>$name - $location</td></tr>";

        echo "</table>";
    }
} else {
    echo "<p>No orders available.</p>";
}
?>

</body>
</html>

<?php 
########   ####      #####      #       #######     ########     #######  ####   ####     ######     #######
#      #   #   #    #      #    #       #                 #      #       #     # #    #   #          #   ##
########   #    #   #      #    #       ###             #       #       #     # #     #  ######     #  ##
#      #   #   #     #    #     #       #              #         #       #     # #    #   #          #   ##
#      #   ####       ####      ######  #            ########    #######  #####  ####     #######    #    ##
?>
