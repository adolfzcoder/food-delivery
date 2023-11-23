<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
  exit;
}

include 'db_conn.php';

$userId = $_SESSION['id'];

$isDriverQuery = "SELECT is_driver FROM users WHERE id = $userId";
$isDriverResult = mysqli_query($db, $isDriverQuery);

if (!$isDriverResult) {
  die("Error checking driver status: " + mysqli_error($db));
}

$isDriverRow = mysqli_fetch_assoc($isDriverResult);
$isDriver = $isDriverRow['is_driver'];

if ($isDriver) {
  // Driver view
  $getAllConversationsQuery = "SELECT * FROM conversations";
  $getAllConversationsResult = mysqli_query($db, $getAllConversationsQuery);

  if (!$getAllConversationsResult) {
    die("Error fetching conversations: " + mysqli_error($db));
  }

  echo "<ul>";

  while ($conversationRow = mysqli_fetch_assoc($getAllConversationsResult)) {
    $conversationId = $conversationRow['id'];
    $conversationUser = $conversationRow['user_id'];

    $getUserDetailsQuery = "SELECT name FROM users WHERE id = $conversationUser";
    $getUserDetailsResult = mysqli_query($db, $getUserDetailsQuery);

    if (!$getUserDetailsResult) {
      continue;
    }

    $userDetailsRow = mysqli_fetch_assoc($getUserDetailsResult);
    $userName = $userDetailsRow['name'];

    echo "<li><a href='conversation.php?id=$conversationId'>$userName</a></li>";
  }

  echo "</ul>";

  echo "<br><a href='order_history.php'>View Order History</a>";
} else {
  // Non-driver view
  $getAssignedDriverQuery = "SELECT driver_id FROM orders WHERE status = 'Pending' AND user_id = $userId";
  $getAssignedDriverResult = mysqli_query($db, $getAssignedDriverQuery);

  if (!$getAssignedDriverResult) {
    die("Error fetching assigned driver: " + mysqli_error($db));
  }

  if (mysqli_num_rows($getAssignedDriverResult) === 0) {
    echo "<h2>No pending orders</h2>";
    echo "<a href='order.php'>Place a new order</a>";
  } else {
    $assignedDriverRow = mysqli_fetch_assoc($getAssignedDriverResult);
    $assignedDriverId = $assignedDriverRow['driver_id'];

    $getConversationQuery = "SELECT * FROM conversations WHERE (user_id = $userId AND driver_id = $assignedDriverId) OR (user_id = $assignedDriverId AND driver_id = $userId)";
    $getConversationResult = mysqli_query($db, $getConversationQuery);

    if (!$getConversationResult) {
      die("Error fetching conversation: " + mysqli_error($db));
    }

    if (mysqli_num_rows($getConversationResult) === 0) {
      echo "<h2>Awaiting driver pickup</h2>";
    } else {
      $conversationRow = mysqli_fetch_assoc($getConversationResult);
      $conversationId = $conversationRow['id'];

      echo "<h2>Messaging with Driver</h2>";
      echo "<a href='conversation.php?id=$conversationId'>Continue Conversation</a>";
    }
  }
}
