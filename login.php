<?php
session_start();
include 'db.php'; // Include your database connection

$error = ""; // Variable to store error message

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and get input values
    $email = trim($conn->real_escape_string($_POST["email"]));
    $password = $_POST["password"];

    // Query to find user by email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password with the hashed password
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];

            // Redirect to the home page after successful login
            header("Location: home.php");
            exit;
        } else {
            $error = "Invalid credentials."; // Invalid password
        }
    } else {
        $error = "User not found."; // Invalid email
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login - MedicineStore</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('https://png.pngtree.com/thumb_back/fh260/background/20240403/pngtree-assorted-pharmaceutical-medicine-pills-tablets-and-capsules-over-blue-background-image_15647957.jpg');">

  <form method="POST" class="bg-grey-900 p-8 rounded shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-4 text-blue-600">Login </h2>

    <!-- Display error if any -->
    <?php if ($error): ?>
      <p class="text-red-500 text-sm mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Input fields for email and password -->
    <input name="email" type="email" placeholder="Email" required
           class="w-full mb-3 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

    <input name="password" type="password" placeholder="Password" required
           class="w-full mb-4 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-200">
      Login
    </button>

    <p class="text-sm mt-4 text-center">
      Don't have an account? 
      <a href="signup.php" class="text-green-600 hover:underline">Sign up</a><br>
      <a href="admin_login.php" class="text-blue-600 hover:underline">Login as Admin</a>
    </p>
  </form>

</body>
</html>
