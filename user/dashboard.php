<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/DBConn.php");

$user = $_SESSION['user'];

$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

$brandCategoryMap = [
    'Nike' => 'Streetwear',
    'Adidas' => 'Streetwear',
    'Puma' => 'Streetwear',
    'Converse' => 'Streetwear',
    'The North Face' => 'Streetwear',
    'Amiri' => 'Streetwear',
    'Gucci' => 'Luxury',
    'Louis Vuitton' => 'Luxury',
    'Giuseppe Zanotti' => 'Luxury',
    'Valli' => 'Luxury',
    'Ermenegildo Zegna' => 'Vintage',
    'Ermenglio Zignalli' => 'Vintage',
    'Levi\'s' => 'Casual',
    'Everyday' => 'Casual'
];

$result = $conn->query("SELECT c.*, COALESCE(SUM(ol.quantity), 0) AS total_sold
    FROM tblClothes c
    LEFT JOIN tblOrderLine ol ON ol.cloth_id = c.cloth_id
    WHERE c.status='available' AND c.quantity > 0
    GROUP BY c.cloth_id
    ORDER BY c.cloth_id DESC");

$dashboardProducts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $category = $brandCategoryMap[$row['brand']] ?? 'Casual';
        if ($selectedCategory !== '' && $category !== $selectedCategory) {
            continue;
        }

        $dashboardProducts[] = [
            'id' => (int) $row['cloth_id'],
            'name' => $row['name'],
            'brand' => $row['brand'],
            'category' => $category,
            'price' => (float) $row['price'],
            'sizes' => buildDashboardSizeArray($row['size']),
            'stock' => (int) $row['quantity'],
            'popularity' => (int) $row['total_sold'],
            'description' => $row['description'],
            'images' => buildDashboardImages($row['image'])
        ];
    }
}

// Filter dashboard products by selected category.
$visibleProducts = $dashboardProducts;

function buildDashboardSizeArray($sizeString) {
    $sizeString = trim($sizeString);
    if ($sizeString === '') {
        return ['One Size'];
    }
    return array_map('trim', explode(',', $sizeString));
}

function buildDashboardImages($imagePath) {
    $imagePath = trim(str_replace('\\', '/', $imagePath));
    if ($imagePath === '') {
        return [
            'https://images.unsplash.com/photo-1520975916726-861e26ff0b6c?auto=format&fit=crop&w=1000&q=80'
        ];
    }
    if (preg_match('#^https?://#i', $imagePath)) {
        return [$imagePath, $imagePath, $imagePath];
    }
    $resolved = '../' . ltrim($imagePath, '/');
    return [$resolved, $resolved, $resolved];
}

$hasMoreThanDashboard = false;
if (count($visibleProducts) > 10) {
    $visibleProducts = array_slice($visibleProducts, 0, 10);
    $hasMoreThanDashboard = true;
}

$popularProducts = $visibleProducts;
usort($popularProducts, function ($a, $b) {
    return $b['popularity'] <=> $a['popularity'];
});
$popularIds = array_column(array_slice($popularProducts, 0, 2), 'id');

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

$categories = [
    [
        'key' => '',
        'title' => 'All',
        'description' => 'Browse every item in the latest Past Times collection.',
        'link' => 'dashboard.php'
    ],
    [
        'key' => 'Luxury',
        'title' => 'Luxury',
        'description' => 'Designer-inspired pieces and premium looks.',
        'link' => 'dashboard.php?category=Luxury'
    ],
    [
        'key' => 'Streetwear',
        'title' => 'Streetwear',
        'description' => 'Urban brands, sneakers and bold fashion.',
        'link' => 'dashboard.php?category=Streetwear'
    ],
    [
        'key' => 'Casual',
        'title' => 'Casual',
        'description' => 'Everyday comfort with a second-hand twist.',
        'link' => 'dashboard.php?category=Casual'
    ],
    [
        'key' => 'Vintage',
        'title' => 'Vintage',
        'description' => 'Retro jackets, denim and classic styles.',
        'link' => 'dashboard.php?category=Vintage'
    ]
];
?>

<link rel="stylesheet" href="../assets/styles.css">

