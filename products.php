<?php
$pageTitle = "Royal Ethnic Wear & Suits Collection";
require_once __DIR__ . '/includes/header.php';

// Parameters
$categorySlug = trim($_GET['category'] ?? '');
$searchQuery  = trim($_GET['q'] ?? '');
$sortBy       = trim($_GET['sort'] ?? 'newest');

// Fetch current category if specified
$currentCategory = null;
if (!empty($categorySlug)) {
    $cStmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status='active'");
    $cStmt->execute([$categorySlug]);
    $currentCategory = $cStmt->fetch();
    if ($currentCategory) {
        $pageTitle = $currentCategory['name'] . " | Vastra Mahal";
    }
}

// Build query
$whereClauses = ["p.status = 'active'"];
$params = [];

if ($currentCategory) {
    $whereClauses[] = "p.category_id = ?";
    $params[] = $currentCategory['id'];
}

if (!empty($searchQuery)) {
    $whereClauses[] = "(p.name LIKE ? OR p.fabric LIKE ? OR p.color LIKE ? OR p.work_type LIKE ? OR p.short_desc LIKE ?)";
    $like = '%' . $searchQuery . '%';
    $params = array_merge($params, [$like, $like, $like, $like, $like]);
}

$whereSql = implode(' AND ', $whereClauses);

// Sorting
$orderBySql = "ORDER BY p.id DESC";
if ($sortBy === 'price_low') {
    $orderBySql = "ORDER BY COALESCE(p.sale_price, p.price) ASC";
} elseif ($sortBy === 'price_high') {
    $orderBySql = "ORDER BY COALESCE(p.sale_price, p.price) DESC";
} elseif ($sortBy === 'featured') {
    $orderBySql = "ORDER BY p.is_featured DESC, p.id DESC";
}

// Execute query
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE $whereSql 
        $orderBySql";

$pStmt = $pdo->prepare($sql);
$pStmt->execute($params);
$products = $pStmt->fetchAll();

