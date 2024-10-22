<?php
session_start();

require_once '../config/Database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if customer_id is set in session
if (!isset($_SESSION['customer_id'])) {
    echo "No customer ID found. Please complete the checkout process.";
    exit();
}

// Assuming customer details are stored in the session
$customer_id = $_SESSION['customer_id'];

// Fetch the latest order for the customer
$query = "SELECT * FROM `order` WHERE customer_id = :customer_id ORDER BY order_id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "No orders found.";
    exit();
}

// Fetch order items
$order_id = $order['order_id'];
$order_items_query = "SELECT oi.*, p.name AS product_name FROM order_item oi JOIN product p ON oi.product_id = p.product_id WHERE oi.order_id = :order_id";
$order_items_stmt = $db->prepare($order_items_query);
$order_items_stmt->bindParam(':order_id', $order_id);
$order_items_stmt->execute();
$order_items = $order_items_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        p {
            margin: 0 0 10px;
        }
        .return-button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .return-button:hover {
            background-color: #FF0000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Confirmation</h2>
    <p>Thank you for your order!</p>
    <p>Your Order ID: <strong><?php echo htmlspecialchars($order['order_id']); ?></strong></p>
    <p>Total Amount: <strong>PHP <?php echo number_format($order['total_amount'], 2); ?></strong></p>

    <h3>Order Items:</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($order_items) > 0): ?>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>PHP <?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No items found for this order.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Return to Browsing Button -->
    <a href="index.php" class="return-button">Return to Browsing</a>
</div>

</body>
</html>
