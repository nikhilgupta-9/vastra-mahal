<?php
require_once __DIR__ . '/config/db.php';

$productId = (int)($_GET['id'] ?? 0);
if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Fetch Product Details
try {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ? AND p.status='active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: products.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'] . " | Vastra Mahal";
require_once __DIR__ . '/includes/header.php';

$hasDiscount = !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
$discountPercent = $hasDiscount ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;
$effectivePrice = $hasDiscount ? $product['sale_price'] : $product['price'];

// Fetch related products
$relStmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                          FROM products p 
                          JOIN categories c ON p.category_id = c.id 
                          WHERE p.category_id = ? AND p.id != ? AND p.status='active' 
                          LIMIT 4");
$relStmt->execute([$product['category_id'], $productId]);
$relatedProducts = $relStmt->fetchAll();

// Product page URL for WhatsApp share
$currentProductUrl = SITE_URL . '/product-detail.php?id=' . $product['id'];
$waMessage = "Hello Vastra Mahal! I am interested in this design:\n\n*{$product['name']}*\nPrice: " . formatRupee($effectivePrice) . "\nSKU: {$product['sku']}\n\nLink: {$currentProductUrl}\n\nIs this available in the Sagar Pur boutique for trial or delivery?";
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1 style="font-size:32px;"><?= e($product['name']); ?></h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <a href="products.php">Products</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <a href="products.php?category=<?= e($product['category_slug']); ?>"><?= e($product['category_name']); ?></a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <span class="text-truncate" style="max-width:200px;"><?= e($product['name']); ?></span>
        </div>
    </div>
</div>

