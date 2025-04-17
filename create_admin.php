<?php
$conn = new mysqli("localhost", "root", "", "medicine_store"); // adjust if needed

$new_password = password_hash("admin123", PASSWORD_DEFAULT); // your new password
$email = "admin@gmail.com";

$sql = "UPDATE admins SET password = '$new_password' WHERE email = '$email'";

if ($conn->query($sql) === TRUE) {
    echo "Password updated successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
