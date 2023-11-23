<?php
session_start();
include("db_conn.php");

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user information from the database
$query = "SELECT id, name, email, phone_number, location FROM users WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_bind_result($stmt, $id, $name, $email, $phone_number, $location);
    mysqli_stmt_fetch($stmt);
} else {
    // Handle the case where the user is not found
    echo "User not found.";
    exit();
}

mysqli_stmt_close($stmt);

// Handle form submission
if (isset($_POST['update_profile'])) {
    $new_name = $_POST['new_name'];
    $new_email = $_POST['new_email'];
    $new_phone_number = $_POST['new_phone_number'];
    $new_location = $_POST['new_location'];

    // Validate and sanitize user inputs
    $new_name = filter_var($new_name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $new_name = preg_replace("/[^a-zA-Z0-9\s]/", "", $new_name);

    // You can add more validation for email, phone_number, and location

    // Update user information in the database
    $updateQuery = "UPDATE users SET name = ?, email = ?, phone_number = ?, location = ? WHERE id = ?";
    $updateStmt = mysqli_prepare($db, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ssssi", $new_name, $new_email, $new_phone_number, $new_location, $user_id);

    if (mysqli_stmt_execute($updateStmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . mysqli_error($db) . "');</script>";
    }

    mysqli_stmt_close($updateStmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .edit-profile-container {
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

        input[type="text"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #337ab7;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include("navbar.php");?>
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form action="edit_profile.php" method="post">
            <label for="new_name">New Name:</label>
            <input type="text" id="new_name" name="new_name" value="<?php echo $name; ?>" required>

            <label for="new_email">New Email:</label>
            <input type="text" id="new_email" name="new_email" value="<?php echo $email; ?>" required>

            <label for="new_phone_number">New Phone Number:</label>
            <input type="text" id="new_phone_number" name="new_phone_number" value="<?php echo $phone_number; ?>" required>

            <label for="new_location">New Location:</label>
            <input type="text" id="new_location" name="new_location" value="<?php echo $location; ?>" required>

            <input type="submit" name="update_profile" value="Update Profile">
        </form>

        <br>
        <a href="profile.php">Back to Profile</a>
    </div>
</body>
</html>

<?php 
########   ####      #####      #       #######     ########     #######  ####   ####     ######     #######
#      #   #   #    #      #    #       #                 #      #       #     # #    #   #          #   ##
########   #    #   #      #    #       ###             #       #       #     # #     #  ######     #  ##
#      #   #   #     #    #     #       #              #         #       #     # #    #   #          #   ##
#      #   ####       ####      ######  #            ########    #######  #####  ####     #######    #    ##
?>
