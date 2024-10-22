<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Order.php';
require_once '../classes/OrderHistory.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Order class
$order = new Order($db);

// Create an instance of OrderHistory
$orderHistory = new OrderHistory($order, $customer_id);

// Handle order cancellation
$message = $orderHistory->handleCancellation();

// Fetch orders for the logged-in customer
$orders = $orderHistory->fetchOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .cancel-button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
        .cancel-button:hover {
            background-color: #d32f2f;
        }
        .return-button {
            margin: 20px 0;
            padding: 10px 15px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .return-button:hover {
            background-color: #005f73;
        }
    </style>
</head>
<body>

    <button class="return-button" onclick="window.location.href='index.php'">Return to Home</button>

    <h2>Order History</h2>
    
    <?php echo $message; // Display the cancellation message ?>

    <?php echo $orderHistory->displayOrders($orders); ?>

</body>
</html>
