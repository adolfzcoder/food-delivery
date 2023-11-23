<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $shop = $_POST['shop'];
    $type = $_POST['type'];
    $image = $_FILES["image"]["tmp_name"];
    $location = isset($_POST['location']) ? $_POST['location'] : ''; // Check if 'location' is set

    if (!empty($name) && !empty($price) && is_uploaded_file($image) && !empty($location)) {
        // Read the image file
        $imageData = file_get_contents($image);

        // Prepare and execute the SQL query to insert data
        $stmt = $db->prepare("INSERT INTO foods (name, type, price, shop, shop_location, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssb", $name, $type, $price, $shop, $location, $imageData);

        if ($stmt->execute()) {
            echo "Submitted successfully!";
            echo "<script>alert('Submitted Successfully')</script>";
        } else {
            echo "Error inserting data: " . $stmt->error;
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
    <title>Add Food/Drink</title>
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
    <h1>Upload food or drink</h1>
    <div class="container">
        <form method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder='eg. mushroom' required>

            <label for="type">Type:</label>
            <input type="text" id="type" name="type" placeholder='eg. idk dessert or smthin' >

            <label for="price">Price:</label>
            <input id="price" placeholder='N$' name="price" required>

            <label for="shop">shop:</label>
            <input id="shop" placeholder="eg. Debonairs" name="shop" required><br>

            <label for="location">(Town)Location:</label>
            <select name="location" id="location">
                <option value="Ongwediva">Ongwediva</option>
                <option value="Oshakati">Oshakati</option>
            </select>
            <br><br>

            Any additional information:
                <textarea name="type" id="type" cols="30" rows="10"></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <input type="submit" value="Upload">
        </form>
        <a href="view.php">Go to home page</a>
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

