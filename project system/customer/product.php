<?php
session_start();

require_once '../config/Database.php';
require_once '../classes/Cart.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Cart
$cart = isset($_SESSION['cart']) ? unserialize($_SESSION['cart']) : new Cart();

// Check if a product has been added to the cart via POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Add product to cart
    $cart->addToCart($product_id, $quantity);
    $_SESSION['cart'] = serialize($cart); // Update session

    $message = "Item successfully added to cart!";
}

// Fetch all products
$product = new Product($db);
$stmt = $product->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .product-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        h3 {
            margin: 0 0 10px;
            color: #4CAF50;
        }
        p {
            margin: 5px 0;
        }
        .quantity {
            margin: 10px 0;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #45a049;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>Available Products</h2>

<?php if ($message): ?>
    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="product-container">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="product-card">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <p>Price: PHP <?php echo number_format($row['price'], 2); ?></p>
            <form method="POST" action="">
                <div class="quantity">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" min="1" value="1" required>
                </div>
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>

