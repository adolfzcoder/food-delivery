<?php 
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
  exit;
}

include 'db_conn.php';

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
    $cartCounts[$foodId] = 1; // Add the item with initial quantity of 1
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

echo '<tr>';
echo '<td colspan="2">Total:</td>';
echo '<td>' . $totalQuantity . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td colspan="2">Price:</td>';
echo '<td>$' . $totalPrice . '</td>';
echo '</tr>';

echo '</table>';

echo '<form action="" method="post">';
echo '<button type="submit">Order</button>';
echo '</form>';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO orders values (user_order, total, quantity, name, location)";
}

