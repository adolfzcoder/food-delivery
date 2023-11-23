<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <style>
        body {
            background-color: #f2f2f2;
            font-family: sans-serif;
        }

        #form {
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

        #btn {
            background-color: #337ab7;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        a {
            text-decoration: none;
            color: #337ab7;
        }

    </style>
</head>
<body>
    <div id="form">
        <form action="process.php" method="post">
            <p>
                <label for="">Email Address</label>
                <input type="email" id="email" name="email" required><br>
            </p>
            <p>
                <label for="">Password</label>
                <input type="password" id="password" name="password" required><br>
            </p>
            <p>
                <input type="submit" id="btn" value="Login">
            </p>
        </form>
        <a href="register.php">Don't have an account? Sign up</a>
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