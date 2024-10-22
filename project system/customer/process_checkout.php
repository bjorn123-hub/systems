<?php
session_start();

require_once '../config/Database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if customer_id is set in session and cart is not empty
if (!isset($_SESSION['customer_id']) || empty($_SESSION['cart'])) {
    // Display error message in a user-friendly way
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checkout Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 50px;
                text-align: center;
            }
            .message {
                background-color: #ffcccc;
                color: #d8000c;
                padding: 20px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            a {
                text-decoration: none;
                color: white;
                background-color: #007bff;
                padding: 10px 15px;
                border-radius: 5px;
            }
            a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="message">
            No customer ID or empty cart found. Please complete the checkout process.
        </div>
        <a href="index.php">Return Browsing</a>
    </body>
    </html>
    <?php
    exit();
}

$customer_id = $_SESSION['customer_id'];
$cart_items = $_SESSION['cart'];

// Begin transaction
$db->beginTransaction();

try {
    // Insert order into the orders table
    $query = "INSERT INTO `order` (customer_id, order_date, total_amount, status) VALUES (:customer_id, NOW(), 0, 'Pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();
    $order_id = $db->lastInsertId();

    // Initialize total amount
    $total_amount = 0;

    // Prepare insert statement for order items
    $order_item_query = "INSERT INTO order_item (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
    $order_item_stmt = $db->prepare($order_item_query);

    foreach ($cart_items as $product_id => $details) {
        $quantity = $details['quantity'];

        // Fetch product price
        $product_query = "SELECT price FROM product WHERE product_id = :product_id";
        $product_stmt = $db->prepare($product_query);
        $product_stmt->bindParam(':product_id', $product_id);
        $product_stmt->execute();
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $price = $product['price'];
            $total_amount += $price * $quantity;

            // Insert order item
            $order_item_stmt->bindParam(':order_id', $order_id);
            $order_item_stmt->bindParam(':product_id', $product_id);
            $order_item_stmt->bindParam(':quantity', $quantity);
            $order_item_stmt->bindParam(':price', $price);
            $order_item_stmt->execute();
        }
    }

    // Update the total amount in the orders table
    $update_order_query = "UPDATE `order` SET total_amount = :total_amount WHERE order_id = :order_id";
    $update_stmt = $db->prepare($update_order_query);
    $update_stmt->bindParam(':total_amount', $total_amount);
    $update_stmt->bindParam(':order_id', $order_id);
    $update_stmt->execute();

    // Commit transaction
    $db->commit();

    // Clear the cart session
    unset($_SESSION['cart']);

    // Redirect to order confirmation page
    header("Location: confirmation.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    echo "Failed to process the order: " . $e->getMessage();
    exit();
}
?>
