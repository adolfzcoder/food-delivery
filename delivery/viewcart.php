<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];

$getCartQuery = "SELECT cart FROM users WHERE id = $userId";
$getCartResult = mysqli_query($db, $getCartQuery);

if (!$getCartResult) {
    die("Error fetching user cart: " . mysqli_error($db));
}

$userCartRow = mysqli_fetch_assoc($getCartResult);
$userCart = $userCartRow['cart'];

$cartArray = explode(',', $userCart);

echo '<table class="cart-table">';
echo '<tr><th>Food Name</th><th>Price</th><th>Quantity</th></tr>';

$totalQuantity = 0;
$totalPrice = 0;

$cartCounts = []; // Array to track item quantities

foreach ($cartArray as $cartItemId) {
    // Convert the cartItemId to an integer
    $foodId = (int)$cartItemId;

    // Check if the cartItemId corresponds to a valid food item
    $getFoodQuery = "SELECT * FROM foods WHERE id = $foodId";
    $getFoodResult = mysqli_query($db, $getFoodQuery);

    if (!$getFoodResult) {
        continue;
    }

    if (mysqli_num_rows($getFoodResult) === 0) {
        continue; // Skip invalid cart items
    }

    $foodRow = mysqli_fetch_assoc($getFoodResult);
    $foodName = $foodRow['name'];
    $foodPrice = $foodRow['price'];

    // Update the total quantity and price
    $totalPrice += $foodPrice;
    $totalQuantity++;

    // Check if the item already exists in the cartCounts array
    if (!isset($cartCounts[$foodId])) {
        $cartCounts[$foodId] = 1; // Add the item with an initial quantity of 1
    } else {
        $cartCounts[$foodId]++; // Increment the quantity for the existing item
    }
}

// Display the cart items with their actual quantities
foreach ($cartCounts as $foodId => $quantity) {
    $getFoodQuery = "SELECT name FROM foods WHERE id = $foodId";
    $getFoodResult = mysqli_query($db, $getFoodQuery);

    if (!$getFoodResult) {
        continue;
    }
    
    $foodRow = mysqli_fetch_assoc($getFoodResult);
    $foodName = $foodRow['name'];

    echo '<tr>';
    echo '<td>' . $foodName . '</td>';
    echo '<td>$' . $foodPrice . '</td>';
    echo '<td>' . $quantity . '</td>';
    echo '</tr>';
}

$deliveryFee = 0;
if ($totalPrice == 0) {
    echo "Nothing in Cart";
    header("Location: view.php");
}

if ($totalQuantity < 5 || $totalPrice < 100) {
    $deliveryFee = 10;
    $totalPrice = $totalPrice + $deliveryFee;
} else {
    $deliveryFee = 20;
    $totalPrice = $totalPrice + $deliveryFee;
}

echo '<tr>';
echo '<td colspan="2">Quantity:</td>';
echo '<td>' . $totalQuantity .  '</td>';
echo '</tr>';

echo '<tr>';
echo '<td colspan="1">Delivery Fee:</td>';
echo '<td>N$ ' . $deliveryFee . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td colspan="1">Price:</td>';
echo '<td>N$ ' . $totalPrice . '</td>';
echo '</tr>';

echo '</table>';
echo'<p>Please E-wallet the amount to this number 081 216 8687</p>';
echo 'If you are paying in cash, Please <a href="tel:+264 81 216 8687">+264 81 216 8687</a> this number to collect the money';

