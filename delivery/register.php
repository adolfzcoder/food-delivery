<?php
session_start();
include("db_conn.php");

if (isset($_POST['register'])) {
    // Using prepared statements to prevent SQL injection
    $name = $_POST['name'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $image = $_FILES["image"]["tmp_name"];

    // Validate and sanitize user inputs
    $name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name);

    if (empty($name)) {
        echo "Error: Invalid characters in the name. Please use only letters and numbers.";
        exit;
    }

    // Check if the username already exists
    $checkUsernameQuery = "SELECT id FROM users WHERE name = ?";
    $checkUsernameStmt = mysqli_prepare($db, $checkUsernameQuery);
    mysqli_stmt_bind_param($checkUsernameStmt, "s", $name);
    mysqli_stmt_execute($checkUsernameStmt);
    mysqli_stmt_store_result($checkUsernameStmt);

    if (mysqli_stmt_num_rows($checkUsernameStmt) > 0) {
        echo "<script>alert('Error: This Name is already taken. Try adding your initials.'); window.location.href='register.php';</script>";
        mysqli_stmt_close($checkUsernameStmt);
        exit;
    }

    mysqli_stmt_close($checkUsernameStmt);

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $checkEmailStmt = mysqli_prepare($db, $checkEmailQuery);
    mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
    mysqli_stmt_execute($checkEmailStmt);
    mysqli_stmt_store_result($checkEmailStmt);

    if (mysqli_stmt_num_rows($checkEmailStmt) > 0) {
        echo "<script>alert('Error: This email is already registered. Please use a different email.'); window.location.href='register.php';</script>";
        mysqli_stmt_close($checkEmailStmt);
        exit;
    }

    mysqli_stmt_close($checkEmailStmt);

    if (!empty($name) && !empty($password) && !empty($email) && !empty($location) && is_uploaded_file($image)) {
        // Prepare and execute the SQL query to insert data
        $imageData = file_get_contents($image);

        $insertQuery = "INSERT INTO users (name, password, email, phone_number, picture_of_id, location) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $insertQuery);

        mysqli_stmt_bind_param($stmt, "ssssss", $name, $password, $email, $phone_number, $imageData, $location);

        $insertResult = mysqli_stmt_execute($stmt);

        if ($insertResult) {
            echo "<script>alert('Registration successful! Please login to continue.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error inserting data: " . mysqli_error($db) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: sans-serif;
        }

        form {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
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
<form action="register.php" method="post" enctype="multipart/form-data">
    <label for="name">Full Name and Initials:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="phone_number">Phone number:</label>
    <input type="text" id="phone_number" name="phone_number" placeholder="0812345678"><br>

    <label for="location">(Town)Location:</label>
    <select name="location" id="location">
    <option value="">Select</option>
        <option value="ongwediva">Ongwediva</option>
        <option value="oshakati">Oshakati</option>
    </select>
    <br><br>
    <label for="image">Upload Image:</label>
    <input type="file" id="image" name="image" accept="image/*" required>

    <input type="submit" name="register" value="SIGN UP"><br><br>
    <a href="login.php">Already have an account</a>
</form>
</body>
</html>
