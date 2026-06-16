<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="products.php">Shop</a>
        <a href="cart.php">Cart</a>
        <a href="sellRequest.php">Sell</a>
        <a href="history.php">History</a>
        <a href="messages.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($user['fname'] . " " . $user['lname']); ?>!</h1>
    <p>Browse second-hand clothing, add items to your cart, request to sell clothing, and track your orders.</p>
</div>

<div class="action-row">
    <a class="button-link" href="products.php">Shop Clothes</a>
    <a class="button-link secondary" href="sellRequest.php">Request to Sell</a>
</div>
