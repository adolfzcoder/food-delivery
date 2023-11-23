<?php 
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "delivery";


$db = new mysqli($servername, $username, $password, $database_name);

if ($db->connect_error) {
    echo "Could not connect";
    die("Connection Failed!" . $db->connect_error);
}
?>