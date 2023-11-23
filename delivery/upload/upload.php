<?php
include 'db_conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $description = $_POST["description"];
    $image = $_FILES["image"]["tmp_name"];

    if (!empty($username) && !empty($description) && is_uploaded_file($image)) {
        // Read the image file
        $imageData = file_get_contents($image);

        // Prepare and execute the SQL query to insert data
        $stmt = $db->prepare("INSERT INTO users (username, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $description, $imageData);

        if ($stmt->execute()) {
            echo "User data inserted successfully!";
        } else {
            echo "Error inserting user data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please fill in all fields and select an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Upload your picture</h1>
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <input type="submit" value="Upload">
        </form>
        <a href="index.html">Go to home page</a>
    </div>
</body>
</html>
