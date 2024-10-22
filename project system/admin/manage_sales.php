<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Sales.php';
require_once '../classes/Order.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Sales and Order classes
$sales = new Sales($db);
$order = new Order($db);

// Handle order deletion (which will also affect sales)
if (isset($_GET['delete_order_id'])) {
    $order_id = $_GET['delete_order_id'];
    if ($order->deleteOrder($order_id)) {
        echo "<p style='color: green;'>Order deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting order.</p>";
    }
}

// Fetch all orders (which will be considered part of sales)
$orders = $order->getAllOrders();

// Initialize total sales amount
$totalSales = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales</title>
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
        .total-sales {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            font-size: 24px;
        }
    </style>
</head>
<body>

<h2>Sales Overview</h2>

<table>
    <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Order Date</th>
        <th>Status</th>
        <th>Total Amount</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>PHP <?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
            <td>
                <a href="?delete_order_id=<?php echo $row['order_id']; ?>" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
            </td>
        </tr>
        <?php $totalSales += $row['total_amount']; // Add to total sales ?>
    <?php endwhile; ?>
</table>

<!-- Display total sales amount -->
<div class="total-sales">
    Total Sales: PHP <?php echo number_format($totalSales, 2); ?>
</div>

<!-- Return Button -->
<a href="dashboard.php" class="return-button">Return to Dashboard</a>

</body>
</html>
