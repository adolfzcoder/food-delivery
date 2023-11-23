<?php
session_start();
include("db_conn.php");
include("navbar.php");
// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user information from the database
$query = "SELECT id, name, email, phone_number, cart, picture_of_id, location, is_driver, orders FROM users WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $id, $name, $email, $phone_number, $cart, $picture_of_id, $location, $is_driver, $orders);
    mysqli_stmt_fetch($stmt);
} else {
    // Handle the case where the user is not found
    echo "User not found.";
    exit();
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .profile-container {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Your Profile</h2>
        <label for="name">Name: <?php echo $name; ?></label>
        <label for="email">Email: <?php echo $email; ?></label>
        <label for="phone_number">Phone Number: <?php echo $phone_number; ?></label>
        <label for="location">Location: <?php echo $location; ?></label>

        <!-- You can display other user information similarly -->

        <?php if ($picture_of_id): ?>
            <label for="picture">Picture ID:</label>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($picture_of_id); ?>" alt="User Picture">
        <?php endif; ?>

        <br><br>
        <a href="edit_profile.php">Edit Profile</a>
        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
