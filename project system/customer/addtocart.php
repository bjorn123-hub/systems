<?php
session_start();

// Initialize total amount
$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
</head>
<body>
    <h1>Your Cart</h1>

    <?php if (!empty($_SESSION['cart'])): ?>
        <ul>
            <?php foreach ($_SESSION['cart'] as $product_id => $quantity): ?>
                <li>Product ID: <?php echo htmlspecialchars($product_id); ?>, Quantity: <?php echo htmlspecialchars($quantity); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="checkout.php">Proceto Checkout</a>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <a href="product.php">Return to Products</a>
</body>
</html>
