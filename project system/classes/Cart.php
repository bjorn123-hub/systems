<?php
class Cart {
    private $cartItems = [];
    private $db;
    private $total = 0;

    public function __construct($db) {
        $this->db = $db;
        $this->cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    // Add item to cart
    public function addItem($product_id, $quantity) {
        if (isset($this->cartItems[$product_id])) {
            $this->cartItems[$product_id]['quantity'] += $quantity;
        } else {
            $this->cartItems[$product_id] = ['quantity' => $quantity];
        }
        $_SESSION['cart'] = $this->cartItems;
    }

    // Get cart items with details
    public function getCartItems() {
        $items = [];
        $product = new Product($this->db);

        foreach ($this->cartItems as $product_id => $details) {
            $productData = $product->getProductById($product_id);

            if ($productData) {
                $items[$product_id] = [
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'quantity' => $details['quantity']
                ];
                $this->total += $productData['price'] * $details['quantity'];
            }
        }
        return $items;
    }

    // Get total price
    public function getTotal() {
        return $this->total;
    }
}
?>
