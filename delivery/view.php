<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'db_conn.php';

$name = $_SESSION['name'];
$location = $_SESSION['location'];

if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    $sql = "SELECT * FROM foods WHERE name LIKE '%$searchTerm%'";
} else {
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'random';

    if ($sort == 'price_desc') {
        $sql = "SELECT * FROM foods ORDER BY price DESC";
    } elseif ($sort == 'price_asc') {
        $sql = "SELECT * FROM foods ORDER BY price ASC";
    } else {
        $sql = "SELECT * FROM foods";
    }
}

$result = mysqli_query($db, $sql);

if (!$result) {
    die("Error: " . mysqli_error($db));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $foodId = $_POST['food_id'];
        $userId = $_SESSION['id'];

        // Fetch the current cart value
        $getUserCartQuery = "SELECT cart FROM users WHERE id = $userId";
        $getUserCartResult = mysqli_query($db, $getUserCartQuery);

        if (!$getUserCartResult) {
            die("Error fetching user cart: " . mysqli_error($db));
        }

        $userCartRow = mysqli_fetch_assoc($getUserCartResult);
        $currentCart = $userCartRow['cart'];

        // Append the new foodId to the current cart (comma-separated values)
        $newCart = $currentCart . $foodId . ',';

        // Update the user's cart in the database
        $updateCartQuery = "UPDATE users SET cart = '$newCart' WHERE id = $userId";
        $updateResult = mysqli_query($db, $updateCartQuery);

        if (!$updateResult) {
            die("Error updating cart: " . mysqli_error($db));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LQZH0H9VYX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-LQZH0H9VYX');
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        function copyToClipboard(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
        }

        const shareLinks = document.querySelectorAll('.share-link');
        shareLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const profileLink = link.getAttribute('href');
                copyToClipboard('https://gabrieltaapopiss.000webhostapp.com/' + profileLink);
                alert('Profile link copied to clipboard!');
            });
        });
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap 'n Chow</title>
    <style>
        /* General styling */

body {
  font-family: sans-serif;
  margin: 0;
  padding: 0;
}

a {
  text-decoration: none;
  color: #337ab7;
}

/* Navbar styling */

.navbar {
  background-color: #f2f2f2;
  padding: 20px;
}

.navbar-brand {
  font-size: 24px;
  font-weight: bold;
  color: #337ab7;
}

.navbar-nav {
  list-style: none;
  margin: 0;
  padding: 0;
}

.navbar-nav li {
  display: inline-block;
  margin-left: 20px;
}

.navbar-nav a {
  padding: 5px 10px;
  border-radius: 3px;
  background-color: #ccc;
  color: #333;
}

.navbar-nav a:hover {
  background-color: #eee;
}

/* Search form styling */

#search-form {
  margin: 20px 0;
}

#search-form input {
  width: 200px;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

#search-form input[type="submit"] {
  padding: 5px 10px;
  border: none;
  border-radius: 3px;
  background-color: #337ab7;
  color: white;
  cursor: pointer;
}

/* Post container styling */

.post-container {
  display: flex;
  flex-direction: column;
  margin: 20px 0;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
}

.post-image {
  width: 220px;
  height: 220px;
  object-fit: cover;
  border-radius: 4px;
}

.post-info {
  margin-top: 10px;
}

.post-info h3 {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 5px;
}

.post-info p {
  margin-bottom: 5px;
}

.type {
  font-size: 12px;
  color: #999;
}

/* Post buttons styling */

#shops2 {
    display: inline-grid;
}
.post-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.post-buttons form {
  display: inline-block;
}

.post-buttons input[type="submit"] {
  padding: 5px 10px;
  border: none;
  border-radius: 3px;
  background-color: #337ab7;
  color: white;
  cursor: pointer;
}

.share-link {
  font-size: 12px;
  color: #999;
}
#shops {
    display: none;
}

.share-icon {
  margin-left: 5px;
  font-size: 14px;
}

/* Additional styling */

.grid-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.shop-container {
  border: 1px solid #ccc;
  padding: 20px;
  margin-bottom: 20px;
}

h2 {
  text-align: center;
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}
.shop-bar {
    background-color: #f2f2f2;
    padding: 20px;
    display: flex;
    justify-content: space-between;
}

.shop-link {
    padding: 5px 10px;
    border-radius: 3px;
    background-color: #ccc;
    color: #333;
    text-decoration: none;
}

.shop-link:hover {
    background-color: #eee;
}

.form {
  text-align: center;
  margin: 20px 0;
}

.form label {
  display: block;
  margin-bottom: 5px;
}

