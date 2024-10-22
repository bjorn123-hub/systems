<?php
session_start();
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Check if the email exists
    $query = "SELECT * FROM customer WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($customer && password_verify($password, $customer['password'])) {
        // Store customer information in session
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];
        header("Location: index.php"); // Redirect to the product page
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #333;

    background-image: url('new.png'); /* Replace with your image path */
    background-size: cover; /* or use 'contain' depending on your preference */
    background-repeat: no-repeat; /* Prevent the image from repeating */
    background-position: center; /* Center the image */
}

.login-container {
    background-color: rgba(101, 67, 33, 0.8); /* Dark brown with 80% opacity */
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); /* Slightly darker shadow for better contrast */
    width: 300px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: rgb(101, 67, 33); /* Dark brown color for the heading */
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

input[type="email"]:focus,
input[type="password"]:focus {
    border-color: rgb(12, 67, 33); /* Dark brown border color on focus */
    outline: none;
}

button {
    background-color: rgb(101, 67, 33); /* Dark brown background for button */
    color: white; /* White text for button */
    border: none;
    padding: 10px;
    cursor: pointer;
    border-radius: 4px;
    width: 100%;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #7b4b3a; /* Slightly lighter shade for hover effect */
}

p {
    margin-top: 20px;
    color: #666;
}

a {
    color: rgb(101, 67, 33); /* Dark brown color for links */
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error; ?></p>
        <?php endif; ?>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>
</body>
</html>
