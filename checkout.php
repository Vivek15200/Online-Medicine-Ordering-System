<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all products from user's cart
$cart_query = "
    SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.description
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
";
$cart_items = $conn->query($cart_query);

if ($cart_items->num_rows === 0) {
    echo "<p>Your cart is empty. <a href='home.php'>Go back</a>.</p>";
    exit;
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - MedicineStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-green-700">Confirm Your Order</h1>

        <?php while ($item = $cart_items->fetch_assoc()): 
            $item_total = $item['price'] * $item['quantity'];
            $total += $item_total;
        ?>
        <div class="flex flex-col sm:flex-row sm:space-x-6 mb-6 border-b pb-4">
            <div class="w-full sm:w-1/4">
                <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-32 object-cover rounded-lg shadow-md">
            </div>
            <div class="w-full sm:w-3/4 mt-4 sm:mt-0">
                <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($item['description']) ?></p>
                <p class="text-sm mt-2"><strong>Price:</strong> ₹<?= $item['price'] ?></p>
                <p class="text-sm"><strong>Quantity:</strong> <?= $item['quantity'] ?></p>
                <p class="text-sm"><strong>Subtotal:</strong> ₹<?= $item_total ?></p>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="text-right text-xl font-bold text-green-800 mb-4">
            Grand Total: ₹<?= $total ?>
        </div>

        <form method="POST" action="confirmation_successful.php">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                Confirm
            </button>
        </form>
    </div>
</body>
</html>
