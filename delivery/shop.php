<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include 'db_conn.php';
include"navbar.php"; 
    // Check if a shop name is provided in the URL
    if (!isset($_GET['shop'])) {
        die("Error: Shop name not provided.");
    }

    $shopName = $_GET['shop'];

    // Get all food items from the specified shop
    $sql = "SELECT * FROM foods WHERE shop = '$shopName'";
    $result = mysqli_query($db, $sql);
    
    // Display the shop name as a heading
    echo '<h2>' . $shopName . '</h2>';

    // Display the food items
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="post-container">'; // Start the post container

        // Display the image
        echo '<img class="post-image" src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" alt="Food Image">';

        // Display the food item details
        echo '<div class="post-info">';
        echo '<strong>' . $row['name'] . '</strong>';

        // Display the description
        echo '<p><strong>N$</strong> ' . $row['price'] . '</p>';

        echo '<div class="type">';
        echo '<strong>Type:</strong> ' . $row['type'];
        echo '</div>';

        // Add the "Add to Cart" form
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="food_id" value="' . $row['id'] . '">';
        echo '<input type="submit" name="add_to_cart" value="Add to Cart">';
        echo '</form>';

        echo '</div>'; // Close post-info div

        // Add buttons for liking and sharing

        echo '</div>'; // Close post container
    }

    // Handle the "Add to Cart" functionality when the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $foodId = $_POST['food_id'];

        // Check if the cart session variable is already set
        if (!isset($_SESSION['cart'])) {
            // If not, initialize it as an empty array
            $_SESSION['cart'] = [];
        }

        // Add the selected food item to the cart
        $_SESSION['cart'][] = $foodId;

        
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $shopName; ?></title>
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
        .post-container {
  display: inline-flex;
  flex-direction: column;
  margin: 20px 0;
  padding: 20px;
  max-width: 220px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
}

.post-image {
  width: 100px;
  height: 100px;
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

.add-to-cart-button {
  padding: 5px 10px;
  border: none;
  border-radius: 3px;
  background-color: #337ab7;
  color: white;
  cursor: pointer;
}

.post-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.share-link {
  font-size: 12px;
  color: #999;
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
.form select,
.form input[type="submit"] {
  display: inline-block;
  padding: 5px 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
}
.form {
  text-align: center;
  margin: 20px 0;
}

.form label {
  display: block;
  margin-bottom: 5px;
}

    </style>
</head>
<body>

 

    

    <!-- JavaScript for showing an alert when the item is added to the cart -->
    <script>
        // Function to show an alert when the item is added to the cart
        function showAlert() {
            alert("Item added to the cart!");
        }

        // Add a click event listener to the "Add to Cart" buttons
        const addToCartButtons = document.querySelectorAll('[name="add_to_cart"]');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', showAlert);
        });
    </script>
</body>
</html>
