<?php
$pageTitle = "Store QR Standee & Print";
require_once __DIR__ . '/includes/admin-header.php';

$storeAddress = getSetting($pdo, 'store_address', 'RZ K1A/272, Gandhi Market, West Sagar Pur, New Delhi - 110046');
$storePhone = getSetting($pdo, 'phone_number', '+91 96251 37860');
$googleMapUrl = getSetting($pdo, 'google_map_url', 'https://maps.app.goo.gl/73nDqtqFGqEFEmUf6');
?>

<style>
@media print {
    .admin-sidebar, .admin-topbar, .admin-footer, .no-print {
        display: none !important;
    }
    .admin-main-wrapper {
        margin-left: 0 !important;
    }
    .admin-content {
        padding: 0 !important;
    }
    .printable-standee {
        border: 4px solid #C89D4B !important;
        box-shadow: none !important;
        page-break-inside: avoid;
        margin: 20px auto !important;
    }
}
.printable-standee {
    max-width: 540px;
    background: #FFFDF9;
    border: 3px solid var(--vm-gold);
    border-radius: 16px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 16px 40px rgba(0,0,0,0.15);
    margin: 0 auto;
    position: relative;
}
.printable-standee::before {
    content: '';
    position: absolute;
    top: 8px;
    left: 8px;
    right: 8px;
    bottom: 8px;
    border: 1px dashed var(--vm-gold);
    border-radius: 12px;
    pointer-events: none;
}
</style>

<div class="card border-0 rounded-3 shadow-sm bg-white p-3 mb-4 no-print">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">Boutique Counter QR Standee</h5>
            <small class="text-muted">Print this display card for your boutique reception desk, trial rooms, and packaging.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-royal">
                <i class="fa-solid fa-print me-1"></i> Print Standee
            </button>
            <a href="../images/vastra-mahal-location-qr.png" download="Vastra-Mahal-Boutique-QR.png" class="btn btn-outline-dark">
                <i class="fa-solid fa-download me-1"></i> Download QR Image
            </a>
        </div>
    </div>
</div>

<!-- Standee Preview -->
<div class="py-4">
    <div class="printable-standee">
        <div class="vastra-brand-emblem mx-auto mb-3" style="width:60px;height:60px;font-size:24px;">
            <span>VM</span>
        </div>
        <h2 class="fw-bold text-dark mb-1" style="font-family: var(--vm-font-title); letter-spacing: 2px; color: var(--vm-maroon) !important;">
            VASTRA MAHAL
        </h2>
        <div class="text-warning text-uppercase fw-bold small mb-3" style="letter-spacing: 3px; font-size:11px;">
            Royal Heritage Ethnic Couture
        </div>

        <div class="my-4 p-3 bg-white border rounded-3 d-inline-block shadow-sm" style="border-color: var(--vm-gold) !important;">
            <img src="../images/vastra-mahal-location-qr.png" alt="Shop Location QR" class="img-fluid" style="width: 280px; height: 280px;">
        </div>

        <h4 class="fw-bold mb-2 text-dark" style="font-family: var(--vm-font-title);">SCAN TO EXPLORE & NAVIGATE</h4>
        <p class="text-muted small mb-3" style="max-width: 420px; margin: 0 auto; line-height: 1.6;">
            Scan with your mobile camera to view our complete catalog of <strong>Girl Suits, Banarasi Sarees, Designer Anarkalis & Lehengas</strong> or open instant Google Maps directions to this shop!
        </p>

        <div class="p-3 rounded-3 bg-light border text-start small mt-4" style="font-size:12px;">
            <div class="mb-1">
                <strong><i class="fa-solid fa-store text-danger me-1"></i> Showroom:</strong> <?= e($storeAddress); ?>
            </div>
            <div>
                <strong><i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp Orders & Inquiries:</strong> <?= e($storePhone); ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
