<?php
session_start();
include("../config/DBConn.php");
include("../classes/ShoppingCart.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$cart = new ShoppingCart();
$message = "";

if (isset($_GET['add'])) {
    $clothId = mysqli_real_escape_string($conn, $_GET['add']);
    $itemResult = $conn->query("SELECT * FROM tblClothes WHERE cloth_id='$clothId' AND status='available' AND quantity > 0");

    if ($itemResult->num_rows > 0) {
        $cart->AddItem($itemResult->fetch_assoc());
        $message = "Item added to cart.";
    }
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : "";

if ($search != "") {
    $result = $conn->query("SELECT * FROM tblClothes
        WHERE status='available'
        AND quantity > 0
        AND (name LIKE '%$search%' OR brand LIKE '%$search%' OR description LIKE '%$search%')");
} else {
    $result = $conn->query("SELECT * FROM tblClothes WHERE status='available' AND quantity > 0");
}
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="cart.php">Show Cart</a>
        <a href="sellRequest.php">Sell</a>
        <a href="history.php">History</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="page-header">
    <h1>Shop Second-Hand Clothing</h1>
    <p>Search by item name, brand or style description.</p>
</div>

<form class="search-bar" method="GET">
    <input type="text" name="search" placeholder="Search clothes, brands or styles" value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<?php if ($message != "") { ?>
    <p class="success"><?php echo $message; ?></p>
<?php } ?>

<div class="products">

<?php while ($row = $result->fetch_assoc()) { ?>
    <div class="product">
        <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="image-placeholder">Image coming soon</div>
        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
        <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        <p><strong>R<?php echo number_format($row['price'], 2); ?></strong></p>
        <p>Size: <?php echo htmlspecialchars($row['size']); ?> | Available: <?php echo $row['quantity']; ?></p>
        <a class="button-link" href="products.php?add=<?php echo $row['cloth_id']; ?>">Add To Cart</a>
    </div>
<?php } ?>

</div>
