<?php
session_start();
include("../config/DBConn.php");
include("../classes/ShoppingCart.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php?message=login_required");
    exit();
}

$cart = new ShoppingCart();
$items = $cart->GetItems();
$user = $_SESSION['user'];
$message = "";
$referenceNumber = "";
$deliveryAddress = isset($user['address']) ? $user['address'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deliveryAddress = trim($_POST['delivery_address']);

    if (count($items) == 0) {
        $message = "Your cart is empty.";
    } elseif ($deliveryAddress == "") {
        $message = "Please enter a delivery address.";
    } else {
        $referenceNumber = $cart->Checkout($conn, $user['user_id'], $deliveryAddress);
        $message = "Checkout complete. Your reference number is " . $referenceNumber . ".";
    }
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="cart.php">Cart</a>
        <a href="products.php">Shop</a>
        <a href="history.php">History</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card wide-card">
    <h2>Checkout</h2>

    <?php if ($message != "") { ?>
        <p class="success"><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <?php if ($referenceNumber == "" && count($items) > 0) { ?>
        <p>Total to pay: <strong>R<?php echo number_format($cart->GetTotal(), 2); ?></strong></p>

        <form method="POST">
            <label>Delivery Address</label>
            <input type="text" name="delivery_address" required value="<?php echo htmlspecialchars($deliveryAddress); ?>">

            <button type="submit">Place Order</button>
        </form>
    <?php } ?>

    <div class="link">
        <a href="products.php">Continue Shopping</a>
    </div>
</div>
</div>
