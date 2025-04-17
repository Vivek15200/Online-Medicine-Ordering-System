<?php
session_start();
include 'db.php';

// Admin authentication check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$status_message = '';

// Handle status update securely
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        $status_message = "Order status updated successfully!";
    } else {
        $status_message = "Failed to update order status.";
    }
    $stmt->close();
}

// Fetch all orders using LEFT JOIN to include user details
$orders = $conn->query("SELECT o.id, o.quantity, o.total_price, o.status, o.order_date,
           p.name AS product_name, p.image,
           u.name AS user_name, u.email AS user_email
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC");

// Fetch total turnover (total sales)
$turnover_result = $conn->query("SELECT SUM(total_price) AS total_sales FROM orders");
$turnover_row = $turnover_result->fetch_assoc();
$total_sales = $turnover_row['total_sales'] ?? 0;

// Debug if query fails
if (!$orders) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - MedicineStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-6 min-h-screen">

<!-- Navigation Bar -->
<nav class="bg-white text-black p-4">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="text-xl font-bold text-green-700 flex items-center space-x-2">
            <i class="fas fa-pills w-6 h-6 text-green-600"></i>
            <span>MedicineStore</span>
        </div>
        <ul class="flex space-x-6">
            
            <li><a href="#orders" class="hover:text-gray-200">
            <li><a href="admin_dashboard.php" class="hover:text-green-600 transition-colors duration-300" title="Home"><i class="fas fa-home w-6 h-6"></i></a></li>

<li><a href="logout.php" class="hover:text-gray-200">
                <i class="fas fa-sign-out-alt w-6 h-6 text-red-500 hover:text-red-600"></i> Logout
            </a></li>

        </ul>
    </div>
</nav>

<div class="max-w-6xl mx-auto bg-white shadow p-6 rounded-lg mt-6">

    <!-- Orders Section -->
    <h3 class="text-xl font-semibold mb-4">All User Orders</h3>

    <?php if (!empty($status_message)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <?= htmlspecialchars($status_message) ?>
        </div>
    <?php endif; ?>

    <?php if ($orders->num_rows > 0): ?>
        <table class="w-full table-auto border-collapse">
            <thead class="bg-green-100 text-left">
                <tr>
                    <th class="p-2">User</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Product</th>
                    <th class="p-2">Qty</th>
                    <th class="p-2">Total</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr class="border-t">
                        <td class="p-2"><?= htmlspecialchars($order['user_name']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($order['user_email']) ?></td>
                        <td class="p-2 flex items-center gap-3">
                            <?php if (!empty($order['image'])): ?>
                                <img src="images/<?= htmlspecialchars($order['image']) ?>" class="w-10 h-10 rounded object-cover" alt="">
                            <?php endif; ?>
                            <?= htmlspecialchars($order['product_name'] ?? 'Unknown Product') ?>
                        </td>
                        <td class="p-2"><?= $order['quantity'] ?></td>
                        <td class="p-2">₹<?= number_format($order['total_price'], 2) ?></td>
                        <td class="p-2 font-medium <?= $order['status'] === 'Cancelled' ? 'text-red-500' : ($order['status'] === 'Delivered' ? 'text-blue-600' : 'text-green-600') ?>">
                            <?= $order['status'] ?>
                        </td>
                        <td class="p-2 text-sm"><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                        <td class="p-2">
                            <form method="POST" class="flex items-center space-x-2">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="border p-1 rounded">
                                    <option value="Confirmed" <?= $order['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600">No orders found.</p>
    <?php endif; ?>
</div>

</body>
</html>
