<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST["name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $address = $conn->real_escape_string($_POST["address"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, address, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $address, $password);
    $stmt->execute();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20240403/pngtree-assorted-pharmaceutical-medicine-pills-tablets-and-capsules-over-blue-background-image_15647957.jpg');">

<form action="" method="POST" class="bg-grey-700 p-6 rounded shadow w-full max-w-md">
  <h2 class="text-2xl font-bold text-center mb-4 text-green-600">Signup</h2>

  <input name="name" type="text" placeholder="Name" required class="w-full mb-3 p-2 border rounded">
  <input name="email" type="email" placeholder="Email" required class="w-full mb-3 p-2 border rounded">
  <input name="address" type="text" placeholder="Address" required class="w-full mb-3 p-2 border rounded">
  <input name="password" type="password" placeholder="Password" required class="w-full mb-4 p-2 border rounded">

  <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Register</button>
  <p class="text-sm mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
</form>

</body>
</html>
