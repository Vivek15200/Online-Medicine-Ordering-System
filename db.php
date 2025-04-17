<?php
$host = "localhost";
$user = "root";         // Update if needed
$password = "";         // Update if needed
$database = "medicine_store";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
