<?php
$storePhone = getSetting($pdo, 'phone_number', '+91 96251 37860');
$storeWhatsapp = getSetting($pdo, 'whatsapp_number', '+919625137860');
$storeEmail = getSetting($pdo, 'store_email', 'thevastramahal60@gmail.com');
$storeAddress = getSetting($pdo, 'store_address', 'RZ K1A/272, Gandhi Market, West Sagar Pur, New Delhi - 110046');
$googleMapUrl = getSetting($pdo, 'google_map_url', 'https://share.google/4X3xcWrgXxZ754XWa');
$storeTimings = getSetting($pdo, 'store_timings', 'Mon - Sun: 10:30 AM to 9:00 PM');
$cleanWhatsapp = preg_replace('/[^0-9]/', '', $storeWhatsapp);
?>
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/<?= $cleanWhatsapp; ?>?text=Hello+The+Vastra+Mahal%2C+I+am+interested+in+your+collection" target="_blank" class="floating-whatsapp" title="Chat on WhatsApp with The Vastra Mahal">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Footer Section Start -->
    <footer class="luxury-footer text-light">
        <div class="container pb-5">
            <div class="row g-4">
                <!-- Col 1: About Brand -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="vastra-brand-emblem" style="width:40px;height:40px;font-size:14px;">
                            <span>TVM</span>
                        </div>
                        <h4 class="mb-0 text-white" style="letter-spacing:1px;">The Vastra Mahal</h4>
                    </div>
                    <p class="text-secondary small" style="line-height:1.7;">
                        Celebrating the timeless grandeur of Indian ethnic couture. We bring you handpicked Banarasi silks, opulent bridal lehengas, royal Anarkalis, and trendy girl suits crafted with authentic heritage artistry and modern flair.
                    </p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="https://wa.me/<?= $cleanWhatsapp; ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp text-success"></i>
                        </a>
                        <a href="<?= e($instagramUrl ?? 'https://instagram.com/thevastramahal'); ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Instagram">
                            <i class="fa-brands fa-instagram text-danger"></i>
                        </a>
                        <a href="<?= e($facebookUrl ?? 'https://facebook.com/thevastramahal'); ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-primary"></i>
                        </a>
                        <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;" title="Google Maps">
                            <i class="fa-solid fa-location-dot text-warning"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h5>Explore</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="index.php"><i class="fa-solid fa-angle-right me-1 text-warning"></i> Home</a></li>
                        <li><a href="products.php?category=girl-suits"><i class="fa-solid fa-angle-right me-1 text-warning"></i> Girl Suits</a></li>
                        <li><a href="categories.php"><i class="fa-solid fa-angle-right me-1 text-warning"></i> Categories</a></li>
                        <li><a href="products.php"><i class="fa-solid fa-angle-right me-1 text-warning"></i> All Products</a></li>
                        <li><a href="about.php"><i class="fa-solid fa-angle-right me-1 text-warning"></i> About Heritage</a></li>
                        <li><a href="contact.php"><i class="fa-solid fa-angle-right me-1 text-warning"></i> Contact Us</a></li>
                        <li><a href="store-qr.php"><i class="fa-solid fa-qrcode me-1 text-warning"></i> Digital QR Code</a></li>
                    </ul>
                </div>

                <!-- Col 3: Popular Categories -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h5>Collections</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="products.php?category=girl-suits"><i class="fa-solid fa-gem me-1 text-warning"></i> Girl Partywear Suits</a></li>
                        <li><a href="products.php?category=banarasi-sarees"><i class="fa-solid fa-gem me-1 text-warning"></i> Pure Banarasi Sarees</a></li>
                        <li><a href="products.php?category=designer-anarkali-suits"><i class="fa-solid fa-gem me-1 text-warning"></i> Designer Anarkali Sets</a></li>
                        <li><a href="products.php?category=bridal-lehengas"><i class="fa-solid fa-gem me-1 text-warning"></i> Royal Bridal Lehengas</a></li>
                        <li><a href="products.php?category=kanjivaram-sarees"><i class="fa-solid fa-gem me-1 text-warning"></i> Kanjivaram Silk Sarees</a></li>
                        <li><a href="products.php?category=designer-kurtis"><i class="fa-solid fa-gem me-1 text-warning"></i> Chanderi Silk Kurtis</a></li>
                    </ul>
                </div>

                <!-- Col 4: Store Location & QR Code -->
                <div class="col-lg-3 col-md-6">
                    <h5>Visit Our Store</h5>
                    <p class="small text-secondary mb-2">
                        <i class="fa-solid fa-location-dot text-danger me-2"></i>
                        <?= e($storeAddress); ?>
                    </p>
                    <p class="small text-secondary mb-2">
                        <i class="fa-regular fa-clock text-warning me-2"></i>
                        <?= e($storeTimings); ?>
                    </p>
                    <p class="small text-secondary mb-2">
                        <i class="fa-solid fa-phone text-success me-2"></i>
                        <a href="tel:<?= e($storePhone); ?>" class="text-light"><?= e($storePhone); ?></a>
                    </p>
                    <p class="small text-secondary mb-3">
                        <i class="fa-solid fa-envelope text-warning me-2"></i>
                        <a href="mailto:<?= e($storeEmail); ?>" class="text-light"><?= e($storeEmail); ?></a>
                    </p>
                    
                    <div class="d-flex align-items-center gap-3 p-2 rounded" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(200,157,75,0.3);">
                        <img src="images/vastra-mahal-location-qr.png" alt="Scan Shop Location" class="img-fluid rounded" style="width:70px;height:70px;background:#FFF;padding:4px;">
                        <div>
                            <span class="d-block small text-warning fw-semibold">Scan with Mobile</span>
                            <span class="d-block text-secondary" style="font-size:11px;">Direct Shop GPS Navigation</span>
                            <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-danger py-1 px-2 mt-1" style="font-size:11px;">
                                <i class="fa-solid fa-diamond-turn-right me-1"></i> Open Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subfooter -->
        <div class="py-3 border-top" style="border-color: rgba(255,255,255,0.1) !important; background: #0E0708;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <span class="small text-secondary">
                            &copy; <?= date('Y'); ?> <strong>The Vastra Mahal</strong>. All Rights Reserved. Indian Royal Ethnic Couture.
                        </span>
                    </div>
                    <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                        <span class="small text-secondary">
                            Developed by <a href="https://nikhilworks.com" target="_blank" rel="noopener noreferrer" class="text-warning text-decoration-none fw-semibold">Nikhil Works</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Scripts -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        if (typeof bootstrap === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"><\/script>');
        }
    </script>
    <script src="js/swiper-bundle.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
