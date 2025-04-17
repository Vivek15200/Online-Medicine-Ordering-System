<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle cancel request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    $conn->query("UPDATE orders SET status = 'Cancelled' WHERE id = $order_id AND user_id = $user_id AND status = 'Confirmed'");
}

// Fetch user's orders
$orders = $conn->query("
    SELECT o.id, o.quantity, o.total_price, o.status, o.order_date, p.name, p.image
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - MedicineStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white shadow p-6 rounded">
        <h2 class="text-2xl font-bold text-green-700 mb-4">My Orders</h2>

        <?php if ($orders->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <div class="flex items-center justify-between bg-gray-50 border rounded p-4">
                        <div class="flex items-center gap-4">
                            <img src="images/<?= htmlspecialchars($order['image']) ?>" class="w-16 h-16 rounded object-cover" alt="<?= htmlspecialchars($order['name']) ?>">
                            <div>
                                <h3 class="text-lg font-semibold"><?= htmlspecialchars($order['name']) ?></h3>
                                <p class="text-sm text-gray-600">Qty: <?= $order['quantity'] ?> | ₹<?= $order['total_price'] ?></p>
                                <p class="text-sm text-gray-500">Ordered on <?= date('d M Y', strtotime($order['order_date'])) ?></p>
                                <p class="text-sm font-semibold <?= $order['status'] === 'Cancelled' ? 'text-red-500' : 'text-green-600' ?>">Status: <?= $order['status'] ?></p>
                            </div>
                        </div>

                        <?php if ($order['status'] === 'Confirmed'): ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" name="cancel_order" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">You have no orders yet. <a href="home.php" class="text-green-600 underline">Start shopping</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>
