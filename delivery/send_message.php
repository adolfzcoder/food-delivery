<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Get user details
$userId = $_SESSION['id'];

// Retrieve driver status from the database
$getDriverStatusQuery = "SELECT is_driver FROM users WHERE id = $userId";
$getDriverStatusResult = mysqli_query($db, $getDriverStatusQuery);

if (!$getDriverStatusResult || mysqli_num_rows($getDriverStatusResult) == 0) {
    echo "Error fetching user details";
    exit;
}

$userDetails = mysqli_fetch_assoc($getDriverStatusResult);
$isDriver = $userDetails['is_driver'];

// Check if the user is a driver
if (!$isDriver) {
    echo "You do not have permission to view this page.";
    exit;
}

// The rest of your messaging logic for drivers goes here
// ...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Messages</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>

<!-- Your driver messaging interface goes here -->

</body>
</html>


<?php 
########   ####      #####      #       #######     ########     #######  ####   ####     ######     #######
#      #   #   #    #      #    #       #                 #      #       #     # #    #   #          #   ##
########   #    #   #      #    #       ###             #       #       #     # #     #  ######     #  ##
#      #   #   #     #    #     #       #              #         #       #     # #    #   #          #   ##
#      #   ####       ####      ######  #            ########    #######  #####  ####     #######    #    ##
?>