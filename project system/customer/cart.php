<?php
session_start();

// Include necessary files
require_once '../config/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Product.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize cart
$cart = new Cart($db);

// Get cart items and total
$items = $cart->getCartItems();
$total = $cart->getTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background: url('final.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.5); /* Slight white overlay */
            filter: blur(8px);
            z-index: -1;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        h3 {
            margin-top: 20px;
            text-align: right;
            color: #333;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
            text-align: center;
        }

        a:hover {
            background-color: #45a049;
        }

        .empty-cart {
            text-align: center;
            margin-top: 20px;
        } 
    </style>
</head>
<body>

<h2>Your Shopping Cart</h2>

<?php if (empty($items)): ?>
    <div class="empty-cart">
        <p>Your cart is empty. Please add items to your cart.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $product_id => $details): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product_id); ?></td>
                    <td><?php echo htmlspecialchars($details['name']); ?></td>
                    <td><?php echo htmlspecialchars($details['quantity']); ?></td>
                    <td><?php echo "PHP " . number_format($details['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total: PHP <?php echo number_format($total, 2); ?></h3>
<?php endif; ?>

<a href="checkout.php">Proceed to Checkout</a>
<a href="index.php" style="margin-left: 10px;">Return to Browsing</a>

</body>
</html>
