<?php
session_start();

require_once '../config/Database.php';
require_once '../classes/Product.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch all categories
$category_stmt = $db->query("SELECT * FROM category");

// Initialize Product class
$product = new Product($db);

// Check if category is selected
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Fetch products based on the selected category (if any)
if ($category_id) {
    $stmt = $db->prepare("SELECT * FROM product WHERE category_id = :category_id");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
} else {
    // Fetch all products if "All Products" is selected or no category is selected
    $stmt = $product->getAllProducts();
}
$stmt->execute();

// Initialize success message
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        // Initialize or update the cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = []; // Initialize cart if it doesn't exist
        }
        
        // Add product to cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Update quantity if already in cart
        } else {
            $_SESSION['cart'][$product_id] = [
                'quantity' => $quantity,
            ];
        }
        
        // Set success message
        $_SESSION['success_message'] = 'Item successfully added to cart';
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit;
    }
}
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
            background: url('final.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            filter: blur(8px);
            z-index: -1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-link, .login-link {
            font-size: 18px;
            color: #000000;
            text-decoration: none;
            margin-left: 20px;
        }

        .categories {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .category-card {
            text-align: center;
            background-color: rgba(241, 241, 241, 0.8);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            width: 150px;
            transition: transform 0.3s, background-color 0.3s;
            cursor: pointer;
        }

        .category-card:hover {
            transform: scale(1.1);
            background-color: #000000;
            color: white;
        }

        .category-card p {
            font-size: 16px;
            margin: 0;
            color: inherit;
        }

        .category-card a {
            text-decoration: none;
            color: inherit;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .product-card {
            background: rgba(255, 255, 255, 0.9);
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
            color: #000000;
        }

        p {
            margin: 5px 0;
        }

        .quantity {
            margin: 10px 0;
        }

        button {
            background-color: #000000;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #333333;
        }

        .success-message {
            color: #000000;
            text-align: center;
            margin-bottom: 20px;
        }

        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>

    <!-- Smooth category click without reload -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryCards = document.querySelectorAll('.category-card a');
            categoryCards.forEach(card => {
                card.addEventListener('click', function (event) {
                    event.preventDefault();
                    const categoryUrl = this.getAttribute('href');
                    fetch(categoryUrl)
                        .then(response => response.text())
                        .then(html => {
                            document.body.innerHTML = html;
                            window.history.pushState({}, '', categoryUrl);
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</head>
<body>

<div class="header">
    <div>
        <img src="new.png" alt="Logo" style="height: 125px;"> <!-- Adjust logo size here -->
    </div>
    <div>
        <a href="cart.php" class="cart-link">Cart</a>
        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="order_history.php" class="cart-link">Order History</a> <!-- Order History link -->
            <a href="logout.php" class="login-link">Logout</a>
        <?php else: ?>
            <a href="login.php" class="login-link">Login</a>
        <?php endif; ?>
    </div>
</div>

<!-- Categories Section -->
<div class="categories">
    <div class="category-card">
        <a href="index.php"> <!-- "All Products" option -->
            <p>All Products</p>
        </a>
    </div>
    <?php while ($row = $category_stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="category-card">
            <a href="index.php?category_id=<?php echo $row['category_id']; ?>">
                <p><?php echo htmlspecialchars($row['name']); ?></p>
            </a>
        </div>
    <?php endwhile; ?>
</div>

<!-- Success message -->
<?php if ($successMessage): ?>
    <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
<?php endif; ?> 

<!-- Product Listing -->
<div class="product-container">
    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="product-card">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <!-- Display Product Image -->
            <?php if (!empty($row['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <?php else: ?>
                <p>No image available</p> <!-- Neutral fallback if no image is available -->
            <?php endif; ?>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <p>Price: PHP <?php echo number_format($row['price'], 2); ?></p>
            <form method="POST" action="">
                <div class="quantity">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" min="1" value="1" required>
                </div>
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
