<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ---- Handle quantity update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    $conn->query("UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND product_id = $product_id");
}

// ---- Handle item removal ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    $conn->query("DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
}

// ---- Fetch cart items ----
$cart_query = "
    SELECT c.product_id, c.quantity, p.name, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
";
$cart_items = $conn->query($cart_query);

// ---- Fetch previous orders ----
$order_query = "
    SELECT o.id, o.product_id, o.quantity, p.name, p.price, p.image, o.status, o.order_date
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.user_id = $user_id
    ORDER BY o.order_date DESC
";
$orders = $conn->query($order_query);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<head>
  <meta charset="UTF-8">
  <title>MedicineStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- FontAwesome CDN for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen p-6">
 
<!-- Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <!-- Logo and Store Name -->
    <div class="text-xl font-bold text-green-600 flex items-center space-x-2">
      <i class="fas fa-pills w-6 h-6 text-green-600"></i> <!-- Changed to FontAwesome Icon -->
      <span>MedicineStore</span>
    </div>

    <!-- Navbar Links -->
    <ul class="flex space-x-6 text-gray-700 items-center">
      <!-- Home -->
      <li><a href="home.php" class="hover:text-green-600 transition-colors duration-300" title="Home"><i class="fas fa-home w-6 h-6"></i></a></li>

      <!-- User Info -->
      <li><button id="userBtn" class="hover:text-green-600 transition-colors duration-300" title="User Info"><i class="fas fa-user w-6 h-6"></i></button></li>


      <!-- Cart -->
      <li><a href="cart.php" class="hover:text-green-600 transition-colors duration-300" title="Cart"><i class="fas fa-shopping-cart w-6 h-6"></i></a></li>

      <!-- Logout -->
      <li><a href="logout.php" class="hover:text-red-600 transition-colors duration-300" title="Logout"><i class="fas fa-sign-out-alt w-6 h-6 text-red-500"></i></a></li>
    </ul>
  </div>
</nav>

<br>
<br>
<div class="max-w-4xl mx-auto bg-white shadow p-6 rounded">
    <!-- Cart Section -->
    <h2 class="text-2xl font-bold mb-6 text-green-700">Your Shopping Cart</h2>

    <?php if ($cart_items->num_rows > 0): ?>
      <table class="w-full border-collapse mb-6">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-2">Product</th>
            <th class="p-2">Price</th>
            <th class="p-2">Quantity</th>
            <th class="p-2">Total</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $cart_items->fetch_assoc()): 
            $item_total = $row['price'] * $row['quantity'];
            $total += $item_total;
          ?>
          <tr class="border-t">
            <td class="p-2 flex items-center gap-3">
              <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="w-12 h-12 object-cover rounded">
              <?= htmlspecialchars($row['name']) ?>
            </td>
            <td class="p-2">₹<?= $row['price'] ?></td>
            <td class="p-2">
              <form method="POST" class="flex items-center">
                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="1" class="w-16 border p-1 text-center rounded">
                <button type="submit" name="update_quantity" class="ml-2 text-blue-600 hover:underline">Update</button>
              </form>
            </td>
            <td class="p-2">₹<?= $item_total ?></td>
            <td class="p-2">
              <form method="POST">
                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                <button type="submit" name="remove_item" class="text-red-600 hover:underline">Remove</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="text-right text-xl font-semibold text-green-800">
        Grand Total: ₹<?= $total ?>
      </div>
      <div class="text-right mt-4">
        <a href="checkout.php" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Proceed to Checkout</a>
      </div>

    <?php else: ?>
      <p class="text-gray-600">Your cart is empty. <a href="home.php" class="text-green-600 underline">Continue shopping</a>.</p>
    <?php endif; ?>
  </div>

  <!-- Orders Section -->
  <div class="max-w-4xl mx-auto bg-white shadow p-6 rounded mt-8">
    <h2 class="text-2xl font-bold mb-6 text-green-700">Your Previous Orders</h2>

    <?php if ($orders->num_rows > 0): ?>
      <table class="w-full border-collapse mb-6">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-2">Product</th>
            <th class="p-2">Price</th>
            <th class="p-2">Quantity</th>
            <th class="p-2">Total</th>
            <th class="p-2">Status</th>
            <th class="p-2">Order Date</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = $orders->fetch_assoc()):
            $order_total = $order['price'] * $order['quantity'];
          ?>
          <tr class="border-t">
            <td class="p-2 flex items-center gap-3">
              <img src="images/<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($order['name']) ?>" class="w-12 h-12 object-cover rounded">
              <?= htmlspecialchars($order['name']) ?>
            </td>
            <td class="p-2">₹<?= $order['price'] ?></td>
            <td class="p-2"><?= $order['quantity'] ?></td>
            <td class="p-2">₹<?= $order_total ?></td>
            <td class="p-2">
              <span class="font-semibold <?= $order['status'] === 'Delivered' ? 'text-blue-600' : ($order['status'] === 'Cancelled' ? 'text-red-600' : 'text-yellow-600') ?>">
                <?= htmlspecialchars($order['status']) ?>
              </span>
            </td>
            <td class="p-2"><?= date('d M Y', strtotime($order['order_date'])) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-gray-600">You have no previous orders.</p>
    <?php endif; ?>
  </div>

</body>
</html>
