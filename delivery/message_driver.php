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

// If a message is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = mysqli_real_escape_string($db, $_POST['message']);

    if (!empty($message)) {
        // Insert the message into the database
        $insertMessageQuery = "INSERT INTO messages (user_id, driver_id, message_text)
                               VALUES ($userId, 9, '$message')";
        $insertMessageResult = mysqli_query($db, $insertMessageQuery);

        if (!$insertMessageResult) {
            echo "Error inserting message: " . mysqli_error($db);
        }
    }
}

// Retrieve chat history
$getChatHistoryQuery = "SELECT * FROM messages WHERE (user_id = $userId AND driver_id = 9) OR (user_id = 9 AND driver_id = $userId)";
$getChatHistoryResult = mysqli_query($db, $getChatHistoryQuery);

if (!$getChatHistoryResult) {
    echo "Error fetching chat history: " . mysqli_error($db);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        #message-container {
            max-width: 600px;
            margin: auto;
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

<div id="message-container">
    <h2>Messages</h2>
    <div class="conversation">
        <h3>Conversation with Driver</h3>
        <div id="messages">
            <?php
            // Display chat history
            while ($messageRow = mysqli_fetch_assoc($getChatHistoryResult)) {
                echo '<p>';
                echo $messageRow['user_id'] == $userId ? 'You: ' : 'Driver: ';
                echo $messageRow['message_text'];
                echo '</p>';
            }
            ?>
        </div>

        <?php
        // Display message form
        echo '<form class="message-form" action="message_driver.php" method="post">';
        echo '<textarea name="message" rows="4" cols="50" placeholder="Type your message..."></textarea>';
        echo '<br>';
        echo '<button type="submit">Send Message</button>';
        echo '</form>';
        ?>
    </div>

    <?php
    if ($isDriver) {
        // Display all conversations for the driver
        $getAllConversationsQuery = "SELECT user_id FROM messages WHERE driver_id = 9 GROUP BY user_id";
        $getAllConversationsResult = mysqli_query($db, $getAllConversationsQuery);

        if (!$getAllConversationsResult) {
            echo "Error fetching all conversations: " . mysqli_error($db);
            exit;
        }

        while ($conversationRow = mysqli_fetch_assoc($getAllConversationsResult)) {
            $otherUserId = $conversationRow['user_id'];

            // Retrieve chat history for each conversation
            $getOtherChatHistoryQuery = "SELECT * FROM messages WHERE (user_id = $otherUserId AND driver_id = 9) OR (user_id = 9 AND driver_id = $otherUserId)";
            $getOtherChatHistoryResult = mysqli_query($db, $getOtherChatHistoryQuery);

            if (!$getOtherChatHistoryResult) {
                echo "Error fetching chat history for user $otherUserId: " . mysqli_error($db);
                exit;
            }
    ?>
    <div class="conversation">
        <h3>Conversation with User <?php echo $otherUserId; ?></h3>
        <div id="messages">
            <?php
            // Display chat history for each conversation
            while ($messageRow = mysqli_fetch_assoc($getOtherChatHistoryResult)) {
                echo '<p>';
                echo $messageRow['user_id'] == $userId ? 'You: ' : 'Driver: ';
                echo $messageRow['message_text'];
                echo '</p>';
            }
            ?>
        </div>

        <?php
        // Display message form for each conversation
        echo '<form class="message-form" action="message_driver.php" method="post">';
        echo '<textarea name="message" rows="4" cols="50" placeholder="Type your message..."></textarea>';
        echo '<br>';
        echo '<button type="submit">Send Message</button>';
        echo '</form>';
        ?>
    </div>
    <?php } ?>
    <?php } ?>

</div>

</body>
</html>
