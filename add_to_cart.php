<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $user_id = $_SESSION['user_id'];

    // Check if item already in cart
    $check = $conn->query("SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    if ($check->num_rows > 0) {
        // Update quantity
        $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        // Insert new item
        $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
    }

    header("Location: cart.php");
    exit;
}
?>
