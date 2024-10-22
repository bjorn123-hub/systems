<?php  

session_start();

// Include the database connection
require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Initialize total and items
$total = 0; 
$items = []; 

// Check if the cart session exists
if (isset($_SESSION['cart'])) {
    $items = $_SESSION['cart']; // Get the cart items from the session

    // Prepare a query to fetch product details
    $product_ids = implode(',', array_map('intval', array_keys($items))); // Get product IDs safely

    // Check if there are any product IDs to query
    if (!empty($product_ids)) {
        $query = "SELECT product_id, name, price FROM product WHERE product_id IN ($product_ids)";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create an associative array for product details
        $product_details = [];
        foreach ($products as $product) {
            $product_details[$product['product_id']] = $product;
        }

        // Calculate total
        foreach ($items as $product_id => $details) {
            if (isset($product_details[$product_id])) {
                $total += $product_details[$product_id]['price'] * $details['quantity']; // Update total
            }
        }
    } else {
        // Handle the case when there are no valid product IDs
        echo "<div class='empty-cart'>No products found in the cart.</div>";
    }
}

// Handle removal of items from the cart
if (isset($_POST['remove_product'])) {
    $remove_id = intval($_POST['remove_product']);
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]); // Remove the product from the session
        header('Location: checkout.php');
        exit;
    }
}

// Handle quantity update
if (isset($_POST['update_product'])) {
    $update_id = intval($_POST['update_product']);
    $new_quantity = intval($_POST['quantity']);
    if (isset($_SESSION['cart'][$update_id])) {
        if ($new_quantity > 0) {
            $_SESSION['cart'][$update_id]['quantity'] = $new_quantity; // Update quantity in the session
        } else {
            unset($_SESSION['cart'][$update_id]); // Remove if quantity is 0
        }
        header('Location: checkout.php');
        exit;
    }
}

// Handle adding product to cart
if (isset($_POST['add_product'])) {
    $product_id = intval($_POST['product_id']); // Assume you get this from the form
    $quantity_to_add = 1; // Change this to however many you want to add

    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = ['quantity' => $quantity_to_add];
    } else {
        // Increase the quantity if it already exists
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    }
    header('Location: checkout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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

        form {
            display: inline;
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

        .remove-button {
            background-color: #f44336; /* Red */
            margin-left: 10px;
        }

        .empty-cart {
            text-align: center;
            margin-top: 20px;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
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
    </style>
</head>
<body>

<h2>Checkout</h2>

<?php if (empty($items)): ?>
    <div class="empty-cart">
        <p>Your cart is empty. Please add items to your cart before checking out.</p>
        <a href="cart.php">Return to Cart</a> <!-- Added return button -->
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $product_id => $details): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product_id); ?></td>
                    <td><?php echo htmlspecialchars($product_details[$product_id]['name']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="update_product" value="<?php echo htmlspecialchars($product_id); ?>">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($details['quantity']); ?>" min="0">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td><?php echo "PHP " . number_format($product_details[$product_id]['price'], 2); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="remove_product" value="<?php echo htmlspecialchars($product_id); ?>">
                            <button type="submit" class="remove-button" onclick="return confirm('Are you sure you want to remove this item?');">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total: PHP <?php echo number_format($total, 2); ?></h3>
    <form method="POST" action="process_checkout.php" style="display: inline;">
        <button type="submit">Confirm Order</button>
    </form>
    <form method="GET" action="cart.php" style="display: inline;">
        <button type="submit" class="cancel-button">Cancel</button>
    </form>
<?php endif; ?>
