<?php
session_start();
include("../classes/ShoppingCart.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$cart = new ShoppingCart();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['quantity'] as $clothId => $quantity) {
        $cart->UpdateItem($clothId, intval($quantity));
    }
}

if (isset($_GET['remove'])) {
    $cart->RemoveItem($_GET['remove']);
}

if (isset($_GET['empty'])) {
    $cart->EmptyCart();
}

$items = $cart->GetItems();
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="products.php">Continue Shopping</a>
        <a href="dashboard.php">Home</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="page-header">
    <h1>Shopping Cart</h1>
    <p>Edit quantities, remove items, or continue shopping before checkout.</p>
</div>

<?php if (count($items) == 0) { ?>
    <p class="notice">Your cart is empty.</p>
    <div class="action-row">
        <a class="button-link" href="products.php">Start Shopping</a>
    </div>
<?php } else { ?>
    <form method="POST">
        <table>
            <tr>
                <th>Item</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>

            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['brand']); ?></td>
                    <td>R<?php echo number_format($item['price'], 2); ?></td>
                    <td>
                        <input class="small-input" type="number" name="quantity[<?php echo $item['cloth_id']; ?>]" min="0" max="<?php echo $item['stock_quantity']; ?>" value="<?php echo $item['quantity']; ?>">
                    </td>
                    <td>R<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $item['cloth_id']; ?>">Remove</a></td>
                </tr>
            <?php } ?>
        </table>

        <div class="cart-summary">
            <strong>Total: R<?php echo number_format($cart->GetTotal(), 2); ?></strong>
        </div>

        <div class="action-row">
            <button class="small-button" type="submit">Update Cart</button>
            <a class="button-link secondary" href="products.php">Continue Shopping</a>
            <a class="button-link" href="checkout.php">Checkout</a>
            <a class="text-link" href="cart.php?empty=1">Empty Cart</a>
        </div>
    </form>
<?php } ?>
