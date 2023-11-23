<style>

  body {
    font-family:  Arial,sans-serif;
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

</style>

<nav class="navbar">
    <a href="view.php" class="navbar-brand">Tap 'n Chow</a>
    <ul class="navbar-nav">
        

        <?php
        $userId = $_SESSION["id"];
        $getUserDetailsQuery = "SELECT * FROM users WHERE id = $userId";
        $getUserDetailsResult = mysqli_query($db, $getUserDetailsQuery);

        if (!$getUserDetailsResult || mysqli_num_rows($getUserDetailsResult) == 0) {
            echo "Error fetching user details";
            exit;
        }

        $userDetails = mysqli_fetch_assoc($getUserDetailsResult);
        $isDriver = $userDetails['is_driver'];

        // Check if the user is a driver
        if ($isDriver) {

            echo '<li class="nav-item">';
            echo'<a href="view.php" class="nav-link">Shops</a>';
            echo '</li>';

            echo '<li class="nav-item">';
            echo '<a href="view_orders.php" class="nav-link">View orders</a>';
            echo '</li>';

            

            echo '<li class="nav-item">';
            echo '<a href="viewcart.php" class="nav-link">View Cart</a>';
            echo '</li>';
        }
        if (!$isDriver) {
            echo '<li class="nav-item">';
            echo '<a href="view.php" class="nav-link">Shops</a>';
            echo '</li>';

            echo '<li class="nav-item">';
            echo '<a href="orders.php" class="nav-link">View Orders</a>';
            echo '</li>';

            echo '<li class="nav-item">';
            echo '<a href="viewcart.php" class="nav-link">View Cart</a>';
            echo '</li>';

            echo '<li class="nav-item">';
            echo '<a href="profile.php" class="nav-link">Profile</a>';
            echo '</li>';
        }
        ?>
        <li class="nav-item">
            <a href="logout.php" class="nav-link">Logout</a>
        </li>
    </ul>
</nav>