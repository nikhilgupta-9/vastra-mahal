<?php
require_once __DIR__ . '/../config/db.php';

// Active page helper
$currentPage = basename($_SERVER['PHP_SELF']);
$pageCategory = $_GET['category'] ?? '';

$storePhone = getSetting($pdo, 'phone_number', '+91 96251 37860');
$storeWhatsapp = getSetting($pdo, 'whatsapp_number', '+919625137860');
$storeEmail = getSetting($pdo, 'store_email', 'thevastramahal60@gmail.com');
$storeAddress = getSetting($pdo, 'store_address', 'Gandhi Market, Sagar Pur, New Delhi');
$googleMapUrl = getSetting($pdo, 'google_map_url', 'https://share.google/4X3xcWrgXxZ754XWa');
$storeTimings = getSetting($pdo, 'store_timings', 'Mon - Sun: 10:30 AM to 9:00 PM');
$instagramUrl = getSetting($pdo, 'instagram_url', 'https://instagram.com/thevastramahal');
$facebookUrl = getSetting($pdo, 'facebook_url', 'https://facebook.com/thevastramahal');

// Fetch active categories for dropdown
try {
    $headerCatStmt = $pdo->query("SELECT id, name, slug FROM categories WHERE status='active' ORDER BY is_featured DESC, id ASC");
    $navCategories = $headerCatStmt->fetchAll();
} catch (Exception $e) {
    $navCategories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | The Vastra Mahal - Royal Heritage Ethnic Couture' : 'The Vastra Mahal - Royal Heritage Suits, Sarees & Ethnic Couture'; ?></title>
    
    <!-- Favicon Icon -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png">
    
    <!-- Google Fonts & Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- Font Awesome (Local & CDN Fallback) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/all.min.css" rel="stylesheet" media="screen">
    <link href="css/animate.css" rel="stylesheet">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    
    <!-- Main Template & Luxury Enhancements -->
    <link href="css/custom.css" rel="stylesheet" media="screen">
    <link href="css/vastra-mahal-luxury.css" rel="stylesheet" media="screen">
</head>
<body>

    <!-- Topbar Section Start -->
    <div class="topbar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-7 d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                        <span><i class="fa-solid fa-store me-1 text-warning"></i> <strong>Store:</strong> Gandhi Market, Sagar Pur, New Delhi</span>
                        <span>|</span>
                        <a href="<?= e($googleMapUrl); ?>" target="_blank" class="text-decoration-none">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i> Get Shop Directions
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5 text-md-end text-center">
                    <div class="d-inline-flex align-items-center gap-3">
                        <span><i class="fa-regular fa-clock me-1"></i> 10:30 AM - 9:00 PM</span>
                        <span>|</span>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>?text=Hello+The+Vastra+Mahal%2C+I+want+to+know+more+about+your+collection" target="_blank" class="text-decoration-none">
                            <i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp: <?= e($storePhone); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar Section End -->

    <!-- Main Header & Navbar -->
    <header class="main-header sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="container">
                <!-- Brand Logo -->
                <a class="vastra-brand" href="index.php">
                    <div class="vastra-brand-emblem">
                        <span>TVM</span>
                    </div>
                    <div class="vastra-brand-text">
                        <span class="vastra-brand-name">The Vastra Mahal</span>
                        <span class="vastra-brand-tagline">Royal Heritage Couture</span>
                    </div>
                </a>

                <!-- Mobile Hamburger Button -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#vastraNavbar" aria-controls="vastraNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="vastraNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'index.php' ? 'active text-danger fw-bold' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($pageCategory === 'girl-suits') ? 'active text-danger fw-bold' : ''; ?>" href="products.php?category=girl-suits">
                                <span class="badge bg-warning text-dark me-1">Hot</span> Girl Suits
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['categories.php']) ? 'active text-danger fw-bold' : ''; ?>" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu border-0 shadow" aria-labelledby="categoriesDropdown">
                                <li><a class="dropdown-item fw-semibold" href="categories.php"><i class="fa-solid fa-grid-2 me-2"></i> View All Categories</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach ($navCategories as $navCat): ?>
                                    <li><a class="dropdown-item" href="products.php?category=<?= e($navCat['slug']); ?>"><?= e($navCat['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($currentPage === 'products.php' && empty($pageCategory)) ? 'active text-danger fw-bold' : ''; ?>" href="products.php">All Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'about.php' ? 'active text-danger fw-bold' : ''; ?>" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentPage === 'contact.php' ? 'active text-danger fw-bold' : ''; ?>" href="contact.php">Contact & Location</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-primary fw-semibold <?= $currentPage === 'store-qr.php' ? 'active' : ''; ?>" href="store-qr.php">
                                <i class="fa-solid fa-qrcode text-danger me-1"></i> Shop QR
                            </a>
                        </li>
                    </ul>

                    <!-- Header CTA Right -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-royal-outline d-none d-xl-inline-flex align-items-center" title="Open Store Location in Google Maps">
                            <i class="fa-solid fa-map-location-dot me-1"></i> Visit Shop
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>" target="_blank" class="btn btn-sm btn-whatsapp" title="Chat on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i> Chat
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