// Fetch all categories for sidebar
$sidebarCats = $pdo->query("SELECT c.*, COUNT(p.id) as count 
                            FROM categories c 
                            LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
                            WHERE c.status='active' 
                            GROUP BY c.id 
                            ORDER BY c.id ASC")->fetchAll();
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1><?= $currentCategory ? e($currentCategory['name']) : 'Our Complete Ethnic Collection'; ?></h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <a href="products.php">Products</a>
            <?php if ($currentCategory): ?>
                <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
                <span><?= e($currentCategory['name']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Products Section -->
<section class="pb-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left Sidebar Filters -->
            <div class="col-lg-3 col-md-4">
                <!-- Search Box -->
                <div class="card border rounded-3 p-3 mb-4 shadow-sm bg-white">
                    <h6 class="fw-bold mb-3 text-uppercase text-danger" style="letter-spacing:1px;font-size:13px;">Search Collection</h6>
                    <form action="products.php" method="GET">
                        <?php if ($categorySlug): ?>
                            <input type="hidden" name="category" value="<?= e($categorySlug); ?>">
                        <?php endif; ?>
                        <div class="input-group">
                            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search suits, sarees..." value="<?= e($searchQuery); ?>">
                            <button class="btn btn-dark btn-sm" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Category Filter -->
                <div class="card border rounded-3 p-3 mb-4 shadow-sm bg-white">
                    <h6 class="fw-bold mb-3 text-uppercase text-danger" style="letter-spacing:1px;font-size:13px;">Categories</h6>
                    <div class="d-flex flex-column gap-2">
                        <a href="products.php<?= !empty($searchQuery) ? '?q='.urlencode($searchQuery) : ''; ?>" class="d-flex justify-content-between align-items-center text-decoration-none py-1 border-bottom <?= empty($categorySlug) ? 'text-danger fw-bold' : 'text-dark'; ?>">
                            <span>All Collections</span>
                            <span class="badge bg-light text-dark rounded-pill"><?= array_sum(array_column($sidebarCats, 'count')); ?></span>
                        </a>
                        <?php foreach ($sidebarCats as $sCat): 
                            $isActive = ($categorySlug === $sCat['slug']);
                        ?>
                            <a href="products.php?category=<?= e($sCat['slug']); ?><?= !empty($searchQuery) ? '&q='.urlencode($searchQuery) : ''; ?>" class="d-flex justify-content-between align-items-center text-decoration-none py-1 border-bottom <?= $isActive ? 'text-danger fw-bold' : 'text-dark'; ?>">
                                <span><?= e($sCat['name']); ?></span>
                                <span class="badge <?= $isActive ? 'bg-danger text-white' : 'bg-light text-dark'; ?> rounded-pill"><?= (int)$sCat['count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Special Girl Suits Banner Callout -->
                <div class="card border-0 rounded-3 p-3 mb-4 text-white shadow-sm" style="background: linear-gradient(135deg, #A81C20, #580B0D);">
                    <span class="badge bg-warning text-dark align-self-start mb-2 fw-bold">Trending Now</span>
                    <h5 class="fw-bold text-white mb-2" style="font-family: var(--vm-font-title);">Girl Suits & Partywear</h5>
                    <p class="small text-light opacity-90 mb-3">Shop matching festive salwar suits, shararas, and peplum sets for young girls and teens.</p>
                    <a href="products.php?category=girl-suits" class="btn btn-sm btn-light text-danger fw-bold">
                        Browse Girl Suits <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <!-- Store Location & QR Sidebar Widget -->
                <div class="card border rounded-3 p-3 shadow-sm bg-white text-center">
                    <span class="text-danger fw-bold small text-uppercase" style="letter-spacing:1px;">Try Before You Buy</span>
                    <h6 class="fw-bold mt-1 mb-2">Visit Janakpuri Boutique</h6>
                    <img src="images/vastra-mahal-location-qr.png" alt="Store QR" class="img-fluid rounded mx-auto mb-2 border p-1" style="max-width:140px;">
                    <p class="small text-muted mb-2">Scan on mobile to navigate directly to the shop!</p>
                    <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-royal-outline w-100">
                        <i class="fa-solid fa-location-dot me-1"></i> Shop Directions
                    </a>
                </div>
            </div>

            <!-- Right Products Grid -->
            <div class="col-lg-9 col-md-8">
                <!-- Sorting Bar -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div class="mb-2 mb-md-0">
                        <span class="text-muted small">Showing <strong><?= count($products); ?></strong> exquisite designs</span>
                        <?php if (!empty($searchQuery)): ?>
                            <span class="badge bg-secondary ms-2">Search: "<?= e($searchQuery); ?>" <a href="products.php<?= $categorySlug ? '?category='.urlencode($categorySlug) : ''; ?>" class="text-white ms-1 text-decoration-none">&times;</a></span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted text-nowrap">Sort By:</label>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">
                            <?php 
                                $baseSortUrl = 'products.php?';
                                if ($categorySlug) $baseSortUrl .= 'category=' . urlencode($categorySlug) . '&';
                                if ($searchQuery) $baseSortUrl .= 'q=' . urlencode($searchQuery) . '&';
                            ?>
                            <option value="<?= $baseSortUrl; ?>sort=newest" <?= $sortBy === 'newest' ? 'selected' : ''; ?>>New Arrivals</option>
                            <option value="<?= $baseSortUrl; ?>sort=featured" <?= $sortBy === 'featured' ? 'selected' : ''; ?>>Royal Featured</option>
                            <option value="<?= $baseSortUrl; ?>sort=price_low" <?= $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="<?= $baseSortUrl; ?>sort=price_high" <?= $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                    <div class="text-center py-5 bg-white rounded-3 border p-5">
                        <i class="fa-solid fa-vest-patches fs-1 text-muted mb-3"></i>
                        <h4 class="text-dark">No Designs Found</h4>
                        <p class="text-muted">We couldn't find any designs matching your criteria. Try adjusting your filters or browse our complete collection.</p>
                        <a href="products.php" class="btn btn-royal mt-2">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($products as $prod): 
                            $hasDiscount = !empty($prod['sale_price']) && $prod['sale_price'] < $prod['price'];
                            $discountPercent = $hasDiscount ? round((($prod['price'] - $prod['sale_price']) / $prod['price']) * 100) : 0;
                            $effectivePrice = $hasDiscount ? $prod['sale_price'] : $prod['price'];
                        ?>
                            <div class="col-lg-4 col-sm-6 col-6">
                                <div class="product-card-luxury">
                                    <div class="product-img-box">
                                        <a href="product-detail.php?id=<?= $prod['id']; ?>">
                                            <img src="<?= e($prod['main_image']); ?>" alt="<?= e($prod['name']); ?>">
                                        </a>
                                        <div class="product-badge-group">
                                            <?php if ($hasDiscount): ?>
                                                <span class="badge-luxury-sale"><?= $discountPercent; ?>% OFF</span>
                                            <?php endif; ?>
                                            <?php if ($prod['category_slug'] === 'girl-suits'): ?>
                                                <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-sparkles"></i> Girl Suit</span>
                                            <?php elseif ($prod['is_featured']): ?>
                                                <span class="badge-luxury-gold"><i class="fa-solid fa-crown me-1"></i> Featured</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="product-info-box">
                                        <span class="product-category-name"><?= e($prod['category_name']); ?></span>
                                        <h3 class="product-title">
                                            <a href="product-detail.php?id=<?= $prod['id']; ?>"><?= e($prod['name']); ?></a>
                                        </h3>

                                        <div class="product-specs-chips">
                                            <?php if (!empty($prod['fabric'])): ?>
                                                <span class="spec-chip"><i class="fa-solid fa-feather-pointed me-1"></i> <?= e($prod['fabric']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($prod['color'])): ?>
                                                <span class="spec-chip"><i class="fa-solid fa-palette me-1"></i> <?= e($prod['color']); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="product-price-row">
                                            <span class="price-current">
                                                <?= formatRupee($effectivePrice); ?>
                                            </span>
                                            <?php if ($hasDiscount): ?>
                                                <span class="price-original"><?= formatRupee($prod['price']); ?></span>
                                                <span class="price-discount"><?= $discountPercent; ?>% off</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="product-action-row">
                                            <a href="product-detail.php?id=<?= $prod['id']; ?>" class="btn btn-outline-dark">
                                                Details
                                            </a>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=<?= urlencode("Hello Vastra Mahal! I am inquiring about " . $prod['name'] . " (" . formatRupee($effectivePrice) . "). Is it in stock in store?"); ?>" target="_blank" class="btn btn-whatsapp" title="Inquire on WhatsApp">
                                                <i class="fa-brands fa-whatsapp"></i> Chat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
