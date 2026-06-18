<?php session_start(); ?>

<link rel="stylesheet" href="assets/styles.css">

<div class="navbar">
    <h3>Pastimes</h3>
    <div>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Register</a>
        <a href="admin/adminLogin.php">Admin</a>
    </div>
</div>

<div class="hero">
    <h1>Discover Second-Hand Fashion</h1>
    <p>Buy and sell premium pre-owned clothing from top brands at affordable prices.</p>

    <div style="margin-top:20px;">
        <a class="btn primary" href="auth/login.php">Start Shopping</a>
        <a class="btn secondary" href="auth/register.php">Create Account</a>
    </div>
</div>

<div class="section">
    <h2>Featured Categories</h2>

    <div class="grid">
        <div class="card">
            <h3>Streetwear</h3>
            <p>Nike, Adidas, Supreme, Off-White styles, Assics, The North Face, </p>
        </div>

        <div class="card">
            <h3>Vintage</h3>
            <p>90s fashion, retro jackets, classic denim</p>
        </div>

        <div class="card">
            <h3>Luxury</h3>
            <p>Gucci, Louis Vuitton, designer pieces</p>
        </div>
    </div>
</div>

<?php
include("config/DBConn.php");


$allResult = $conn->query("
    SELECT c.*, COALESCE(SUM(ol.quantity), 0) AS total_sold
    FROM tblClothes c
    LEFT JOIN tblOrderLine ol ON ol.cloth_id = c.cloth_id
    WHERE c.status='available'
    AND c.quantity > 0
    GROUP BY c.cloth_id
    ORDER BY c.cloth_id DESC
");

$allProducts = [];
if ($allResult) {
    while ($row = $allResult->fetch_assoc()) {
        $allProducts[] = $row;
    }
}

$popularProducts = $allProducts;
usort($popularProducts, function($a, $b) {
    return $b['total_sold'] <=> $a['total_sold'];
});
$popularIds = array_column(array_slice($popularProducts, 0, 2), 'cloth_id');

$products = array_slice($allProducts, 0, 4);

function normalizeImagePath($imagePath) {
    $imagePath = trim(str_replace('\\', '/', $imagePath));
    if ($imagePath === '') {
        return 'images/OIP.webp';
    }
    if (preg_match('#^assets/images/#i', $imagePath)) {
        $imagePath = preg_replace('#^assets/images/#i', 'images/', $imagePath);
    }
    if (!preg_match('#^(https?://|/|images/)#i', $imagePath)) {
        $imagePath = 'images/' . ltrim($imagePath, '/');
    }
    return $imagePath;
}

function resolveImageUrl($imagePath, $serverBaseDir, $urlPrefix = '') {
    $imagePath = normalizeImagePath($imagePath);
    if (preg_match('#^(https?://|/)#i', $imagePath)) {
        return $imagePath;
    }

    $serverPath = realpath($serverBaseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath));
    if ($serverPath && file_exists($serverPath)) {
        return $urlPrefix . $imagePath;
    }

    $base = preg_replace('#\.[^.]+$#', '', $imagePath);
    foreach (['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'] as $ext) {
        $candidate = $base . '.' . $ext;
        $candidateServerPath = realpath($serverBaseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate));
        if ($candidateServerPath && file_exists($candidateServerPath)) {
            return $urlPrefix . $candidate;
        }
    }

    return $urlPrefix . $imagePath;
}
?>

<div class="section">
    <h2>Latest Products</h2>

    <div class="products">

        <?php foreach ($products as $row) {
            $safeImage = htmlspecialchars(resolveImageUrl($row['image'], __DIR__, ''));
            $isPopular = in_array($row['cloth_id'], $popularIds);
        ?>

            <div class="product">
                <?php if ($isPopular) { ?>
                    <div class="product-badge popular">Popular</div>
                <?php } ?>
                <?php if ($row['quantity'] < 5) { ?>
                    <div class="product-badge limited">Limited Stock</div>
                <?php } ?>

                <img 
                    src="<?php echo $safeImage; ?>" 
                    alt="<?php echo htmlspecialchars($row['name']); ?>"
                    style="width:100%; border-radius:8px;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div class="image-placeholder">Image coming soon</div>

                <h4><?php echo htmlspecialchars($row['name']); ?></h4>

                <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>

                <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>

                <p><?php echo htmlspecialchars($row['description']); ?></p>

                <p><strong>R<?php echo number_format($row['price'], 2); ?></strong></p>

                <p>Stock: <?php echo $row['quantity']; ?></p>

                <a class="btn primary" href="user/products.php?add=<?php echo $row['cloth_id']; ?>">
                    Add to Cart
                </a>

            </div>

        <?php } ?>

    </div>
    <div class="action-row" style="text-align:center; margin-top: 20px;">
        <a class="btn primary" href="user/products.php">Shop for more</a>
    </div>
</div>

<div class="section">
    <h2>Why Pastimes?</h2>

    <div class="grid">
        <div class="card">
            <h3>Safe Trading</h3>
            <p>Admin verified users and secure transactions</p>
        </div>

        <div class="card">
            <h3>Affordable Fashion</h3>
            <p>Find branded clothes at lower prices</p>
        </div>

        <div class="card">
            <h3>Easy Selling</h3>
            <p>Upload clothes and get approved by admin</p>
        </div>
    </div>
</div>

