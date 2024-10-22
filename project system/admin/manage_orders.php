<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

// Handle order deletion
if (isset($_GET['delete_order_id'])) {
    $order_id = $_GET['delete_order_id'];
    if ($order->deleteOrder($order_id)) {
        echo "<p style='color: green;'>Order deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting order.</p>";
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Validate inputs
    if (!in_array($status, ['To Deliver', 'Delivered', 'Cancel'])) {
        echo "<p style='color: red;'>Invalid status provided.</p>";
    } else {
        if ($order->updateOrderStatus($order_id, $status)) {
            echo "<p style='color: green;'>Order status updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating order status.</p>";
        }
    }
}

// Fetch all orders
$orders = $order->getAllOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
    </style>
</head>
<body>
    <button class="return-button" onclick="window.location.href='dashboard.php'">Return to Dashboard</button>

    <h2>Manage Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Total Amount</th>
            <th>Action</th>
            <th>Update Status</th>
        </tr>
        <?php while ($row = $orders->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                <td>
                    <a href="?delete_order_id=<?php echo $row['order_id']; ?>" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <select name="status">
                            <option value="To Deliver" <?php echo ($row['status'] == 'To Deliver') ? 'selected' : ''; ?>>To Deliver</option>
                            <option value="Delivered" <?php echo ($row['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancel" <?php echo ($row['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancel</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
