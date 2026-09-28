<?php
$pageTitle = "Dashboard Overview";
require_once __DIR__ . '/includes/admin-header.php';

// Fetch stats
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$totalGirlSuits = (int)$pdo->query("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id=c.id WHERE c.slug='girl-suits' AND p.status='active'")->fetchColumn();
$totalCategories = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE status='active'")->fetchColumn();
$totalInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();
$unreadInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read=0")->fetchColumn();

// Fetch recent products
$recentProducts = $pdo->query("SELECT p.*, c.name as category_name 
                              FROM products p 
                              JOIN categories c ON p.category_id = c.id 
                              ORDER BY p.id DESC LIMIT 6")->fetchAll();

// Fetch recent inquiries
$recentInquiries = $pdo->query("SELECT * FROM contact_inquiries ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- Metric Cards Row -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Products -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white h-100" style="border-left: 4px solid var(--vm-maroon) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Listed Products</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $totalProducts; ?></h3>
                    <a href="products.php" class="small text-danger fw-semibold text-decoration-none">Manage All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="rounded-circle p-3 text-white" style="background: linear-gradient(135deg, var(--vm-maroon), #9E1A1E);">
                    <i class="fa-solid fa-vest-patches fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Girl Suits -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white h-100" style="border-left: 4px solid #DFBD6C !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Girl Suits & Shararas</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $totalGirlSuits; ?></h3>
                    <a href="products.php?category=girl-suits" class="small text-warning text-dark fw-semibold text-decoration-none">View Girl Suits <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="rounded-circle p-3 text-dark bg-warning">
                    <i class="fa-solid fa-sparkles fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Categories -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white h-100" style="border-left: 4px solid #0D6EFD !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Categories</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $totalCategories; ?></h3>
                    <a href="categories.php" class="small text-primary fw-semibold text-decoration-none">Manage Categories <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="rounded-circle p-3 text-white bg-primary">
                    <i class="fa-solid fa-layer-group fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Customer Inquiries -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm p-3 bg-white h-100" style="border-left: 4px solid #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Customer Inquiries</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">
                        <?= $totalInquiries; ?>
                        <?php if ($unreadInquiries > 0): ?>
                            <span class="badge bg-danger fs-6"><?= $unreadInquiries; ?> New</span>
                        <?php endif; ?>
                    </h3>
                    <a href="inquiries.php" class="small text-success fw-semibold text-decoration-none">View Messages <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="rounded-circle p-3 text-white bg-success">
                    <i class="fa-solid fa-envelope-open-text fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Store QR Standee & Location Banner -->
<div class="card border-0 rounded-3 shadow-sm p-4 mb-4 text-white" style="background: linear-gradient(135deg, #4A080A 0%, #680E10 50%, #2A0406 100%); border: 1.5px solid var(--vm-gold) !important;">
    <div class="row align-items-center g-3">
        <div class="col-md-2 text-center">
            <img src="../images/vastra-mahal-location-qr.png" alt="Shop QR" class="img-fluid rounded border border-warning p-1 bg-white" style="max-width: 110px;">
        </div>
        <div class="col-md-7">
            <span class="badge bg-warning text-dark fw-bold mb-1">Store QR Standee Active</span>
            <h5 class="fw-bold mb-1 text-white" style="font-family: var(--vm-font-title);">Boutique GPS Location & Digital Catalog QR</h5>
            <p class="small text-light opacity-90 mb-0">
                Customers in Janakpuri / Uttam Nagar can scan this code to browse all product categories and open turn-by-turn Google Maps navigation directly to your store: <code>https://maps.app.goo.gl/73nDqtqFGqEFEmUf6</code>.
            </p>
        </div>
        <div class="col-md-3 text-md-end">
            <a href="store-qr.php" class="btn btn-sm btn-light text-danger fw-bold me-2">
                <i class="fa-solid fa-print me-1"></i> Print Standee
            </a>
            <a href="https://maps.app.goo.gl/73nDqtqFGqEFEmUf6" target="_blank" class="btn btn-sm btn-warning text-dark fw-bold">
                <i class="fa-solid fa-map-location-dot me-1"></i> Test GPS
            </a>
        </div>
    </div>
</div>

<!-- Two Column Layout: Recent Products & Inquiries -->
<div class="row g-4">
    <!-- Col 1: Recent Products Table -->
    <div class="col-lg-7">
        <div class="card border-0 rounded-3 shadow-sm bg-white h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--vm-font-title);"><i class="fa-solid fa-vest-patches text-danger me-2"></i> Recently Added Products</h6>
                <a href="products.php" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-secondary">
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $rp): 
                                $rPrice = !empty($rp['sale_price']) && $rp['sale_price'] < $rp['price'] ? $rp['sale_price'] : $rp['price'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="../<?= e($rp['main_image']); ?>" alt="" class="rounded border" style="width:42px;height:42px;object-fit:cover;">
                                            <div>
                                                <div class="fw-semibold text-dark text-truncate" style="max-width:180px;"><?= e($rp['name']); ?></div>
                                                <small class="text-muted"><?= e($rp['sku'] ?? 'N/A'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= e($rp['category_name']); ?></span></td>
                                    <td><span class="fw-bold text-danger"><?= formatRupee($rPrice); ?></span></td>
                                    <td>
                                        <?php if ($rp['in_stock']): ?>
                                            <span class="badge bg-success-subtle text-success">In Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-muted">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="product-edit.php?id=<?= $rp['id']; ?>" class="btn btn-sm btn-light border text-primary" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="../product-detail.php?id=<?= $rp['id']; ?>" target="_blank" class="btn btn-sm btn-light border text-secondary" title="View Public">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Col 2: Recent Customer Inquiries -->
    <div class="col-lg-5">
        <div class="card border-0 rounded-3 shadow-sm bg-white h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--vm-font-title);"><i class="fa-solid fa-comments text-success me-2"></i> Recent Inquiries</h6>
                <a href="inquiries.php" class="btn btn-sm btn-outline-success">Manage Inquiries</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentInquiries)): ?>
                    <div class="p-4 text-center text-muted">No inquiries received yet.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentInquiries as $inq): 
                            $cleanPhone = preg_replace('/[^0-9]/', '', $inq['phone']);
                            if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
                        ?>
                            <div class="list-group-item p-3 <?= $inq['is_read'] ? '' : 'bg-light'; ?>">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold text-dark"><?= e($inq['name']); ?></h6>
                                    <small class="text-muted" style="font-size:11px;"><?= date('d M, h:i A', strtotime($inq['created_at'])); ?></small>
                                </div>
                                <div class="small text-danger fw-semibold mb-1"><?= e($inq['subject'] ?? 'Product Inquiry'); ?></div>
                                <p class="small text-secondary mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                    <?= e($inq['message']); ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="https://wa.me/<?= $cleanPhone; ?>?text=<?= urlencode("Hello " . $inq['name'] . "! Thank you for contacting Vastra Mahal regarding: " . $inq['subject']); ?>" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:12px;">
                                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Reply
                                    </a>
                                    <a href="tel:<?= e($inq['phone']); ?>" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size:12px;">
                                        <i class="fa-solid fa-phone me-1"></i> Call
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
