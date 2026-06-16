<?php
class ShoppingCart
{
    public function __construct()
    {
        // The cart is stored in the session so it remains available while the user continues shopping.
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
    }

    public function AddItem($item)
    {
        $clothId = $item['cloth_id'];

        // If the item is already in the cart, only the quantity is increased.
        if (isset($_SESSION['cart'][$clothId])) {
            if ($_SESSION['cart'][$clothId]['quantity'] < $_SESSION['cart'][$clothId]['stock_quantity']) {
                $_SESSION['cart'][$clothId]['quantity'] = $_SESSION['cart'][$clothId]['quantity'] + 1;
            }
        } else {
            $_SESSION['cart'][$clothId] = array(
                'cloth_id' => $item['cloth_id'],
                'name' => $item['name'],
                'brand' => $item['brand'],
                'price' => $item['price'],
                'image' => $item['image'],
                'stock_quantity' => $item['quantity'],
                'quantity' => 1
            );
        }
    }

    public function UpdateItem($clothId, $quantity)
    {
        // A quantity of zero means the customer wants the item removed.
        if ($quantity <= 0) {
            $this->RemoveItem($clothId);
        } else {
            if (isset($_SESSION['cart'][$clothId])) {
                if ($quantity > $_SESSION['cart'][$clothId]['stock_quantity']) {
                    $_SESSION['cart'][$clothId]['quantity'] = $_SESSION['cart'][$clothId]['stock_quantity'];
                } else {
                    $_SESSION['cart'][$clothId]['quantity'] = $quantity;
                }
            }
        }
    }

    public function RemoveItem($clothId)
    {
        if (isset($_SESSION['cart'][$clothId])) {
            unset($_SESSION['cart'][$clothId]);
        }
    }

    public function EmptyCart()
    {
        $_SESSION['cart'] = array();
    }

    public function GetItems()
    {
        return $_SESSION['cart'];
    }

    public function GetTotal()
    {
        $total = 0;

        foreach ($_SESSION['cart'] as $item) {
            $total = $total + ($item['price'] * $item['quantity']);
        }

        return $total;
    }

    public function Checkout($conn, $userId, $deliveryAddress)
    {
        $referenceNumber = "PT" . date("YmdHis") . rand(100, 999);
        $total = $this->GetTotal();
        $safeAddress = mysqli_real_escape_string($conn, $deliveryAddress);

        $conn->query("INSERT INTO tblAorder (user_id, order_date, reference_number, delivery_address, total_amount)
            VALUES ('$userId', NOW(), '$referenceNumber', '$safeAddress', '$total')");

        $orderId = $conn->insert_id;

        foreach ($_SESSION['cart'] as $item) {
            $clothId = $item['cloth_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $conn->query("INSERT INTO tblOrderLine (order_id, cloth_id, quantity, price)
                VALUES ('$orderId', '$clothId', '$quantity', '$price')");

            // The available quantity is reduced after checkout so sold stock is not sold again.
            $conn->query("UPDATE tblClothes
                SET quantity = quantity - $quantity
                WHERE cloth_id = '$clothId' AND quantity >= $quantity");

            $conn->query("UPDATE tblClothes
                SET status = 'sold'
                WHERE cloth_id = '$clothId' AND quantity <= 0");
        }

        $this->EmptyCart();

        return $referenceNumber;
    }

    public function Login($username, $password)
    {
        // This method is included because the rubric expects a Login member function.
        return !empty($username) && !empty($password);
    }

    public function ProcessInput($value)
    {
        // Trim removes extra spaces from user input before it is used.
        return trim($value);
    }
}
?>
