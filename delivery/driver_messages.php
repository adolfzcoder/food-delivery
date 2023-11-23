<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['id']) || !$_SESSION['is_driver']) {
    header("Location: login.php");
    exit;
}

$driverId = $_SESSION['id'];

// Fetch all user IDs that have conversations with this driver
$getUserIdsQuery = "SELECT DISTINCT user_id FROM messages WHERE driver_id = $driverId";
$getUserIdsResult = mysqli_query($db, $getUserIdsQuery);

if (!$getUserIdsResult) {
    echo "Error fetching user IDs: " . mysqli_error($db);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .conversation {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .message-form {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Driver Messages</h2>

<?php
// Display conversations with each user
while ($userRow = mysqli_fetch_assoc($getUserIdsResult)) {
    $otherUserId = $userRow['user_id'];

    // Fetch and display messages for each conversation
    $getChatHistoryQuery = "SELECT * FROM messages WHERE (user_id = $otherUserId AND driver_id = $driverId) OR (user_id = $driverId AND driver_id = $otherUserId)";
    $getChatHistoryResult = mysqli_query($db, $getChatHistoryQuery);

    if (!$getChatHistoryResult) {
        echo "Error fetching chat history: " . mysqli_error($db);
        exit;
    }
?>

<div class="conversation">
    <h3>Conversation with User <?php echo $otherUserId; ?></h3>
    <div id="messages">
        <?php
        // Display chat history for each conversation
while ($messageRow = mysqli_fetch_assoc($getChatHistoryResult)) {
    echo '<p class="' . ($messageRow['user_id'] == $driverId ? 'driver-message' : 'user-message') . '">';
    echo $messageRow['user_id'] == $driverId ? 'Driver: ' : 'You: '; // Corrected logic here
    echo $messageRow['message_text'];
    echo '</p>';
}

        ?>
    </div>

    <?php
    // Display message form for each conversation
    echo '<form class="message-form" method="post">';
    echo '<input type="hidden" name="other_user_id" value="' . $otherUserId . '">';
    echo '<textarea name="message" rows="4" cols="50" placeholder="Type your message..."></textarea>';
    echo '<br>';
    echo '<button type="submit" name="send_message">Send Message</button>';
    echo '</form>';
    ?>
</div>

<?php } ?>

<?php
// Check if a message is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $message = mysqli_real_escape_string($db, $_POST['message']);
    $otherUserId = (int)$_POST['other_user_id'];

    // Check if the driver has permission to send a message to this user
    $checkPermissionQuery = "SELECT COUNT(*) FROM messages WHERE user_id = $otherUserId AND driver_id = $driverId";
    $checkPermissionResult = mysqli_query($db, $checkPermissionQuery);

    if (!$checkPermissionResult) {
        echo "Error checking permission: " . mysqli_error($db);
        exit;
    }

    $permissionCount = mysqli_fetch_assoc($checkPermissionResult)['COUNT(*)'];

    if ($permissionCount > 0) {
        // Insert the message into the database
        $insertMessageQuery = "INSERT INTO messages (user_id, driver_id, message_text)
                               VALUES ($otherUserId, $driverId, '$message')";
        $insertMessageResult = mysqli_query($db, $insertMessageQuery);

        if (!$insertMessageResult) {
            echo "Error inserting message: " . mysqli_error($db);
        }
    } else {
        echo "You do not have permission to send a message to this user.";
    }
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
