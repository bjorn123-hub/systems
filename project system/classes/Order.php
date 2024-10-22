<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to get all orders
    public function getAllOrders() {
        $query = "SELECT o.order_id, o.order_date, o.status, o.total_amount, c.first_name, c.last_name 
                  FROM `order` o 
                  JOIN customer c ON o.customer_id = c.customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Method to get orders by customer ID, excluding delivered orders
    public function getOrdersByCustomerId($customer_id) {
        $query = "SELECT order_id, order_date, status, total_amount 
                  FROM `order` 
                  WHERE customer_id = :customer_id AND status != 'Delivered'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Method to cancel an order
    public function cancelOrder($order_id, $customer_id) {
        // First check if the order can be cancelled
        $query = "SELECT status FROM `order` WHERE order_id = :order_id AND customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ensure order can be cancelled
        if ($row['status'] == 'Delivered' || $row['status'] == 'Cancelled' || $row['status'] == 'To Deliver') {
            return false;  // Cannot cancel delivered or already cancelled orders, or if marked as "To Deliver"
        }

        // Update the order status to 'Cancelled'
        $query = "UPDATE `order` SET status = 'Cancelled' WHERE order_id = :order_id AND customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        
        // Notify admin (You can implement your own notification mechanism)
        // For example, you could insert a record into an 'admin_notifications' table.

        return $stmt->execute();
    }

    // Method to delete an order
    public function deleteOrder($order_id) {
        $query = "DELETE FROM `order` WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Method to update order status
    public function updateOrderStatus($order_id, $status) {
        $query = "UPDATE `order` SET status = :status WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        
        // Execute the update and return the result
        return $stmt->execute();
    }
}
