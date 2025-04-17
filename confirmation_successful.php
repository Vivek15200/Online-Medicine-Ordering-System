<?php
session_start();
include 'db.php';

if (!isset($_POST['user_id']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_query = "
    SELECT c.product_id, c.quantity, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
";
$cart_items = $conn->query($cart_query);

if ($cart_items->num_rows === 0) {
    echo "<p>Your cart is empty. <a href='home.php'>Go back</a>.</p>";
    exit;
}

// Insert each cart item into the orders table
while ($item = $cart_items->fetch_assoc()) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $total_price = $quantity * $price;
    $status = 'Pending';
    $order_date = date('Y-m-d H:i:s');

    $insert_query = "
        INSERT INTO orders (user_id, product_id, quantity, total_price, order_date, status)
        VALUES ('$user_id', '$product_id', '$quantity', '$total_price', '$order_date', '$status')
    ";

    $conn->query($insert_query);
}

// Clear the user's cart
$conn->query("DELETE FROM cart WHERE user_id = $user_id");
?>

<!-- ✅ Beautiful Success Page UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful - MedicineStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded-2xl shadow-lg text-center max-w-md w-full">
        <!-- Branding -->
        <h1 class="text-3xl font-extrabold text-green-700 mb-2">MedicineStore</h1>
        <div class="flex justify-center mb-4">
            <!-- Checkmark animation -->
            <svg class="w-16 h-16 text-green-500 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h2>
        <p class="text-gray-600 mb-6">Thank you for shopping with us. Your order is confirmed and will be delivered soon.</p>

        <a href="home.php" class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200">
            Back to Home
        </a>
    </div>
</body>
</html>
