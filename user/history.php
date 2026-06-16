<?php
session_start();
include("../config/DBConn.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user']['user_id'];

// This report shows previous purchases and calculates the totals from the order lines.
$orders = $conn->query("SELECT * FROM tblAorder WHERE user_id='$userId' ORDER BY order_date DESC");
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="page-header">
    <h1>Purchase History</h1>
    <p>Review your completed orders and reference numbers.</p>
</div>

<?php if ($orders->num_rows == 0) { ?>
    <p class="notice">No purchases have been made yet.</p>
<?php } ?>

<?php while ($order = $orders->fetch_assoc()) { ?>
    <div class="report-card">
        <h3>Reference: <?php echo htmlspecialchars($order['reference_number']); ?></h3>
        <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p>Delivery: <?php echo htmlspecialchars($order['delivery_address']); ?></p>

        <table>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>

            <?php
            $orderId = $order['order_id'];
            $lines = $conn->query("SELECT tblOrderLine.*, tblClothes.name
                FROM tblOrderLine
                INNER JOIN tblClothes ON tblOrderLine.cloth_id = tblClothes.cloth_id
                WHERE tblOrderLine.order_id='$orderId'");

            while ($line = $lines->fetch_assoc()) {
                $subtotal = $line['quantity'] * $line['price'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($line['name']); ?></td>
                    <td><?php echo $line['quantity']; ?></td>
                    <td>R<?php echo number_format($line['price'], 2); ?></td>
                    <td>R<?php echo number_format($subtotal, 2); ?></td>
                </tr>
            <?php } ?>
        </table>

        <p class="cart-summary">Order Total: R<?php echo number_format($order['total_amount'], 2); ?></p>
    </div>
<?php } ?>
