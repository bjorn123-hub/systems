<?php
class OrderHistory {
    private $order;
    private $customer_id;

    public function __construct($order, $customer_id) {
        $this->order = $order;
        $this->customer_id = $customer_id;
    }

    public function handleCancellation() {
        if (isset($_POST['cancel_order_id'])) {
            $cancel_order_id = $_POST['cancel_order_id'];
            
            if ($this->order->cancelOrder($cancel_order_id, $this->customer_id)) {
                return "<p style='color: green;'>Order canceled successfully! The admin will be notified.</p>";
            } else {
                return "<p style='color: red;'>Unable to cancel the order. It may already be delivered or canceled.</p>";
            }
        }
        return "";
    }

    public function fetchOrders() {
        return $this->order->getOrdersByCustomerId($this->customer_id);
    }

    public function displayOrders($orders) {
        $output = "<table>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Action</th>
            </tr>";
        
        while ($row = $orders->fetch(PDO::FETCH_ASSOC)) {
            $output .= "<tr>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . htmlspecialchars($row['order_date']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars(number_format($row['total_amount'], 2)) . "</td>
                <td>";

            // Allow cancellation only if the order status is 'Pending'
            if ($row['status'] == 'Pending') {
                $output .= "<form method='POST'>
                    <input type='hidden' name='cancel_order_id' value='" . $row['order_id'] . "'>
                    <button type='submit' class='cancel-button'>Cancel Order</button>
                </form>";
            } else {
                $output .= "<span>Not Eligible for Cancellation</span>";
            }
            
            $output .= "</td></tr>";
        }
        $output .= "</table>";
        return $output;
    }
}
