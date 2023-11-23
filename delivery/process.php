<?php
session_start(); // Start the session
include "db_conn.php";

// Validate and sanitize user input
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password']; // No need to sanitize password as it is not directly used in SQL

// Query to db users input from form using prepared statement
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        // Check if the row exists
        if ($password == $row["password"]) {
            // Store user information in the session
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['location'] = $row['location'];
            $_SESSION['is_driver'] = 0;

            echo "<script>alert('Successfully logged in');</script>";
            header("Location: view.php");
            exit;
        } else {
            echo "Incorrect email or password";
        }
    } else {
        echo "No matching user found";
    }
} else {
    echo "Query failed: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
