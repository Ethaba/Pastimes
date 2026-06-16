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
            <p>Nike, Adidas, Supreme, Off-White styles</p>
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

// Load products from database
$result = $conn->query("
    SELECT * 
    FROM tblClothes 
    WHERE status='available' 
    AND quantity > 0
    ORDER BY cloth_id DESC
    LIMIT 8
");
?>

<div class="section">
    <h2>Latest Products</h2>

    <div class="products">

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div class="product">

                <img 
                    src="<?php echo htmlspecialchars($row['image']); ?>" 
                    alt="<?php echo htmlspecialchars($row['name']); ?>"
                    style="width:100%; border-radius:8px;"
                    onerror="this.style.display='none';"
                >

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

