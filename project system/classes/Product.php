<?php
class Product {
    private $conn;  // This is the database connection property
    private $table_name = "product";  // Name of the product table

    // Constructor to initialize the database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to add a product with an image
    public function addProduct($name, $description, $price, $category_id, $imagePath) {
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image_path) VALUES (:name, :description, :price, :category_id, :image_path)";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $this->bindProductParams($stmt, $name, $description, $price, $category_id, $imagePath);

        return $stmt->execute();
    }

    // Method to get all products
    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Method to delete a product by product_id
    public function deleteProduct($product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind the product_id parameter
        $stmt->bindParam(':product_id', $product_id);

        return $stmt->execute();
    }

    // Method to fetch product by ID
    public function getProductById($product_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to update a product
    public function editProduct($product_id, $name, $description, $imagePath) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, image_path = :image_path WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image_path', $imagePath);

        return $stmt->execute();
    }

    // Helper method to bind parameters for adding/updating a product
    private function bindProductParams($stmt, $name, $description, $price, $category_id, $imagePath) {
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image_path', $imagePath);
    }

    // Method to handle image upload
    public function uploadImage($file) {
        $target_dir = "uploads/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (optional)
        if ($file["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // If everything is ok, try to upload the file
        if ($uploadOk == 1) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file; // Return the path of the uploaded file
            } else {
                echo "Sorry, there was an error uploading your file.";
                return null;
            }
        }
        return null;
    }
}
?>
