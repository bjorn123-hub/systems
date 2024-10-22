<?php

class Sales {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

  
    public function getDeliveredSalesTotal() {
        $query = "SELECT SUM(total_amount) AS total_sales 
                  FROM sales 
                  WHERE status = 'Delivered'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;
    }
}
?>
