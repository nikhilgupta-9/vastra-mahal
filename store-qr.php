<?php
$pageTitle = "Store QR & Digital Catalog | Vastra Mahal";
require_once __DIR__ . '/includes/header.php';

// Fetch all active categories
try {
    $cStmt = $pdo->query("SELECT c.*, COUNT(p.id) as p_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
                         WHERE c.status='active' 
                         GROUP BY c.id 
                         ORDER BY c.id ASC");
    $qrCategories = $cStmt->fetchAll();
} catch (Exception $e) {
    $qrCategories = [];
}
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1>Vastra Mahal Digital QR Hub</h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <span>Digital QR Catalog & Store Navigation</span>
        </div>
    </div>
</div>

<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Main QR Card -->
                <div class="card border rounded-4 shadow-lg p-4 p-md-5 bg-white text-center mb-5" style="border-color: var(--vm-border) !important;">
                    <div class="d-inline-flex align-items-center gap-2 mb-3">
                        <div class="vastra-brand-emblem" style="width:42px;height:42px;font-size:18px;">
                            <span>VM</span>
                        </div>
                        <h3 class="mb-0 text-dark fw-bold" style="font-family: var(--vm-font-title);">Vastra Mahal</h3>
                    </div>
                    <span class="badge bg-danger text-uppercase px-3 py-2 mb-3 align-self-center" style="letter-spacing:1.5px;">Boutique QR Pass & Catalog</span>

                    <h4 class="fw-bold mb-2" style="font-family: var(--vm-font-title); color: var(--vm-maroon);">Scan To Navigate & Explore Collections</h4>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 500px;">
                        Point your mobile camera to scan this QR code. It will open instant GPS directions directly to our boutique in Gandhi Market, Sagar Pur, New Delhi!
                    </p>

                    <!-- QR Code Display Box -->
                    <div class="p-3 border rounded-3 d-inline-block shadow-sm mb-4" style="background:#FFFDF9; border-color: var(--vm-gold) !important;">
                        <img src="images/vastra-mahal-location-qr.png" alt="Vastra Mahal Boutique QR Code" class="img-fluid rounded" style="width: 260px; height: 260px;">
                        <div class="mt-2 small text-muted fw-semibold">
                            <i class="fa-solid fa-map-pin text-danger me-1"></i> Shop GPS Location Attached
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                        <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-royal py-2 px-4 shadow">
                            <i class="fa-solid fa-diamond-turn-right me-2"></i> Open In Google Maps App
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>" target="_blank" class="btn btn-whatsapp py-2 px-4 shadow">
                            <i class="fa-brands fa-whatsapp me-2"></i> Chat with Boutique
                        </a>
                        <a href="images/vastra-mahal-location-qr.png" download="Vastra-Mahal-Shop-QR.png" class="btn btn-outline-dark py-2 px-3">
                            <i class="fa-solid fa-download me-1"></i> Download QR Image
                        </a>
                    </div>

                    <div class="p-3 rounded-3 bg-light border text-start small">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <strong><i class="fa-solid fa-location-dot text-danger me-1"></i> Store Address:</strong><br>
                                <?= e($storeAddress); ?>
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fa-solid fa-clock text-warning me-1"></i> Operating Hours:</strong><br>
                                <?= e($storeTimings); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Digital Category Directory for QR Scanners -->
                <div class="text-center mb-4">
                    <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:1.5px;">Instant Catalog</span>
                    <h3 class="fw-bold text-dark mt-1" style="font-family: var(--vm-font-title);">Browse Store Catalog By Category</h3>
                    <div class="mx-auto" style="width: 50px; height: 3px; background-color: var(--vm-gold);"></div>
                </div>

                <div class="row g-3">
                    <?php foreach ($qrCategories as $qCat): ?>
                        <div class="col-sm-6">
                            <a href="products.php?category=<?= e($qCat['slug']); ?>" class="card p-3 border text-decoration-none shadow-sm h-100 bg-white" style="border-left: 4px solid var(--vm-maroon) !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><?= e($qCat['name']); ?></h6>
                                        <small class="text-muted"><?= (int)$qCat['p_count']; ?> Listed Items</small>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-danger"></i>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