echo '<form action="" method="post">';
echo '<input type="hidden" name="orderDetails" value="' . implode(',', $cartArray) . '">';
echo '<button type="submit">Order</button>';
echo '</form>';
echo "<a href='view.php'>Back to Shops</a>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderDetails = $_POST['orderDetails'];

    // Fetch user details
    $getUserDetailsQuery = "SELECT name, location FROM users WHERE id = $userId";
    $getUserDetailsResult = mysqli_query($db, $getUserDetailsQuery);

    if (!$getUserDetailsResult) {
        die("Error fetching user details: " . mysqli_error($db));
    }

    $userDetails = mysqli_fetch_assoc($getUserDetailsResult);
    $userName = $userDetails['name'];
    $userLocation = $userDetails['location'];

    // Generate a unique reference ID
    $referenceID = generateUniqueReferenceID($db, $userId);

    // Calculate total quantity and price from the order details
    $orderItems = explode(',', $orderDetails);
    $totalQuantity = count($orderItems);

    $totalPrice = 0;

    foreach ($orderItems as $foodId) {
        // Convert the foodId to an integer
        $foodId = (int)$foodId;

        // Check if the foodId corresponds to a valid food item
        $getFoodPriceQuery = "SELECT price FROM foods WHERE id = $foodId";
        $getFoodPriceResult = mysqli_query($db, $getFoodPriceQuery);

        if (!$getFoodPriceResult) {
            die("Error fetching food price: " . mysqli_error($db));
        }

        if (mysqli_num_rows($getFoodPriceResult) === 0) {
            continue; // Skip invalid food items
        }

        $foodPrice = mysqli_fetch_assoc($getFoodPriceResult)['price'];
        $totalPrice += $foodPrice;
    }

    // Insert order details into the 'orders' table
    $insertOrderQuery = "INSERT INTO orders (user_id, user_order, total, quantity, name, location, reference_id)
                        VALUES ($userId, '$orderDetails', $totalPrice, $totalQuantity, '$userName', '$userLocation', '$referenceID')";


    $insertOrderResult = mysqli_query($db, $insertOrderQuery);

    if (!$insertOrderResult) {
        die("Error inserting order details: " . mysqli_error($db));
    }

    // Update the 'orders' column in the 'users' table
    $updateOrdersQuery = "UPDATE users SET orders = '$referenceID' WHERE id = $userId";
    $updateOrdersResult = mysqli_query($db, $updateOrdersQuery);

    if (!$updateOrdersResult) {
        die("Error updating user orders: " . mysqli_error($db));
    }

    // Clear the user's cart after placing the order
    $clearCartQuery = "UPDATE users SET cart = NULL WHERE id = $userId";
    $clearCartResult = mysqli_query($db, $clearCartQuery);

    if (!$clearCartResult) {
        die("Error clearing user cart: " . mysqli_error($db));
    }

    // Redirect back to the cart page or any other desired page
    header("Location: order_success.php?referenceID=$referenceID");
    exit;
}

/**
 * Function to generate a unique reference ID.
 * @param $db mysqli connection
 * @param $userId int user ID
 * @return string
 */
function generateUniqueReferenceID($db, $userId) {
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    do {
        // Generate new random letters and numbers
        $randomLetters = substr(str_shuffle($letters), 0, 3);
        $randomNumbers = sprintf("%03d", mt_rand(1, 999));

        // Concatenate to form the reference ID
        $referenceID = '#' . $randomLetters . $randomNumbers . $userId;

        // Check if the reference ID already exists
        $checkExistenceQuery = "SELECT COUNT(*) as count FROM orders WHERE reference_id = '$referenceID'";
        $existenceResult = mysqli_query($db, $checkExistenceQuery);

        if (!$existenceResult) {
            die("Error checking reference ID existence: " . mysqli_error($db));
        }

        $count = mysqli_fetch_assoc($existenceResult)['count'];

    } while ($count > 0); // Keep regenerating until a unique reference ID is found

    return $referenceID;
}
?>

<html>

<style>
body {
  font-family: sans-serif;
  margin: 0;
  padding: 0;
}

a {
  text-decoration: none;
  color: #337ab7;
}
.cart-table {
  width: 100%;
  border-collapse: collapse;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

.cart-table th,
.cart-table td {
  padding: 5px 10px;
  border: 1px solid #ccc;
  text-align: left;
  width: 150px;
}

.cart-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.checkout-form {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

.checkout-form input[type="hidden"] {
  display: none;
}

.checkout-form button {
  padding: 10px 20px;
  border: 1px solid #337ab7;
  border-radius: 4px;
  background-color: #337ab7;
  color: white;
  cursor: pointer;
}

.back-to-shop-link {
  text-decoration: none;
  color: #337ab7;
  font-size: 14px;
}
.cart-table td.quantity-column {
     width: 50px;
}


</style>
</html>

<?php 
########   ####      #####      #       #######     ########     #######  ####   ####     ######     #######
#      #   #   #    #      #    #       #                 #      #       #     # #    #   #          #   ##
########   #    #   #      #    #       ###             #       #       #     # #     #  ######     #  ##
#      #   #   #     #    #     #       #              #         #       #     # #    #   #          #   ##
#      #   ####       ####      ######  #            ########    #######  #####  ####     #######    #    ##
?>
