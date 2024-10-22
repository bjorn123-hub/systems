<?php
session_start(); // Start the session

require_once '../config/Database.php';
require_once '../classes/Admin.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate the Admin class
$admin = new Admin($db);

// Handle the login process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Attempt to login
    $admin_data = $admin->login($username, $password);

    if ($admin_data) {
        // Admin authenticated, store session data
        $_SESSION['admin_id'] = $admin_data['admin_id'];
        $_SESSION['username'] = $admin_data['username'];
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        $error_message = "Invalid credentials!"; // Error message for invalid login
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" action="">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>
    </div>
</body>
</html>
