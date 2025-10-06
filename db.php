<?php
$servername = "localhost";
$username   = "uasxxqbztmxwm";
$password   = "wss863wqyhal";
$dbname     = "dbyzdtsqa5fb0s";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>
