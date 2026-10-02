<?php
require_once __DIR__ . '/config/db.php';

$successMessage = '';
$errorMessage = '';

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Website Inquiry');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($message)) {
        $errorMessage = 'Please provide your Name, Phone Number, and Message.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please provide a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $successMessage = "Thank you, {$name}! Your inquiry has been sent to our boutique team. We will call or WhatsApp you shortly.";
        } catch (Exception $e) {
            $errorMessage = "Sorry, could not send your message right now. Please WhatsApp us directly.";
        }
    }
}

$pageTitle = "Contact & Boutique Location | The Vastra Mahal";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="luxury-page-header">
    <div class="container">
        <h1>Visit Our Store & Connect</h1>
        <div class="luxury-breadcrumb">
            <a href="index.php">Home</a>
            <span><i class="fa-solid fa-angle-right" style="font-size:11px;"></i></span>
            <span>Contact & Shop Location</span>
        </div>
    </div>
</div>

<!-- Main Contact Section -->
<section class="pb-5">
    <div class="container">
        <!-- Alerts -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= e($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= e($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 g-lg-5">
            <!-- Left: Contact Form -->
            <div class="col-lg-6">
                <div class="card border rounded-3 p-4 shadow-sm bg-white" style="border-color: var(--vm-border) !important;">
                    <span class="text-danger fw-bold text-uppercase small" style="letter-spacing:1px;">Send Us A Message</span>
                    <h3 class="fw-bold mb-3 mt-1" style="font-family: var(--vm-font-title); color: var(--vm-maroon);">Product Inquiry & Custom Orders</h3>
                    <p class="text-muted small mb-4">Have questions about a saree, bridal lehenga, or girl suit? Fill the form below or message our store manager directly.</p>

                    <form action="contact.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Your Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Priya Sharma" required value="<?= e($_POST['name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">WhatsApp / Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="e.g. 9625137860" required value="<?= e($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. priya@example.com" value="<?= e($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Subject / Category Interested In</label>
                                <select name="subject" class="form-select">
                                    <option value="General Inquiry">General Product Inquiry</option>
                                    <option value="Girl Suits & Shararas">Girl Suits & Partywear</option>
                                    <option value="Banarasi Silk Sarees">Banarasi Silk Sarees</option>
                                    <option value="Bridal Lehengas">Bridal Lehengas</option>
                                    <option value="Custom Stitching / Made to Measure">Custom Stitching & Tailoring</option>
                                    <option value="In-Store Boutique Trial Booking">In-Store Trial Booking</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Your Message / Design Requirements <span class="text-danger">*</span></label>
                                <textarea name="message" rows="4" class="form-control" placeholder="Tell us about the design, size, or occasion you are shopping for..." required><?= e($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-royal w-100 py-3 justify-content-center">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Store Location, QR Code & Details -->
            <div class="col-lg-6">
                <!-- Location & Contact Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                            <i class="fa-solid fa-store fs-3 text-danger mb-2"></i>
                            <h6 class="fw-bold mb-1">Our Boutique Address</h6>
                            <p class="small text-muted mb-0"><?= e($storeAddress); ?></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                            <i class="fa-regular fa-clock fs-3 text-warning mb-2"></i>
                            <h6 class="fw-bold mb-1">Store Timings</h6>
                            <p class="small text-muted mb-1"><?= e($storeTimings); ?></p>
                            <span class="badge bg-success-subtle text-success border border-success">Open Today</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                            <i class="fa-brands fa-whatsapp fs-3 text-success mb-2"></i>
                            <h6 class="fw-bold mb-1">WhatsApp & Call</h6>
                            <p class="small text-muted mb-2"><?= e($storePhone); ?></p>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $storeWhatsapp); ?>" target="_blank" class="btn btn-sm btn-success py-1 px-3">
                                <i class="fa-brands fa-whatsapp me-1"></i> Start Chat
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 border bg-white shadow-sm h-100">
                            <i class="fa-regular fa-envelope fs-3 text-primary mb-2"></i>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="small text-muted mb-0"><?= e($storeEmail); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Store QR Code Highlight Box (Client Key Request!) -->
                <div class="card border-0 rounded-3 p-4 text-white shadow" style="background: linear-gradient(135deg, #4A080A 0%, #680E10 50%, #2A0406 100%); border: 1.5px solid var(--vm-gold) !important;">
                    <div class="row align-items-center g-3">
                        <div class="col-sm-4 text-center">
                            <img src="images/vastra-mahal-location-qr.png" alt="Scan Shop Location" class="img-fluid rounded border border-warning p-1 bg-white" style="max-width:140px;">
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-warning text-dark fw-bold mb-2">Scan & Navigate</span>
                            <h5 class="fw-bold text-white mb-2" style="font-family: var(--vm-font-title);">Find Our Boutique in GPS</h5>
                            <p class="small text-light opacity-90 mb-3">Scan this QR code with any camera phone to open Google Maps navigation directly to The Vastra Mahal!</p>
                            <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-light text-danger fw-bold">
                                <i class="fa-solid fa-diamond-turn-right me-1"></i> Open in Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Maps Embed Section (Client Location Link) -->
        <div class="mt-5">
            <div class="card border rounded-3 overflow-hidden shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold" style="font-family: var(--vm-font-title);"><i class="fa-solid fa-map-location-dot text-danger me-2"></i> The Vastra Mahal Location Map</h5>
                        <small class="text-muted"><?= e($storeAddress); ?></small>
                    </div>
                    <a href="<?= e($googleMapUrl); ?>" target="_blank" class="btn btn-sm btn-royal-outline">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Full Map
                    </a>
                </div>
                <div class="ratio ratio-21x9" style="min-height: 380px;">
                    <iframe src="https://maps.google.com/maps?q=Gandhi+Market,+West+Sagar+Pur,+New+Delhi+110046&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=&amp;output=embed" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
