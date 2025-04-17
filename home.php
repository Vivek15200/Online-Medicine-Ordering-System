<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET name='$name', email='$email', address='$address', password='$password' WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users SET name='$name', email='$email', address='$address' WHERE id=$user_id");
    }

    $_SESSION['name'] = $name;
    header("Location: home.php");
    exit;
}

// Fetch user data
$result = $conn->query("SELECT name, email, address FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

// Handle filter, sort, and search
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$query = "SELECT * FROM products";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " WHERE name LIKE '%$search%'";
}

if ($sort === 'price') {
    $query .= " ORDER BY price ASC";
} elseif ($sort === 'name') {
    $query .= " ORDER BY name ASC";
}

$product_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home - Online Medicine Ordering System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <style>
    html {
      scroll-behavior: smooth;
    }
    .slider img {
      transition: opacity 1s ease-in-out;
      object-fit: cover;
      width: 100%;
      height: 100%;
      max-width: 100%;
      max-height: 100%;
    }
    .product-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen scroll-smooth">

<!-- Navbar -->
<nav class="bg-white shadow-md fixed w-full top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <!-- Logo and Store Name -->
    <div class="text-xl font-bold text-green-600 flex items-center space-x-2">
      <i class="fas fa-pills w-6 h-6 text-green-600"></i> <!-- Changed to FontAwesome Icon -->
      <span>MedicineStore</span>
    </div>
    <ul class="flex space-x-6 text-gray-700 items-center">
      <!-- Home -->
      <li><a href="home.php" class="hover:text-green-600 transition-colors duration-300" title="Home"><i class="fas fa-home w-6 h-6"></i></a></li>

      <!-- User Info -->
      <li><button id="userBtn" class="hover:text-green-600 transition-colors duration-300" title="User Info"><i class="fas fa-user w-6 h-6"></i></button></li>

      <!-- Products Section -->
      <li><a href="#productsSection" class="hover:text-green-600 transition-colors duration-300" title="Products"><i class="fas fa-capsules w-6 h-6"></i></a></li>

      <!-- Cart -->
      <li><a href="cart.php" class="hover:text-green-600 transition-colors duration-300" title="Cart"><i class="fas fa-shopping-cart w-6 h-6"></i></a></li>

      <!-- Logout -->
      <li><a href="logout.php" class="hover:text-red-600 transition-colors duration-300" title="Logout"><i class="fas fa-sign-out-alt w-6 h-6 text-red-500"></i></a></li>
    </ul>
  </div>
</nav>

<!-- Slider -->
<div class="relative w-full h-[80vh] pt-16 overflow-hidden">
  <div class="slider absolute inset-0 w-full h-full">
    <div class="slides w-full h-full relative">
      <img src="images/slider1.webp" class="absolute left-1/2 top-1/2 w-full h-full object-cover transform -translate-x-1/2 -translate-y-1/2 opacity-0 rounded-xl shadow-lg" />
      <img src="images/slider2.webp" class="absolute left-1/2 top-1/2 w-full h-full object-cover transform -translate-x-1/2 -translate-y-1/2 opacity-0 rounded-xl shadow-lg" />
      <img src="images/slider3.webp" class="absolute left-1/2 top-1/2 w-full h-full object-cover transform -translate-x-1/2 -translate-y-1/2 opacity-0 rounded-xl shadow-lg" />
    </div>
  </div>
</div>

<!-- Products Section -->
<section id="productsSection" class="max-w-6xl mx-auto mt-12 px-4">
  <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">Available Medicines</h2>

  <!-- Search and Sort -->
  <form class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6" method="GET">
    <input type="text" name="search" placeholder="Search medicines..." value="<?= htmlspecialchars($search) ?>" class="border p-2 rounded w-full md:w-1/2">
    <select name="sort" class="border p-2 rounded">
      <option value="">Sort By</option>
      <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
      <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>Price</option>
    </select>
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Apply</button>
  </form>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php while ($row = $product_result->fetch_assoc()): ?>
      <div class="bg-white p-4 rounded shadow hover:shadow-lg transition">
        <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-image mb-3">
        <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></h3>
        <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($row['description']) ?></p>
        <p class="text-blue-600 font-bold mb-2">₹<?= $row['price'] ?></p>
        <?php if ($row['stock'] > 0): ?>
          <form method="POST" action="add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Add to Cart</button>
          </form>
        <?php else: ?>
          <p class="text-red-500 font-semibold text-center mt-2">Out of Stock</p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
    <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-red-500">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <h2 class="text-xl font-bold mb-4 text-green-700 text-center">Your Profile Info</h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="update_profile" value="1">
      <div>
        <label class="block text-sm mb-1 font-medium">Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border p-2 rounded">
      </div>
      <div>
        <label class="block text-sm mb-1 font-medium">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full border p-2 rounded">
      </div>
      <div>
        <label class="block text-sm mb-1 font-medium">Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required class="w-full border p-2 rounded">
      </div>
      <div>
        <label class="block text-sm mb-1 font-medium">New Password</label>
        <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full border p-2 rounded">
      </div>
      <div class="text-center">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Info</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script>
  lucide.createIcons();

  const userBtn = document.getElementById('userBtn');
  const userModal = document.getElementById('userModal');
  const closeModal = document.getElementById('closeModal');

  userBtn.addEventListener('click', () => userModal.classList.remove('hidden'));
  closeModal.addEventListener('click', () => userModal.classList.add('hidden'));

  const slides = document.querySelectorAll(".slides img");
  let current = 0;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.add("opacity-0");
      slide.classList.remove("opacity-100");
      if (i === index) {
        slide.classList.remove("opacity-0");
        slide.classList.add("opacity-100");
      }
    });
  }

  function nextSlide() {
    current = (current + 1) % slides.length;
    showSlide(current);
  }

  showSlide(current);
  setInterval(nextSlide, 4000);
</script>

</body>
</html>
