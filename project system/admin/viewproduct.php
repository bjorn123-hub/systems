<?php
session_start();

require_once '../config/Database.php';
require_once '../classes/Product.php';
require_once '../classes/Category.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$category = new Category($db);

// Ensure $admin_id is set from session
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;  // Default admin_id for testing

// Handle Add Category form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    if ($category->addCategory($category_name, $admin_id)) {
        echo "<p style='color: green;'>Category added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error adding category.</p>";
    }
}

// Handle Add Product form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_directory = "uploads/"; // Ensure this directory exists
        $target_file = $target_directory . basename($_FILES["image"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file; // Set the image path if upload is successful
        }
    }

    if ($product->addProduct($product_name, $description, $price, $category_id, $image_path)) {
        echo "<p style='color: green;'>Product added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error adding product.</p>";
    }
}

// Handle product deletion
if (isset($_GET['delete_product_id'])) {
    $product_id = $_GET['delete_product_id'];
    if ($product->deleteProduct($product_id)) {
        echo "<p style='color: green;'>Product deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error deleting product.</p>";
    }
}

// Fetch products
$products = $product->getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            margin: 20px; 
            background-color: #f4f4f4; 
        }
        h2, h3 { 
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
        input[type="text"], input[type="number"], textarea { 
            width: calc(100% - 22px); 
            padding: 10px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }
        button { 
            background-color: #4CAF50; 
            color: white; border: none; 
            padding: 10px 15px; 
            cursor: pointer;
            border-radius: 5px; 
            transition: background-color 0.2s; 
        }
        button:hover { 
            background-color: #45a049; 
        }
        .form-container {
             background: white; 
             padding: 20px; 
             border-radius: 8px; 
             box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); 
             margin-bottom: 20px; 
        }
        .error { 
            color: red; 
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

    <div class="form-container">
        <h3>Add Category</h3>
        <form method="POST">
            <input type="text" name="category_name" placeholder="Category Name" required>
            <button type="submit" name="add_category">Add Category</button>
        </form>
    </div>

    <div class="form-container">
        <h3>Add Product</h3>
        <form method="POST" enctype="multipart/form-data"> <!-- Add enctype for file uploads -->
            <input type="text" name="product_name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php
                // Fetch categories to populate the dropdown
                $categories = $category->getCategories();
                while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['category_id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <input type="file" name="image" accept="image/*" required> <!-- Image upload -->
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <h3>Your Products</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $products->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>PHP <?php echo number_format($row['price'], 2); ?></td>
                <td>
                    <a href="edit_product.php?product_id=<?php echo $row['product_id']; ?>">Edit</a> | 
                    <a href="?delete_product_id=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