.form select,
.form input[type="submit"] {
  display: inline-block;
  padding: 5px 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
}


        @media screen and (max-width: 768px) {
            form {
                flex-direction: column;
                text-align: center;
            }

            input[type="text"] {
                width: 100%;
                max-width: none;
                margin-bottom: 10px;
            }

            input[type="submit"] {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<?php include"navbar.php"; ?>
<br><br>
<!-- Search form -->
<form method="post">
    <input type="text" name="search_term" placeholder="Search by Food Name">
    <input type="submit" name="search" value="Search">
</form>



<!-- Sorting options form -->
<form method="get" class="form">
    <label for="sort">Sort by:</label>
    <select name="sort" id="sort">
        <option value="price_desc">Highest Price</option>
        <option value="price_asc"> Lowest Price</option>
    </select>
    <input type="submit" value="Sort">
</form>

<?php

// Loop through the results and display user data in the post-like format
echo '<div class="grid-container">'; // Start the grid container
// Array to store items for each shop
$items = array(
    'KFC' => array(),
    'Steers' => array(),
    'Debonairs' => array(),
    'Woerman' => array(),
    'Pick and Pay' => array(),
    
);
$sql1 = "SELECT DISTINCT shop FROM foods";
            $result1 = mysqli_query($db, $sql1);

            while ($row = mysqli_fetch_assoc($result1)) {
                $shopName = $row['shop'];
                echo "<a href='shop.php?shop=$shopName' class='shop-link'>$shopName</a>";
            }
while ($row = mysqli_fetch_assoc($result)) {

    // Determine the shop for the current item
     // Determine the shop for the current item
     $currentShop =$row['shop'];

    // Display the item within the corresponding shop div

    // $shopName = $row['shop'];
    //             echo "<a href='shop.php?shop=$shopName' class='shop-link'>$shopName</a>";
            

    // Add the current item to the corresponding shop in the $items array
    $items[$currentShop][] = $row;


    echo '<div id="shops" class="post-container ' . $currentShop . '">';

    echo "<a href='shop.php?shop=$shopName' class='shop-link'>$currentShop</a>";
    // Display the image
    echo '<img class="post-image" src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" alt="User Image">';

    // Display the shop location
    echo '<p><strong>Shop Location:</strong> ' . $row['shop_location'] . '</p>';


    // Display the username
    echo '<div class="post-info">';
    echo '<strong></strong> ' . $row['name'];

    // Display the description
    echo '<p><strong>N$</strong> ' . $row['price'] . '</p>';

    echo '<div class="type">';
    echo '<strong>Type:</strong> ' . $row['type'];
    echo '<strong>Location:</strong> ' . $row['shop_location'];
    echo '</div>';

    echo '</div>'; // Close post-info div

    // Buttons container
    echo '<div class="post-buttons">';

    echo '<form action="" method="post">';
    echo '<input type="hidden" name="user_id" value="' . $row['id'] . '">';
    // ...

    echo '</form>';

    echo '<form action="" method="post">';
    echo '<input type="hidden" name="food_id" value="' . $row['id'] . $row['name'] . '">';
    
    echo '<span class="like-count">' . $row['shop'] . '</span>' . '<br>';

    echo '<input type="submit" name="add_to_cart" value="Add to Cart">';
    echo '</form>';

    // Share button
    echo '<a href="food.php?id=' . $row['id'] . '" class="share-link">Share <i class="fas fa-share share-icon"></i></a>' . "<br>";

    // Erro up on line 273
    echo '</div>'; // Close post-buttons div

    echo '</div>'; // Close post-container div
}
echo '</div>'; // Close the grid container

foreach ($items as $shop => $shopItems) {
    // Display the shop name as a heading
    echo '<h2>' . ucwords(str_replace('_', ' ', $shop)) . '</h2>'; // Add styling for each shop if needed

    

    // Display items for the current shop
    foreach (array_slice($shopItems, 0, 5) as $item) {
    // Display the item as needed
    echo '<div id="shops2" class="post-container ' . $shop . '">';
    // Display the image
    echo '<img class="post-image" src="data:image/jpeg;base64,' . base64_encode($item['image']) . '" alt="User Image">';

    // Display the username
    echo '<div class="post-info">';
    echo '<strong></strong> ' . $item['name'];
    
    // Display the description
    echo '<p><strong>N$</strong> ' . $item['price'] . '</p>';
    
    // Display the shop location
    echo '<p><strong>Shop Location:</strong> ' . $item['shop_location'] . '</p>';

    echo '<div class="type">';
    echo '<strong>Type:</strong> ' . $item['type'];

    echo '</div>';

    echo '</div>'; // Close post-info div

    // Buttons container
    echo '<div class="post-buttons">';

    echo '<form action="" method="post">';
    echo '<input type="hidden" name="user_id" value="' . $item['id'] . '">';
    // ...

    echo '</form>';

    echo '<form action="" method="post">';
    echo '<input type="hidden" name="food_id" value="' . $item['id'] . $item['name'] . '">';

    echo '<span class="like-count">' . $item['shop'] . '</span>' . '<br>';

    echo '<input type="submit" name="add_to_cart" value="Add to Cart">';
    echo '</form>';

    // Share button
    echo '<a href="food.php?id=' . $item['id'] . '" class="share-link">Share <i class="fas fa-share share-icon"></i></a>' . "<br>";

    echo '</div>'; // Close post-buttons div

    echo '</div>'; // Close post-container div
}

    echo '</div>'; // Close shop-container div
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