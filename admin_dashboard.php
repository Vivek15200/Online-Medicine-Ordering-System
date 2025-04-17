<?php
session_start();
include 'db.php';

// Admin authentication check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$status_message = '';

// Handle order status update
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

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = floatval($_POST['price']);
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = 'images/';

    if (!empty($product_name) && $price > 0 && !empty($image_name)) {
        move_uploaded_file($image_tmp, $upload_dir . $image_name);
        $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $product_name, $price, $image_name);
        if ($stmt->execute()) {
            $status_message = "Product added successfully!";
        } else {
            $status_message = "Failed to add product.";
        }
        $stmt->close();
    } else {
        $status_message = "Please fill in all fields.";
    }
}

// Handle delete product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);

    // Check if product is used in any orders
    $check = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE product_id = ?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result()->fetch_assoc();
    $check->close();

    if ($result['total'] > 0) {
        $status_message = "Cannot delete. Product is associated with orders.";
    } else {
        $delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete->bind_param("i", $product_id);
        if ($delete->execute()) {
            $status_message = "Product deleted successfully!";
        } else {
            $status_message = "Failed to delete product.";
        }
        $delete->close();
    }
}

// Total sales
$turnover_result = $conn->query("SELECT SUM(total_price) AS total_sales FROM orders");
$turnover_row = $turnover_result->fetch_assoc();
$total_sales = $turnover_row['total_sales'] ?? 0;

// Fetch all products
$product_result = $conn->query("SELECT * FROM products");

if (!$turnover_result || !$product_result) {
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

<!-- Navigation -->
<nav class="bg-white text-black p-4">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="text-xl font-bold text-green-700 flex items-center space-x-2">
            <i class="fas fa-pills w-6 h-6 text-green-600"></i>
            <span>MedicineStore</span>
        </div>
        <ul class="flex space-x-6">
            <li><a href="order.php" class="hover:text-gray-200">
                <i class="fas fa-shopping-cart w-6 h-6 text-green-500 hover:text-red-600"></i> Orders
            </a></li>
            <li><a href="logout.php" class="hover:text-gray-200">
                <i class="fas fa-sign-out-alt w-6 h-6 text-red-500 hover:text-red-600"></i> Logout
            </a></li>
        </ul>
    </div>
</nav>

<!-- Main Content -->
<div class="max-w-6xl mx-auto bg-white shadow p-6 rounded-lg mt-6">

    <!-- Dashboard Title -->
    <h2 class="text-3xl font-bold text-green-700 text-center mb-6">Admin Dashboard</h2>

    <!-- Turnover -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold">Turnover</h3>
        <p class="text-2xl text-green-600">₹<?= number_format($total_sales, 2) ?></p>
    </div>

    <!-- Add Product Section -->
    <h3 class="text-xl font-semibold mb-4 mt-6">Add New Product</h3>

    <?php if (!empty($status_message)): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?= htmlspecialchars($status_message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="product_name" placeholder="Product Name" class="border p-2 rounded" required>
        <input type="number" step="0.01" name="price" placeholder="Price" class="border p-2 rounded" required>
        <input type="file" name="image" accept="image/*" class="border p-2 rounded" required>
        <button type="submit" name="add_product" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Product</button>
    </form>

    <!-- Product List -->
    <h3 class="text-xl font-semibold mb-4 mt-8">All Products</h3>
    <table class="w-full table-auto border-collapse">
        <thead class="bg-green-100 text-left">
            <tr>
                <th class="p-2">Image</th>
                <th class="p-2">Name</th>
                <th class="p-2">Price</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $product_result->fetch_assoc()): ?>
                <tr class="border-t">
                    <td class="p-2">
                        <?php if (!empty($product['image'])): ?>
                            <img src="images/<?= htmlspecialchars($product['image']) ?>" class="w-12 h-12 object-cover rounded" alt="">
                        <?php endif; ?>
                    </td>
                    <td class="p-2"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="p-2">₹<?= number_format($product['price'], 2) ?></td>
                    <td class="p-2">
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" name="delete_product" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
