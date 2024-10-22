<?php
class Category {
    private $conn;
    private $table_name = "category";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to add a new category
    public function addCategory($name, $admin_id) {
        $query = "INSERT INTO " . $this->table_name . " (name, admin_id) VALUES (:name, :admin_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':admin_id', $admin_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Method to fetch all categories
    public function getCategories() {
        $query = "SELECT category_id, name FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