<div class="navbar animate-drop">
    <h3>Past Times</h3>
    <div>
        <a href="products.php">Shop</a>
        <a class="cart-link" href="cart.php">Cart <span id="dashboard-cart-count"><?php echo $cartCount; ?></span></a>
        <a href="sellRequest.php">Sell</a>
        <a href="history.php">History</a>
        <a href="messages.php">Messages</a>
        <a href="../auth/logout.php">Logout</a>
    </div>
</div>

<div class="dashboard-hero dashboard-hero--compact animate-drop">
    <div class="dashboard-hero-copy">
        <h1>Welcome back, <?php echo htmlspecialchars($user['fname'] . " " . $user['lname']); ?>!</h1>
        <p>Explore top categories and the freshest arrivals from our latest approved clothing selection.</p>
        <div class="dashboard-actions">
            <a class="button-link" href="products.php">Shop Now</a>
            <a class="button-link" href="sellRequest.php">Sell Your Clothes</a>
        </div>
    </div>
</div>

<div class="section section--compact">
    <h2>Shop by category</h2>
    <div class="grid category-grid">
        <?php foreach ($categories as $category) { ?>
            <a class="card category-card animate-drop<?php echo $selectedCategory === $category['key'] ? ' active' : ''; ?>" href="<?php echo htmlspecialchars($category['link']); ?>">
                <h3><?php echo htmlspecialchars($category['title']); ?></h3>
                <p><?php echo htmlspecialchars($category['description']); ?></p>
            </a>
        <?php } ?>
    </div>
</div>