<!-- Main Product Details Section -->
<section class="pb-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left: Main Image Showcase -->
            <div class="col-lg-6">
                <div class="position-sticky" style="top: 100px;">
                    <div class="card border rounded-3 overflow-hidden shadow-lg p-2 bg-white" style="border-color: var(--vm-border) !important;">
                        <div class="position-relative">
                            <img src="<?= e($product['main_image']); ?>" alt="<?= e($product['name']); ?>" class="img-fluid w-100 rounded" style="aspect-ratio: 3/4; object-fit: cover;">
                            
                            <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-2">
                                <?php if ($hasDiscount): ?>
                                    <span class="badge bg-danger fs-6 px-3 py-2"><?= $discountPercent; ?>% OFF</span>
                                <?php endif; ?>
                                <?php if ($product['category_slug'] === 'girl-suits'): ?>
                                    <span class="badge bg-warning text-dark fw-bold fs-6 px-3 py-2"><i class="fa-solid fa-sparkles me-1"></i> Girl Suit</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Strip Under Image -->
                    <div class="d-flex justify-content-around text-center py-3 px-2 mt-3 bg-white border rounded shadow-sm">
                        <div>
                            <i class="fa-solid fa-shield-halved text-success fs-5"></i>
                            <div class="small fw-semibold mt-1">100% Authentic</div>
                        </div>
                        <div>
                            <i class="fa-solid fa-truck-fast text-primary fs-5"></i>
                            <div class="small fw-semibold mt-1">Pan-India Delivery</div>
                        </div>
                        <div>
                            <i class="fa-solid fa-scissors text-danger fs-5"></i>
                            <div class="small fw-semibold mt-1">Custom Fit Ready</div>
                        </div>
                        <div>
                            <i class="fa-solid fa-store text-warning fs-5"></i>
                            <div class="small fw-semibold mt-1">In-Store Trial</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Specifications & CTAs -->
            <div class="col-lg-6">
                <div class="ps-lg-3">
                    <span class="badge bg-light text-danger border border-danger text-uppercase px-3 py-2 mb-2 fw-bold" style="letter-spacing:1px;">
                        <?= e($product['category_name']); ?>
                    </span>
                    <h2 class="display-6 fw-bold text-dark mb-2" style="font-family: var(--vm-font-title);"><?= e($product['name']); ?></h2>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="small text-muted">SKU: <strong><?= e($product['sku'] ?? 'VM-' . $product['id']); ?></strong></span>
                        <span>•</span>
                        <?php if ($product['in_stock']): ?>
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fa-solid fa-check me-1"></i> Ready In Boutique Stock</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Made to Order (7-10 Days)</span>
                        <?php endif; ?>
                    </div>

                    <!-- Price Block -->
                    <div class="p-3 rounded-3 mb-4" style="background-color: #FAF4EB; border: 1px solid var(--vm-border);">
                        <div class="d-flex align-items-baseline gap-3">
                            <span class="display-6 fw-bold" style="color: var(--vm-maroon);">
                                <?= formatRupee($effectivePrice); ?>
                            </span>
                            <?php if ($hasDiscount): ?>
                                <span class="fs-4 text-muted text-decoration-line-through">
                                    <?= formatRupee($product['price']); ?>
                                </span>
                                <span class="badge bg-danger fs-6">
                                    Save <?= formatRupee($product['price'] - $product['sale_price']); ?> (<?= $discountPercent; ?>%)
                                </span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted d-block mt-1">Price includes all taxes. In-store fitting & customization available.</small>
                    </div>

                    <!-- Specifications Table -->
                    <div class="card border rounded-3 p-3 mb-4 bg-white shadow-sm">
                        <h6 class="fw-bold text-dark text-uppercase mb-3" style="letter-spacing:1px;font-size:13px;">Fabric & Craftsmanship Details</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <?php if (!empty($product['fabric'])): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold" style="width: 140px;"><i class="fa-solid fa-feather-pointed me-2 text-danger"></i> Fabric:</td>
                                            <td class="text-dark fw-bold"><?= e($product['fabric']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($product['color'])): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><i class="fa-solid fa-palette me-2 text-danger"></i> Color:</td>
                                            <td class="text-dark fw-bold"><?= e($product['color']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($product['work_type'])): ?>
                                        <tr>
                                            <td class="text-muted fw-semibold"><i class="fa-solid fa-wand-magic-sparkles me-2 text-danger"></i> Work / Weave:</td>
                                            <td class="text-dark fw-bold"><?= e($product['work_type']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td class="text-muted fw-semibold"><i class="fa-solid fa-shirt me-2 text-danger"></i> Occasion:</td>
                                        <td class="text-dark fw-bold">Weddings, Sangeet, Festivals, Grand Receptions</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold"><i class="fa-solid fa-bath me-2 text-danger"></i> Wash Care:</td>
                                        <td class="text-dark fw-bold">Strictly Dry Clean Only</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark text-uppercase mb-2" style="letter-spacing:1px;font-size:13px;">About This Creation</h6>
                        <p class="text-muted" style="line-height: 1.8;">
                            <?= nl2br(e($product['description'] ?? $product['short_desc'])); ?>
                        </p>
                    </div>

                    <!-- Action CTAs: WhatsApp + Store Visit -->
                    <div class="d-flex flex-column gap-3 mb-4">
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=<?= urlencode($waMessage); ?>" target="_blank" class="btn btn-lg btn-whatsapp py-3 justify-content-center shadow" style="font-size:17px;">
                            <i class="fa-brands fa-whatsapp fs-4 me-2"></i> Inquire / Order on WhatsApp
                        </a>

                        <div class="d-flex gap-2">
                            <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-royal flex-grow-1 justify-content-center py-2" title="Navigate to Sagar Pur Store">
                                <i class="fa-solid fa-location-dot me-2"></i> Visit Store To Try (GPS Navigation)
                            </a>
                            <a href="contact.php" class="btn btn-royal-outline py-2 px-3" title="Send Contact Inquiry">
                                <i class="fa-regular fa-envelope"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Store Visit Highlight Box -->
                    <div class="p-3 rounded-3 border d-flex align-items-center gap-3 bg-white shadow-sm" style="border-left: 4px solid var(--vm-maroon) !important;">
                        <img src="images/vastra-mahal-location-qr.png" alt="Scan Shop QR" class="img-fluid rounded border" style="width:75px;height:75px;">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Try this design in our Sagar Pur Store</h6>
                            <p class="small text-muted mb-1"><?= e($storeAddress); ?></p>
                            <a href="<?= e($googleMapUrl); ?>" target="_blank" class="small text-danger fw-bold text-decoration-none">
                                <i class="fa-solid fa-diamond-turn-right me-1"></i> Get Directions in Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mt-5 pt-4 border-top">
                <div class="text-center mb-4">
                    <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">Complete The Look</span>
                    <h3 class="fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Similar Creations in <?= e($product['category_name']); ?></h3>
                    <div class="mx-auto" style="width: 50px; height: 3px; background-color: var(--vm-gold);"></div>
                </div>

                <div class="row g-4">
                    <?php foreach ($relatedProducts as $relProd): 
                        $rHasDiscount = !empty($relProd['sale_price']) && $relProd['sale_price'] < $relProd['price'];
                        $rPrice = $rHasDiscount ? $relProd['sale_price'] : $relProd['price'];
                    ?>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="product-card-luxury">
                                <div class="product-img-box">
                                    <a href="product-detail.php?id=<?= $relProd['id']; ?>">
                                        <img src="<?= e($relProd['main_image']); ?>" alt="<?= e($relProd['name']); ?>">
                                    </a>
                                </div>
                                <div class="product-info-box">
                                    <h4 class="product-title" style="font-size:15px;min-height:42px;">
                                        <a href="product-detail.php?id=<?= $relProd['id']; ?>"><?= e($relProd['name']); ?></a>
                                    </h4>
                                    <div class="product-price-row">
                                        <span class="price-current" style="font-size:18px;"><?= formatRupee($rPrice); ?></span>
                                    </div>
                                    <a href="product-detail.php?id=<?= $relProd['id']; ?>" class="btn btn-sm btn-outline-dark w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
