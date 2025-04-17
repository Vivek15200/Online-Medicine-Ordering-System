<?php
session_start();
include 'db.php';  // Your database connection

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Prepared statement for security (to prevent SQL injection)
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);  // 's' stands for string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the result is valid
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin["password"])) {
            // Start session and store session variables
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["name"];
            $_SESSION["role"] = 'admin';  // Store role as 'admin'
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid admin credentials.";
        }
    } else {
        $error = "Admin not found.";
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - MedicineStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20240403/pngtree-assorted-pharmaceutical-medicine-pills-tablets-and-capsules-over-blue-background-image_15647957.jpg');">
  <form method="POST" class="bg-grey-600 p-8 rounded shadow-lg w-full max-w-md">
  <h2 class="text-2xl font-bold text-center mb-4 text-red-600">Admin Login</h2>

  <?php if ($error): ?>
    <p class="text-red-500 text-sm mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <input name="email" type="email" placeholder="Admin Email" required class="w-full mb-3 p-2 border rounded">
  <input name="password" type="password" placeholder="Password" required class="w-full mb-4 p-2 border rounded">

  <button class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition duration-200">Login</button>
  <p class="text-sm mt-4 text-center">
    <a href="login.php" class="text-blue-600 hover:underline">Login as User</a>
  </p>
</form>
</body>
</html>