<div class="section section--compact animate-drop">
    <h2>Latest arrivals</h2>
    <?php if ($selectedCategory !== '') { ?>
        <p class="filter-note">Showing <?php echo htmlspecialchars($selectedCategory); ?> picks across the latest collection.</p>
    <?php } ?>
    <?php if (!empty($visibleProducts)) { ?>
        <?php
            $initialItems = array_slice($visibleProducts, 0, 4);
            $expandedItems = array_slice($visibleProducts, 4, 6);
        ?>
        <div class="products">
            <?php foreach ($initialItems as $row) {
                $isPopular = in_array($row['id'], $popularIds);
                $displayQuantity = $row['stock'];
                $showLimited = $row['stock'] < 5;
            ?>
                <div class="product product--dashboard animate-drop" data-product-id="<?php echo $row['id']; ?>">
                    <?php if ($isPopular) { ?>
                        <div class="product-badge popular">Popular</div>
                    <?php } ?>
                    <?php if ($showLimited) { ?>
                        <div class="product-badge limited">Limited Stock</div>
                    <?php } ?>
                    <button class="product-image-button" type="button" aria-label="View <?php echo htmlspecialchars($row['name']); ?>">
                        <img src="<?php echo htmlspecialchars($row['images'][0]); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </button>
                    <div class="image-placeholder">Image coming soon</div>
                    <div class="product-details">
                        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                        <p class="brand-name"><?php echo htmlspecialchars($row['brand']); ?></p>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                    </div>
                    <div class="product-meta">
                        <span class="product-price">R<?php echo number_format($row['price'], 2); ?></span>
                        <span class="product-stock"><?php echo $displayQuantity; ?> available</span>
                    </div>
                    <a class="button-link dashboard-add-to-cart" href="#" data-product-id="<?php echo $row['id']; ?>">Add to Cart</a>
                </div>
            <?php } ?>
        </div>

        <?php if (!empty($expandedItems)) { ?>
            <div class="action-row" style="text-align:center; margin-top: 20px;">
                <a href="#" id="dashboard-show-more" class="button-link">Shop for more</a>
            </div>
            <div class="products more-products" id="dashboard-more-products" style="display:none;">
                <?php foreach ($expandedItems as $row) {
                    $isPopular = in_array($row['id'], $popularIds);
                    $displayQuantity = $row['stock'];
                    $showLimited = $row['stock'] < 5;
                ?>
                    <div class="product product--dashboard animate-drop" data-product-id="<?php echo $row['id']; ?>">
                        <?php if ($isPopular) { ?>
                            <div class="product-badge popular">Popular</div>
                        <?php } ?>
                        <?php if ($showLimited) { ?>
                            <div class="product-badge limited">Limited Stock</div>
                        <?php } ?>
                        <button class="product-image-button" type="button" aria-label="View <?php echo htmlspecialchars($row['name']); ?>">
                            <img src="<?php echo htmlspecialchars($row['images'][0]); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </button>
                        <div class="image-placeholder">Image coming soon</div>
                        <div class="product-details">
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p class="brand-name"><?php echo htmlspecialchars($row['brand']); ?></p>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                        <div class="product-meta">
                            <span class="product-price">R<?php echo number_format($row['price'], 2); ?></span>
                            <span class="product-stock"><?php echo $displayQuantity; ?> available</span>
                        </div>
                        <a class="button-link dashboard-add-to-cart" href="#" data-product-id="<?php echo $row['id']; ?>">Add to Cart</a>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="dashboard-info-panel animate-drop">
            <!-- Go to Shop button appears below the info panel so users can browse the full DB -->
            <div>
                <h3>Estimated Shipping</h3>
                <p>2 to 4 business days for local delivery, and 5 to 7 days for national shipping.</p>
            </div>
            <div>
                <h3>Payment Methods</h3>
                <p>We accept Visa, Mastercard, EFT, and mobile payments for fast checkout.</p>
            </div>
            <div>
                <h3>Customer Support</h3>
                <p>Questions? Call Past Times customer service at <strong>0800-PASTIMES</strong> for help.</p>
            </div>
        </div>

        <div class="action-row" style="text-align:center; margin-top: 22px;">
            <a class="button-link" href="products.php">Go to Shop</a>
        </div>

        <!-- Fullscreen product viewer with image carousel and size chooser -->
        <div class="product-modal" id="dashboard-product-modal" aria-hidden="true">
            <div class="modal-backdrop" id="dashboard-modal-backdrop"></div>
            <div class="modal-card">
                <button class="modal-close" id="dashboard-modal-close" aria-label="Close product view">×</button>
                <div class="modal-gallery">
                    <button class="modal-arrow left" id="modal-arrow-left" aria-label="Previous image">‹</button>
                    <img id="modal-main-image" src="" alt="Product view">
                    <button class="modal-arrow right" id="modal-arrow-right" aria-label="Next image">›</button>
                </div>
                <div class="modal-details">
                    <h2 id="modal-name"></h2>
                    <p id="modal-brand"></p>
                    <p id="modal-description"></p>
                    <p class="modal-price" id="modal-price"></p>
                    <div class="size-options" id="modal-sizes"></div>
                    <a class="button-link" href="#" id="modal-add-to-cart">Add to Cart</a>
                </div>
            </div>
        </div>

        <div class="dashboard-toast" id="dashboard-toast" style="display:none;"></div>
    <?php } else { ?>
        <p class="info-text">No products are available right now. Check back soon or add a request to sell.</p>
    <?php } ?>
</div>

<script>
    // Product data used by the dashboard modal and cart interactions.
    const dashboardProducts = <?php echo json_encode($dashboardProducts, JSON_HEX_TAG); ?>;
    let dashboardCartCount = <?php echo $cartCount ?: 0; ?>;
    const storedCount = sessionStorage.getItem('dashboardCartCount');
    if (storedCount !== null) {
        dashboardCartCount = parseInt(storedCount, 10) || dashboardCartCount;
    }

    const cartCountElement = document.getElementById('dashboard-cart-count');
    const toast = document.getElementById('dashboard-toast');
    const showMoreButton = document.getElementById('dashboard-show-more');
    const moreProducts = document.getElementById('dashboard-more-products');
    const modal = document.getElementById('dashboard-product-modal');
    const modalBackdrop = document.getElementById('dashboard-modal-backdrop');
    const modalClose = document.getElementById('dashboard-modal-close');
    const modalMainImage = document.getElementById('modal-main-image');
    const modalName = document.getElementById('modal-name');
    const modalBrand = document.getElementById('modal-brand');
    const modalDescription = document.getElementById('modal-description');
    const modalPrice = document.getElementById('modal-price');
    const modalSizes = document.getElementById('modal-sizes');
    const modalAddToCart = document.getElementById('modal-add-to-cart');
    const modalArrowLeft = document.getElementById('modal-arrow-left');
    const modalArrowRight = document.getElementById('modal-arrow-right');

    let currentModalProduct = null;
    let currentImageIndex = 0;

    function updateCartDisplay() {
        if (cartCountElement) {
            cartCountElement.textContent = dashboardCartCount;
            sessionStorage.setItem('dashboardCartCount', dashboardCartCount);
        }
    }

    function showDashboardToast(message, isError = false) {
        if (!toast) return;
        toast.textContent = message;
        toast.style.background = isError ? 'rgba(204, 0, 0, 0.95)' : 'rgba(0, 0, 0, 0.85)';
        toast.style.display = 'block';
        toast.style.opacity = '1';
        window.clearTimeout(toast.dismissTimer);
        toast.dismissTimer = window.setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.style.display = 'none', 300);
        }, 2300);
    }

    function openProductModal(productId) {
        currentModalProduct = dashboardProducts.find(item => item.id === Number(productId));
        if (!currentModalProduct || !modal) return;

        currentImageIndex = 0;
        modalMainImage.src = currentModalProduct.images[0];
        modalMainImage.alt = currentModalProduct.name;
        modalName.textContent = currentModalProduct.name;
        modalBrand.textContent = currentModalProduct.brand;
        modalDescription.textContent = currentModalProduct.description;
        modalPrice.textContent = `R${currentModalProduct.price.toFixed(2)}`;
        modalSizes.innerHTML = '';
        currentModalProduct.sizes.forEach(size => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'size-button';
            button.textContent = size;
            button.addEventListener('click', () => {
                document.querySelectorAll('.size-button').forEach(el => el.classList.remove('active'));
                button.classList.add('active');
            });
            modalSizes.appendChild(button);
        });
        if (modalSizes.firstChild) {
            modalSizes.firstChild.classList.add('active');
        }
        modalAddToCart.dataset.productId = currentModalProduct.id;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeProductModal() {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    function changeModalImage(direction) {
        if (!currentModalProduct) return;
        const images = currentModalProduct.images;
        currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
        modalMainImage.src = images[currentImageIndex];
    }

    function addToCart() {
        dashboardCartCount += 1;
        updateCartDisplay();
        showDashboardToast('Item has been added to the cart.');
    }

    if (updateCartDisplay) {
        updateCartDisplay();
    }

    document.querySelectorAll('.dashboard-add-to-cart').forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            addToCart();
        });
    });

    document.querySelectorAll('.product.product--dashboard').forEach(card => {
        card.addEventListener('click', event => {
            if (event.target.closest('.dashboard-add-to-cart')) {
                return;
            }
            const productId = card.dataset.productId;
            if (productId) {
                openProductModal(productId);
            }
        });
    });

    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', closeProductModal);
    }
    if (modalClose) {
        modalClose.addEventListener('click', closeProductModal);
    }
    if (modalArrowLeft) {
        modalArrowLeft.addEventListener('click', () => changeModalImage(-1));
    }
    if (modalArrowRight) {
        modalArrowRight.addEventListener('click', () => changeModalImage(1));
    }
    if (modalAddToCart) {
        modalAddToCart.addEventListener('click', event => {
            event.preventDefault();
            addToCart();
            closeProductModal();
        });
    }

    if (showMoreButton && moreProducts) {
        showMoreButton.addEventListener('click', event => {
            event.preventDefault();
            const visible = moreProducts.style.display === 'grid' || moreProducts.style.display === 'block';
            moreProducts.style.display = visible ? 'none' : 'grid';
            showMoreButton.textContent = visible ? 'Shop for more' : 'Show fewer';
        });
    }

    // Add page transition animation for navigation between user pages.
    window.addEventListener('DOMContentLoaded', () => {
        document.body.classList.add('page-loaded');

        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;
            if (link.target === '_blank') return;
            link.addEventListener('click', event => {
                if (href.startsWith('http') && !href.includes(location.hostname)) return;
                event.preventDefault();
                document.body.classList.add('page-exit');
                setTimeout(() => {
                    window.location.href = href;
                }, 650);
            });
        });
    });
</script>
