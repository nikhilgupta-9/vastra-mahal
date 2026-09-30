<?php
$pageTitle = "Royal Heritage Ethnic Couture | Suits, Sarees & Lehengas";
require_once __DIR__ . '/includes/header.php';

// Fetch Featured Categories
try {
    $catStmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                           FROM categories c 
                           LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
                           WHERE c.status='active' 
                           GROUP BY c.id 
                           ORDER BY c.is_featured DESC, c.id ASC");
    $categories = $catStmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

// Fetch Featured / New Arrivals Products
try {
    $prodStmt = $pdo->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            WHERE p.status='active' 
                            ORDER BY p.is_featured DESC, p.id DESC 
                            LIMIT 8");
    $featuredProducts = $prodStmt->fetchAll();
} catch (Exception $e) {
    $featuredProducts = [];
}

// Fetch Girl Suits
try {
    $girlSuitStmt = $pdo->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
                                FROM products p 
                                JOIN categories c ON p.category_id = c.id 
                                WHERE p.status='active' AND c.slug='girl-suits' 
                                ORDER BY p.id DESC 
                                LIMIT 4");
    $girlSuits = $girlSuitStmt->fetchAll();
} catch (Exception $e) {
    $girlSuits = [];
}
?>

<!-- Hero Slider Section Start -->
<section class="hero-section py-4">
    <div class="container">
        <div id="vastraHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#vastraHeroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#vastraHeroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#vastraHeroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner shadow-lg" style="border-radius: 14px;">
                <!-- Slide 1: Sarees -->
                <div class="carousel-item active" data-bs-interval="5000">
                    <div class="hero-slider-item" style="background-image: url('images/banners/hero-saree.jpg');">
                        <div class="hero-slider-overlay"></div>
                        <div class="hero-content-box">
                            <span class="hero-badge"><i class="fa-solid fa-crown me-1 text-warning"></i> Royal Heritage Collection</span>
                            <h1>Pure Banarasi & Kanjivaram Silks</h1>
                            <p>Handwoven by hereditary master artisans with pure gold & silver zari. Experience the unmatched majesty of authentic Indian silk heritage.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="products.php?category=banarasi-sarees" class="btn btn-royal">
                                    <i class="fa-solid fa-gem me-1"></i> Explore Sarees
                                </a>
                                <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-outline-light px-4 py-2" style="border-width:1.5px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
                                    <i class="fa-solid fa-store me-1"></i> Visit Boutique
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Designer Suits & Anarkali -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slider-item" style="background-image: url('images/banners/hero-anarkali.jpg');">
                        <div class="hero-slider-overlay"></div>
                        <div class="hero-content-box">
                            <span class="hero-badge"><i class="fa-solid fa-sparkles me-1 text-warning"></i> Haute Couture & Festivities</span>
                            <h1>Majestic Anarkalis & Designer Suits</h1>
                            <p>Flared silhouettes, zardozi embroidery, and timeless royal hues designed to make you the center of celebration.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="products.php?category=designer-anarkali-suits" class="btn btn-royal">
                                    <i class="fa-solid fa-vest-patches me-1"></i> View Suit Collection
                                </a>
                                <a href="contact.php" class="btn btn-outline-light px-4 py-2" style="border-width:1.5px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
                                    <i class="fa-solid fa-scissors me-1"></i> Custom Stitching
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Girl Suits & Partywear -->
                <div class="carousel-item" data-bs-interval="5000">
                    <div class="hero-slider-item" style="background-image: url('images/banners/store-interior.jpg');">
                        <div class="hero-slider-overlay"></div>
                        <div class="hero-content-box">
                            <span class="hero-badge"><i class="fa-solid fa-heart me-1 text-warning"></i> Trending Girls & Teens Wear</span>
                            <h1>Designer Girl Suits & Sharara Sets</h1>
                            <p>Pastel mirror-work shararas, vibrant Punjabi suits, and tiered peplum sets tailored specifically for festive and wedding youth wear.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="products.php?category=girl-suits" class="btn btn-royal">
                                    <i class="fa-solid fa-sparkles me-1"></i> Shop Girl Suits
                                </a>
                                <a href="store-qr.php" class="btn btn-outline-light px-4 py-2" style="border-width:1.5px;font-weight:600;letter-spacing:1px;text-transform:uppercase;">
                                    <i class="fa-solid fa-qrcode me-1"></i> Scan Digital Catalog
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#vastraHeroCarousel" data-bs-slide="prev" aria-label="Previous Slide">
                <span class="carousel-nav-btn"><i class="fa-solid fa-chevron-left"></i></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#vastraHeroCarousel" data-bs-slide="next" aria-label="Next Slide">
                <span class="carousel-nav-btn"><i class="fa-solid fa-chevron-right"></i></span>
            </button>
        </div>
    </div>
</section>
<!-- Hero Slider Section End -->

<!-- Key Features Bar -->
<section class="py-4 border-top border-bottom bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fa-solid fa-certificate text-danger fs-2"></i>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">100% Pure Silk</h6>
                        <small class="text-muted">Handloom certified weaves</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fa-solid fa-store text-danger fs-2"></i>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Boutique Trial</h6>
                        <small class="text-muted">Try in store in Janakpuri</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fa-solid fa-scissors text-danger fs-2"></i>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Custom Fitting</h6>
                        <small class="text-muted">Made-to-measure tailoring</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <i class="fa-brands fa-whatsapp text-success fs-2"></i>
                    <div class="text-start">
                        <h6 class="mb-0 fw-bold">Instant Support</h6>
                        <small class="text-muted">Live video shopping & chat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Showcase Section -->
<section class="py-5" style="background-color: #FAF6F0;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">Royal Selections</span>
            <h2 class="display-6 fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Explore Our Collections</h2>
            <div class="mx-auto" style="width: 70px; height: 3px; background-color: var(--vm-gold);"></div>
            <p class="text-muted mt-2" style="max-width: 600px; margin: 0 auto;">Discover handcrafted bridal lehengas, authentic Banarasi sarees, designer Anarkalis, and trendy festive suits for girls.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="products.php?category=<?= e($cat['slug']); ?>" class="text-decoration-none">
                        <div class="category-card-luxury">
                            <div class="category-img-wrapper">
                                <img src="<?= e($cat['image'] ?? 'images/categories/cat-banarasi.jpg'); ?>" alt="<?= e($cat['name']); ?>">
                            </div>
                            <div class="category-card-overlay">
                                <h4><?= e($cat['name']); ?></h4>
                                <span class="category-count">
                                    <i class="fa-solid fa-sparkles me-1 text-warning"></i> <?= (int)$cat['product_count']; ?> Designs Listed
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trending & Featured Products Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">Curated For You</span>
                <h2 class="display-6 fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Featured Creations</h2>
                <div style="width: 60px; height: 3px; background-color: var(--vm-gold);"></div>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="products.php" class="btn btn-royal-outline">
                    View All Products <i class="fa-solid fa-arrow-right-long ms-1"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredProducts as $prod): 
                $hasDiscount = !empty($prod['sale_price']) && $prod['sale_price'] < $prod['price'];
                $discountPercent = $hasDiscount ? round((($prod['price'] - $prod['sale_price']) / $prod['price']) * 100) : 0;
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                    <div class="product-card-luxury">
                        <div class="product-img-box">
                            <a href="product-detail.php?id=<?= $prod['id']; ?>">
                                <img src="<?= e($prod['main_image']); ?>" alt="<?= e($prod['name']); ?>">
                            </a>
                            <div class="product-badge-group">
                                <?php if ($hasDiscount): ?>
                                    <span class="badge-luxury-sale"><?= $discountPercent; ?>% OFF</span>
                                <?php endif; ?>
                                <?php if ($prod['is_featured']): ?>
                                    <span class="badge-luxury-gold"><i class="fa-solid fa-crown me-1"></i> Royal Pick</span>
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
                            </div>

                            <div class="product-price-row">
                                <span class="price-current">
                                    <?= formatRupee($hasDiscount ? $prod['sale_price'] : $prod['price']); ?>
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
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=<?= urlencode("Hello Vastra Mahal! I am interested in " . $prod['name'] . " (" . formatRupee($hasDiscount ? $prod['sale_price'] : $prod['price']) . "). Is this available in store?"); ?>" target="_blank" class="btn btn-whatsapp" title="Inquire on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i> Inquire
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SPECIAL FEATURED SECTION: GIRL SUITS & PARTYWEAR -->
<section class="py-5" style="background: linear-gradient(135deg, #FFF9F5 0%, #FAF0E6 100%); border-top: 1px solid var(--vm-border); border-bottom: 1px solid var(--vm-border);">
    <div class="container">
        <div class="row align-items-center g-4 mb-4">
            <div class="col-md-8">
                <span class="badge bg-danger text-uppercase px-3 py-2 mb-2" style="letter-spacing:1px;">Trending for Young Women & Teens</span>
                <h2 class="display-6 fw-bold text-dark" style="font-family: var(--vm-font-title);">Girl Suits, Shararas & Punjabi Suits</h2>
                <p class="text-muted mb-0">Crafted for sangeet, weddings, festivals and celebrations. Premium fabrics, comfortable silhouettes, and vibrant authentic hand-embroidery.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="products.php?category=girl-suits" class="btn btn-royal">
                    View Entire Girl Suits Collection <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($girlSuits as $gSuit): 
                $gHasDiscount = !empty($gSuit['sale_price']) && $gSuit['sale_price'] < $gSuit['price'];
            ?>
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="product-card-luxury">
                        <div class="product-img-box">
                            <a href="product-detail.php?id=<?= $gSuit['id']; ?>">
                                <img src="<?= e($gSuit['main_image']); ?>" alt="<?= e($gSuit['name']); ?>">
                            </a>
                            <div class="product-badge-group">
                                <span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-sparkles"></i> Girl Suit</span>
                            </div>
                        </div>
                        <div class="product-info-box">
                            <h3 class="product-title">
                                <a href="product-detail.php?id=<?= $gSuit['id']; ?>"><?= e($gSuit['name']); ?></a>
                            </h3>
                            <div class="product-specs-chips">
                                <span class="spec-chip"><i class="fa-solid fa-tag me-1"></i> <?= e($gSuit['color']); ?></span>
                            </div>
                            <div class="product-price-row">
                                <span class="price-current">
                                    <?= formatRupee($gHasDiscount ? $gSuit['sale_price'] : $gSuit['price']); ?>
                                </span>
                                <?php if ($gHasDiscount): ?>
                                    <span class="price-original"><?= formatRupee($gSuit['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-action-row">
                                <a href="product-detail.php?id=<?= $gSuit['id']; ?>" class="btn btn-outline-dark">
                                    View
                                </a>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=<?= urlencode("Hi Vastra Mahal, I like the Girl Suit: " . $gSuit['name'] . ". Please share size availability!"); ?>" target="_blank" class="btn btn-whatsapp">
                                    <i class="fa-brands fa-whatsapp"></i> Chat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- STORE VISIT & QR SCANNER SECTION (CLIENT CORE DEMAND) -->
<section class="store-qr-section">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center g-5">
            <!-- Left: Store Experience Information -->
            <div class="col-lg-7">
                <span class="hero-badge"><i class="fa-solid fa-shop me-1 text-warning"></i> Visit Vastra Mahal In Person</span>
                <h2 class="display-5 fw-bold text-white mb-3" style="font-family: var(--vm-font-title);">Experience Royal Couture at Our Store</h2>
                <p class="text-light opacity-90 fs-5 mb-4" style="line-height: 1.7;">
                    Touch the richness of authentic pure Katan Banarasi silk, try on custom-tailored bridal lehengas, and explore exclusive girl suits in person with our fashion consultants.
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(200,157,75,0.4);">
                            <i class="fa-solid fa-map-location-dot fs-3 text-warning mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Store Address</h6>
                                <p class="small text-light opacity-80 mb-0"><?= e($storeAddress); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(200,157,75,0.4);">
                            <i class="fa-solid fa-clock-rotate-left fs-3 text-warning mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Open 7 Days a Week</h6>
                                <p class="small text-light opacity-80 mb-0"><?= e($storeTimings); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-royal" style="background: linear-gradient(135deg, var(--vm-gold) 0%, #9F792A 100%); color:#1A1A1A !important; border:none;">
                        <i class="fa-solid fa-diamond-turn-right me-1"></i> Open Google Maps Directions
                    </a>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=Hello+Vastra+Mahal%2C+I+want+to+book+an+in-store+styling+appointment" target="_blank" class="btn btn-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i> Book In-Store Trial
                    </a>
                </div>
            </div>

            <!-- Right: Interactive QR Code Card -->
            <div class="col-lg-5 text-center">
                <div class="qr-card-box">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <div class="vastra-brand-emblem" style="width:32px;height:32px;font-size:14px;">
                            <span>VM</span>
                        </div>
                        <h5 class="mb-0 text-dark" style="font-family: var(--vm-font-title);">Store QR Code</h5>
                    </div>
                    <p class="text-muted small mb-3">Scan with your smartphone camera to navigate directly to our boutique or browse our digital catalogue!</p>
                    
                    <div class="p-2 border rounded shadow-sm d-inline-block bg-white mb-3">
                        <img src="images/vastra-mahal-location-qr.png" alt="Scan Vastra Mahal Store QR" class="img-fluid d-block mx-auto" style="width: 220px; height: 220px;">
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-danger px-3">
                            <i class="fa-solid fa-location-arrow me-1"></i> Navigate Now
                        </a>
                        <a href="images/vastra-mahal-location-qr.png" download="Vastra-Mahal-QR.png" class="btn btn-sm btn-outline-dark px-3">
                            <i class="fa-solid fa-download me-1"></i> Save QR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Boutique Showroom Gallery Preview -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="position-relative rounded-3 overflow-hidden shadow-lg border" style="border-color: var(--vm-border) !important;">
                    <img src="images/banners/store-interior.jpg" alt="Vastra Mahal Showroom Interior" class="img-fluid w-100" style="min-height: 380px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-75 text-white">
                        <h5 class="mb-0 text-warning" style="font-family: var(--vm-font-title);">Vastra Mahal Boutique Showroom</h5>
                        <small>Heritage Arcade, Janakpuri / Uttam Nagar, New Delhi</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">A Century of Artistry</span>
                <h2 class="display-6 fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Where Royalty Meets Contemporary Fashion</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    Founded on the belief that traditional Indian craftsmanship is an irreplaceable luxury, Vastra Mahal houses an exquisite curation of hand-embroidered wedding wear, pure handloom silks from Varanasi and Kanchipuram, and modern ready-to-wear suits for girls and women.
                </p>
                <div class="d-flex flex-column gap-2 mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span>Over 1,000+ curated ethnic designs in store</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span>Dedicated Girl Suits & Sharara section for kids & teens</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <span>Master tailors for custom necklines, sleeve styling & fits</span>
                    </div>
                </div>
                <a href="about.php" class="btn btn-royal">
                    Read Our Full Story <i class="fa-solid fa-arrow-right-long ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
