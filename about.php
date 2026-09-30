<?php
$pageTitle = "Our Heritage & Story | Vastra Mahal";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1>The Story of Vastra Mahal</h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <span>About Our Royal Heritage</span>
        </div>
    </div>
</div>

<!-- Main About Story -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:2px;">Our Royal Legacy</span>
                <h2 class="display-6 fw-bold text-dark mt-1 mb-3" style="font-family: var(--vm-font-title);">Crafting Indian Heritage with Timeless Opulence</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    Founded with a passion for preserving the majestic textile heritage of India, <strong>Vastra Mahal</strong> is a sanctuary of royal Indian ethnic couture. For generations, our master drapers and artisans have worked in close harmony with hereditary handloom weavers from the ancient ghats of Varanasi and the temple towns of Kanchipuram.
                </p>
                <p class="text-muted" style="line-height: 1.8;">
                    From pure silk Katan Banarasi sarees woven with authentic gold tested zari to hand-embroidered bridal lehengas, majestic Anarkali suits, and modern designer girl suits, every piece in our collection is an heirloom designed to be cherished across generations.
                </p>
                <div class="row g-3 pt-3">
                    <div class="col-6">
                        <div class="border-start border-3 border-danger ps-3">
                            <h3 class="fw-bold mb-0 text-dark">1,000+</h3>
                            <small class="text-muted">Curated Exclusive Designs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-start border-3 border-warning ps-3">
                            <h3 class="fw-bold mb-0 text-dark">100%</h3>
                            <small class="text-muted">Pure Silk & Authentic Weaves</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="position-relative rounded-3 overflow-hidden shadow-lg border" style="border-color: var(--vm-border) !important;">
                    <img src="images/banners/store-interior.jpg" alt="Vastra Mahal Showroom" class="img-fluid w-100" style="min-height: 420px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-75 text-white">
                        <h6 class="text-warning mb-0" style="font-family:var(--vm-font-title);">The Vastra Mahal Flagship Boutique</h6>
                        <small>Gandhi Market, Sagar Pur, New Delhi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Four Pillars of Vastra Mahal -->
        <div class="row g-4 my-4">
            <div class="col-md-3 col-sm-6">
                <div class="card border rounded-3 p-4 h-100 bg-white shadow-sm text-center">
                    <i class="fa-solid fa-gem text-danger fs-2 mb-3"></i>
                    <h5 class="fw-bold" style="font-family:var(--vm-font-title);">Authentic Silks</h5>
                    <p class="small text-muted mb-0">Every Banarasi and Kanjivaram silk saree carries certified authenticity with pure tested zari.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border rounded-3 p-4 h-100 bg-white shadow-sm text-center">
                    <i class="fa-solid fa-sparkles text-warning fs-2 mb-3"></i>
                    <h5 class="fw-bold" style="font-family:var(--vm-font-title);">Girl Suits Special</h5>
                    <p class="small text-muted mb-0">Exclusive trend-forward collection of pastel shararas, peplum sets, and Punjabi suits for young girls & teens.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border rounded-3 p-4 h-100 bg-white shadow-sm text-center">
                    <i class="fa-solid fa-scissors text-danger fs-2 mb-3"></i>
                    <h5 class="fw-bold" style="font-family:var(--vm-font-title);">Custom Tailoring</h5>
                    <p class="small text-muted mb-0">In-house master tailors for made-to-measure alterations, custom blouses, and personalised bridal styling.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card border rounded-3 p-4 h-100 bg-white shadow-sm text-center">
                    <i class="fa-solid fa-store text-success fs-2 mb-3"></i>
                    <h5 class="fw-bold" style="font-family:var(--vm-font-title);">In-Store Trial</h5>
                    <p class="small text-muted mb-0">Spacious private bridal suites and personal stylists ready to assist you at our Delhi showroom.</p>
                </div>
            </div>
        </div>

        <!-- Visit Us CTA -->
        <div class="card border-0 rounded-3 p-5 text-white shadow-lg mt-5 text-center" style="background: linear-gradient(135deg, #580B0D 0%, #7B1113 50%, #3D0507 100%); border: 2px solid var(--vm-gold) !important;">
            <div class="mx-auto" style="max-width: 650px;">
                <span class="hero-badge"><i class="fa-solid fa-location-dot me-1 text-warning"></i> Open 7 Days</span>
                <h2 class="display-6 fw-bold text-white mb-3" style="font-family: var(--vm-font-title);">Step into Our Royal Showroom</h2>
                <p class="text-light opacity-90 mb-4">
                    Located in Gandhi Market, Sagar Pur, New Delhi. Come visit us with your family to experience the textiles in person.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-royal" style="background:linear-gradient(135deg, var(--vm-gold) 0%, #9F792A 100%);color:#1A1A1A !important;border:none;">
                        <i class="fa-solid fa-diamond-turn-right me-1"></i> Get Shop Directions
                    </a>
                    <a href="contact.php" class="btn btn-outline-light px-4 py-2">
                        Contact Details & Inquiries
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
