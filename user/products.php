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

    if (isset($_GET['ajax'])) {
        $cartCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $itemResult->num_rows > 0,
            'message' => $message,
            'cartCount' => $cartCount
        ]);
        exit;
    }
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : "";

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

if ($search != "") {
    $result = $conn->query("SELECT * FROM tblClothes
        WHERE status='available'
        AND quantity > 0
        AND (name LIKE '%$search%' OR brand LIKE '%$search%' OR description LIKE '%$search%')");
} else {
    $result = $conn->query("SELECT * FROM tblClothes WHERE status='available' AND quantity > 0");
}

$popularIds = [];
$popularResult = $conn->query("SELECT c.cloth_id, COALESCE(SUM(ol.quantity), 0) AS total_sold
    FROM tblClothes c
    LEFT JOIN tblOrderLine ol ON ol.cloth_id = c.cloth_id
    WHERE c.status='available' AND c.quantity > 0
    GROUP BY c.cloth_id
    ORDER BY total_sold DESC
    LIMIT 2");
if ($popularResult) {
    while ($row = $popularResult->fetch_assoc()) {
        $popularIds[] = $row['cloth_id'];
    }
}

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

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar">
    <h3>Past Times</h3>
    <div>
        <a href="dashboard.php">Home</a>
        <a class="cart-link" href="cart.php">Show Cart <span id="dashboard-cart-count"><?php echo $cartCount; ?></span></a>
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

<?php while ($row = $result->fetch_assoc()) { 
    $productPayload = htmlspecialchars(json_encode([
        'id' => (int)$row['cloth_id'],
        'name' => $row['name'],
        'brand' => $row['brand'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'size' => $row['size'],
        'image' => resolveImageUrl($row['image'], realpath(__DIR__ . '/..'), '../')
    ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
?>
    <div class="product animate-drop" data-product='<?php echo $productPayload; ?>'>
        <?php if (in_array($row['cloth_id'], $popularIds)) { ?>
            <div class="product-badge popular">Popular</div>
        <?php } ?>
        <?php if ($row['quantity'] < 5) { ?>
            <div class="product-badge limited">Limited Stock</div>
        <?php } ?>
        <button class="product-image-button" type="button" aria-label="View <?php echo htmlspecialchars($row['name']); ?>">
            <img src="<?php echo htmlspecialchars(resolveImageUrl($row['image'], realpath(__DIR__ . '/..'), '../')); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        </button>
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

<div class="product-modal" id="shop-product-modal" aria-hidden="true">
    <div class="modal-backdrop" id="shop-modal-backdrop"></div>
    <div class="modal-card">
        <button class="modal-close" id="shop-modal-close" aria-label="Close product view">×</button>
        <div class="modal-gallery">
            <button class="modal-arrow left" id="shop-modal-arrow-left" aria-label="Previous image">‹</button>
            <img id="shop-modal-main-image" src="" alt="Product view">
            <button class="modal-arrow right" id="shop-modal-arrow-right" aria-label="Next image">›</button>
        </div>
        <div class="modal-details">
            <h2 id="shop-modal-name"></h2>
            <p id="shop-modal-brand"></p>
            <p id="shop-modal-description"></p>
            <p class="modal-price" id="shop-modal-price"></p>
            <div class="size-options" id="shop-modal-sizes"></div>
            <a class="button-link" href="#" id="shop-modal-add-to-cart">Add to Cart</a>
        </div>
    </div>
</div>

<script>
    const shopModal = document.getElementById('shop-product-modal');
    const shopModalBackdrop = document.getElementById('shop-modal-backdrop');
    const shopModalClose = document.getElementById('shop-modal-close');
    const shopModalMainImage = document.getElementById('shop-modal-main-image');
    const shopModalName = document.getElementById('shop-modal-name');
    const shopModalBrand = document.getElementById('shop-modal-brand');
    const shopModalDescription = document.getElementById('shop-modal-description');
    const shopModalPrice = document.getElementById('shop-modal-price');
    const shopModalSizes = document.getElementById('shop-modal-sizes');
    const shopModalAddToCart = document.getElementById('shop-modal-add-to-cart');
    const shopModalArrowLeft = document.getElementById('shop-modal-arrow-left');
    const shopModalArrowRight = document.getElementById('shop-modal-arrow-right');

    let activeShopProduct = null;
    let activeShopImageIndex = 0;

    function openShopModal(product) {
        activeShopProduct = product;
        activeShopImageIndex = 0;
        shopModalMainImage.src = product.image;
        shopModalMainImage.alt = product.name;
        shopModalName.textContent = product.name;
        shopModalBrand.textContent = product.brand;
        shopModalDescription.textContent = product.description;
        shopModalPrice.textContent = `R${product.price.toFixed(2)}`;

        shopModalSizes.innerHTML = '';
        const sizes = product.size.split(',').map(size => size.trim()).filter(Boolean);
        if (sizes.length === 0) {
            sizes.push('One Size');
        }
        sizes.forEach(size => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'size-button';
            button.textContent = size;
            button.addEventListener('click', () => {
                document.querySelectorAll('#shop-modal-sizes .size-button').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
            shopModalSizes.appendChild(button);
        });
        if (shopModalSizes.firstChild) {
            shopModalSizes.firstChild.classList.add('active');
        }

        shopModalAddToCart.href = `products.php?add=${product.id}`;
        shopModal.classList.add('open');
        shopModal.setAttribute('aria-hidden', 'false');
    }

    function closeShopModal() {
        shopModal.classList.remove('open');
        shopModal.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.product.animate-drop').forEach(card => {
        const productData = card.dataset.product ? JSON.parse(card.dataset.product) : null;
        if (!productData) return;

        card.addEventListener('click', event => {
            if (event.target.closest('.button-link')) {
                return;
            }
            openShopModal(productData);
        });
    });

    if (shopModalBackdrop) {
        shopModalBackdrop.addEventListener('click', closeShopModal);
    }
    if (shopModalClose) {
        shopModalClose.addEventListener('click', closeShopModal);
    }
    if (shopModalArrowLeft) {
        shopModalArrowLeft.addEventListener('click', () => {
            activeShopImageIndex = (activeShopImageIndex - 1 + 1) % 1;
            shopModalMainImage.src = activeShopProduct.image;
        });
    }
    if (shopModalArrowRight) {
        shopModalArrowRight.addEventListener('click', () => {
            activeShopImageIndex = (activeShopImageIndex + 1) % 1;
            shopModalMainImage.src = activeShopProduct.image;
        });
    }

    window.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('page-loaded');
        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;
            if (link.target === '_blank') return;
            link.addEventListener('click', event => {
                if (href.startsWith('http') && !href.includes(location.hostname)) return;
                if (link.classList.contains('button-link') && href.includes('add=')) return;
                event.preventDefault();
                document.body.classList.add('page-exit');
                setTimeout(() => {
                    window.location.href = href;
                }, 650);
            });
        });
    });
</script>
