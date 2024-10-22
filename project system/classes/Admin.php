<?php

require_once '../config/Database.php';
require_once '../classes/Admin.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $adminData = $admin->login($username, $password);
    if ($adminData) {
        // Set session variables
        $_SESSION['admin_id'] = $adminData['admin_id'];
        $_SESSION['username'] = $adminData['username'];
        header("Location: admin_dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        $error = "Invalid username or password.";
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
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f2f2f2; /* Light gray background */
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 300px; /* Fixed width for the container */
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333; /* Darker text color */
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3; /* Darker shade for hover effect */
        }
        .error {
            color: red;
            margin: 10px 0;
        }
    </style>
</head>




<?php
class Admin {
    private $conn;
    private $table = "admin";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Function to authenticate admin
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verify password (you should hash the password for production)
            if ($password === $row['password']) {  // Replace with password_verify if passwords are hashed
                return $row;  // Return the admin's data if successful  
            }
        }
        return false;
    }
}
?>