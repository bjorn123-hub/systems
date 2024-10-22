    <?php
    session_start();

    require_once '../config/Database.php';
    require_once '../classes/Product.php';

    $database = new Database();
    $db = $database->getConnection();
    $product = new Product($db);

    // Ensure product_id is set from query string
    if (!isset($_GET['product_id'])) {
        header('Location: admin_dashboard.php'); // Redirect if no product ID is provided
        exit;
    }

    $product_id = intval($_GET['product_id']);

    // Fetch the product details
    $product_details = $product->getProductById($product_id);
    if (!$product_details) {
        echo "<p style='color: red;'>Product not found.</p>";
        exit;
    }

    // Handle the edit form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
        $name = $_POST['product_name'];
        $description = $_POST['description'];

        // Update the product
        if ($product->editProduct($product_id, $name, $description)) {
            echo "<p style='color: green;'>Product updated successfully!</p>";
            header('Location: viewproduct.php'); // Redirect after successful update
            exit;
        } else {
            echo "<p style='color: red;'>Error updating product. Please try again.</p>";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Product</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                background-color: #f4f4f4; 
            }
            .form-container { 
                background: white; 
                padding: 20px; 
                border-radius: 8px; 
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
                margin-bottom: 20px; 
            }
            input[type="text"], textarea { 
                width: calc(100% - 22px); 
                padding: 10px; 
                margin: 10px 0; 
                border: 1px solid #ccc; 
                border-radius: 5px; 
            }
            button { 
                background-color: #4CAF50; 
                color: white; 
                border: none; 
                padding: 10px 15px; 
                cursor: pointer; 
                border-radius: 5px; 
            }
            button:hover { 
                background-color: #45a049; 
            }
        </style>
    </head>
    <body>

    <div class="form-container">
        <h3>Edit Product</h3>
        <form method="POST">
            <input type="text" name="product_name" placeholder="Product Name" value="<?php echo htmlspecialchars($product_details['name']); ?>" required>
            <textarea name="description" placeholder="Description" required><?php echo htmlspecialchars($product_details['description']); ?></textarea>
            <button type="submit" name="edit_product">Update Product</button>
        </form>
    </div>

    </body>
    </html>
