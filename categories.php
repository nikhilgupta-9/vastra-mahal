<?php
$pageTitle = "Explore Royal Collections & Categories | The Vastra Mahal";
require_once __DIR__ . '/includes/header.php';

// Fetch all categories with product counts
try {
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
                         WHERE c.status='active' 
                         GROUP BY c.id 
                         ORDER BY c.is_featured DESC, c.id ASC");
    $allCategories = $stmt->fetchAll();
} catch (Exception $e) {
    $allCategories = [];
}
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1>Royal Couture Collections</h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <span>Collections & Categories</span>
        </div>
    </div>
</div>

<!-- Main Categories Grid -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">Handcrafted Elegance</span>
            <h2 class="display-6 fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Browse By Category</h2>
            <div class="mx-auto" style="width: 70px; height: 3px; background-color: var(--vm-gold);"></div>
            <p class="text-muted mt-2" style="max-width: 600px; margin: 0 auto;">
                From vibrant girl partywear suits to royal bridal lehengas and certified pure Banarasi silks, explore our heritage collections.
            </p>
        </div>

        <div class="row g-4">
            <?php foreach ($allCategories as $cat): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="category-card-luxury">
                        <div class="category-img-wrapper">
                            <img src="<?= e($cat['image'] ?? 'images/categories/cat-banarasi.jpg'); ?>" alt="<?= e($cat['name']); ?>">
                        </div>
                        <div class="category-card-overlay">
                            <h4><?= e($cat['name']); ?></h4>
                            <p class="small text-light opacity-90 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <?= e($cat['description']); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light border-opacity-25">
                                <span class="category-count">
                                    <i class="fa-solid fa-sparkles me-1 text-warning"></i> <?= (int)$cat['product_count']; ?> Products Available
                                </span>
                                <a href="products.php?category=<?= e($cat['slug']); ?>" class="btn btn-sm btn-light py-1 px-3 text-dark fw-bold rounded-pill" style="font-size:12px;">
                                    Explore <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Boutique Invitation Callout -->
<section class="py-5" style="background-color: #FAF4EB; border-top: 1px solid var(--vm-border);">
    <div class="container text-center">
        <h3 class="fw-bold mb-3" style="font-family: var(--vm-font-title); color: var(--vm-maroon);">Looking for Something Bespoke?</h3>
        <p class="text-muted mx-auto mb-4" style="max-width: 650px;">
            Visit The Vastra Mahal boutique in Gandhi Market, Sagar Pur, New Delhi or connect directly with our master drapers on WhatsApp for custom color combinations, bridal customization, and size tailoring.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="contact.php" class="btn btn-royal">
                <i class="fa-solid fa-store me-1"></i> Visit Boutique
            </a>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>" target="_blank" class="btn btn-whatsapp">
                <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
