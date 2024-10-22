<?php
// cancel_order.php
session_start();
require_once 'config/Database.php';
require_once 'class/Order.php';

if (!isset($_SESSION['customer_id']) || !isset($_POST['order_id'])) {
    echo "Invalid request.";
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);

$order_id = $_POST['order_id'];
if ($order->cancelOrder($order_id)) {
    echo "Order canceled successfully.";
} else {
    echo "Failed to cancel the order.";
}

// Redirect back to the order history page
header("Location: order_history.php");
exit();
?>