<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/DBConn.php");

$user = $_SESSION['user'];

$latestProductsResult = $conn->query("SELECT c.*, COALESCE((SELECT SUM(quantity) FROM tblOrderLine ol WHERE ol.cloth_id = c.cloth_id), 0) AS total_sold FROM tblClothes c WHERE status='available' AND quantity > 0 ORDER BY cloth_id DESC LIMIT 4");

$latestProducts = [];
if ($latestProductsResult) {
    while ($row = $latestProductsResult->fetch_assoc()) {
        $latestProducts[] = $row;
    }
}

$popularProducts = $latestProducts;
usort($popularProducts, function($a, $b) {
    return $b['total_sold'] <=> $a['total_sold'];
});
$popularIds = array_column(array_slice($popularProducts, 0, 2), 'cloth_id');

$categories = [
    [
        'title' => 'Luxury',
        'description' => 'Designer-inspired pieces and premium looks.',
        'link' => 'products.php?search=luxury'
    ],
    [
        'title' => 'Streetwear',
        'description' => 'Urban brands, sneakers and bold fashion.',
        'link' => 'products.php?search=streetwear'
    ],
    [
        'title' => 'Casual',
        'description' => 'Everyday comfort with a second-hand twist.',
        'link' => 'products.php?search=casual'
    ],
    [
        'title' => 'Vintage',
        'description' => 'Retro jackets, denim and classic styles.',
        'link' => 'products.php?search=vintage'
    ]
];
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

<div class="dashboard-hero dashboard-hero--compact">
    <div class="dashboard-hero-copy">
        <h1>Welcome back, <?php echo htmlspecialchars($user['fname'] . " " . $user['lname']); ?>!</h1>
        <p>Explore top categories and the freshest arrivals from our latest approved clothing selection.</p>
        <div class="dashboard-actions">
            <a class="button-link" href="products.php">Shop Now</a>
            <a class="button-link secondary" href="sellRequest.php">Sell Your Clothes</a>
        </div>
    </div>
</div>

<div class="section section--compact">
    <h2>Shop by category</h2>
    <div class="grid category-grid">
        <?php foreach ($categories as $category) { ?>
            <a class="card category-card" href="<?php echo htmlspecialchars($category['link']); ?>">
                <h3><?php echo htmlspecialchars($category['title']); ?></h3>
                <p><?php echo htmlspecialchars($category['description']); ?></p>
            </a>
        <?php } ?>
    </div>
</div>

<div class="section section--compact">
    <h2>Latest arrivals</h2>
    <?php if (!empty($latestProducts)) { ?>
        <div class="products">
            <?php foreach ($latestProducts as $row) {
                $isPopular = in_array($row['cloth_id'], $popularIds);
            ?>
                <div class="product product--dashboard">
                    <?php if ($isPopular) { ?>
                        <div class="product-badge">Popular</div>
                    <?php } ?>
                    <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="image-placeholder">Image coming soon</div>
                    <div class="product-details">
                        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                        <p><?php echo htmlspecialchars($row['brand']); ?> • <?php echo htmlspecialchars($row['size']); ?></p>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                    </div>
                    <div class="product-meta">
                        <span class="product-price">R<?php echo number_format($row['price'], 2); ?></span>
                        <span class="product-stock"><?php echo $row['quantity']; ?> available</span>
                    </div>
                    <a class="button-link" href="products.php?add=<?php echo $row['cloth_id']; ?>">Add to Cart</a>
                </div>
            <?php } ?>
        </div>
        <div class="action-row" style="text-align:center; margin-top: 20px;">
            <a class="button-link" href="products.php">Shop for more</a>
        </div>
    <?php } else { ?>
        <p class="info-text">No products are available right now. Check back soon or add a request to sell.</p>
    <?php } ?>
</div>
