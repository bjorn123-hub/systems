<?php
session_start(); // Start the session

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/Database.php';
require_once '../classes/Category.php';
require_once '../classes/Product.php';
require_once '../classes/Order.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch counts using the respective classes
$salesCount = getCount($db, 'payment'); // Assuming payment tracks sales
$productCount = getCount($db, 'product');
$orderCount = getCount($db, 'order');

// Function to fetch the count from a given table
function getCount($db, $table) {
    $query = "SELECT COUNT(*) AS total_count FROM `$table`";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            margin-bottom: 20px;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            font-size: 24px;
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        a:hover {
            color: #0056b3;
        }
        .count {
            font-size: 48px;
            color: #333;
            margin-bottom: 10px;
        }
        .logout {
            text-align: right;
            margin-bottom: 20px;
        }
        .logout a {
            font-size: 18px;
            color: red;
            text-decoration: none;
        }
        .logout a:hover {
            color: darkred;
        }
    </style>
</head>
<body>

    <div class="logout">
        <a href="logout.php">Logout</a> <!-- Logout link -->
    </div>

    <h2>Admin Dashboard</h2>

    <div class="container">
        
        <!-- Manage Sales Box -->
        <div class="box">
            <div class="count"><?php echo $salesCount; ?></div>
            <a href="manage_sales.php">Manage Sales</a>
        </div>

        <!-- Manage Orders Box -->
        <div class="box">
            <div class="count"><?php echo $orderCount; ?></div>
            <a href="manage_orders.php">Manage Orders</a>
        </div>

        <!-- Manage Products Box -->
        <div class="box">
            <div class="count"><?php echo $productCount; ?></div>
            <a href="viewproduct.php">Manage Products</a>
        </div>



</body>
</html>
