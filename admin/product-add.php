<?php
$pageTitle = "Add New Product";
require_once __DIR__ . '/includes/admin-header.php';

$error = '';
$success = '';

// Fetch active categories
$categories = $pdo->query("SELECT * FROM categories WHERE status='active' ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $categoryId   = (int)($_POST['category_id'] ?? 0);
    $sku          = trim($_POST['sku'] ?? '');
    $price        = (float)($_POST['price'] ?? 0);
    $salePrice    = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $fabric       = trim($_POST['fabric'] ?? '');
    $color        = trim($_POST['color'] ?? '');
    $workType     = trim($_POST['work_type'] ?? '');
    $shortDesc    = trim($_POST['short_desc'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $isFeatured   = isset($_POST['is_featured']) ? 1 : 0;
    $inStock      = isset($_POST['in_stock']) ? 1 : 0;

    if (empty($name) || $categoryId <= 0 || $price <= 0) {
        $error = 'Please fill in the Product Name, Category, and a valid Regular Price.';
    } elseif (!isset($_FILES['main_image']) || $_FILES['main_image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select a high-resolution product main image.';
    } else {
        // Upload image
        $uploadResult = uploadImageFile('main_image', 'products');
        if (!$uploadResult['success']) {
            $error = $uploadResult['error'];
        } else {
            $mainImagePath = $uploadResult['path'];
            $slug = createSlug($name);

            // Ensure unique slug
            $slugCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");
            $slugCheck->execute([$slug]);
            if ($slugCheck->fetchColumn() > 0) {
                $slug .= '-' . time();
            }

            if (empty($sku)) {
                $sku = 'VM-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3)) . '-' . rand(100, 999);
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO products 
                    (category_id, name, slug, sku, price, sale_price, fabric, color, work_type, short_desc, description, main_image, is_featured, is_new_arrival, in_stock, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 'active')");
                
                $stmt->execute([
                    $categoryId, $name, $slug, $sku, $price, $salePrice, 
                    $fabric, $color, $workType, $shortDesc, $description, 
                    $mainImagePath, $isFeatured, $inStock
                ]);

                $newId = $pdo->lastInsertId();
                autoSyncDatabaseSql($pdo);
                header("Location: products.php?success=Product+'" . urlencode($name) . "'+added+successfully");
                exit;
            } catch (Exception $e) {
                $error = 'Database Error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!-- Add Product Form Card -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">Add New Product</h5>
                    <small class="text-muted">Fill out the details below to publish a new saree, suit, or bridal lehenga to your catalog.</small>
                </div>
                <a href="products.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= e($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="product-add.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Title -->
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-dark">Product Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Royal Wine Velvet Straight Salwar Suit" required value="<?= e($_POST['name'] ?? ''); ?>">
                    </div>

                    <!-- Category -->
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id']; ?>" <?= (isset($_POST['category_id']) && (int)$_POST['category_id'] === $c['id']) ? 'selected' : ''; ?>>
                                    <?= e($c['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Pricing -->
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Regular Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 5999.00" required value="<?= e($_POST['price'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Discounted / Sale Price (₹) <small class="text-muted">(Optional)</small></label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" placeholder="e.g. 4499.00" value="<?= e($_POST['sale_price'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">SKU Code <small class="text-muted">(Auto-generated if empty)</small></label>
                        <input type="text" name="sku" class="form-control" placeholder="e.g. VM-SUIT-099" value="<?= e($_POST['sku'] ?? ''); ?>">
                    </div>

                    <!-- Fabric & Color Specs -->
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Fabric</label>
                        <input type="text" name="fabric" class="form-control" placeholder="e.g. Pure Katan Silk / Velvet" value="<?= e($_POST['fabric'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Color</label>
                        <input type="text" name="color" class="form-control" placeholder="e.g. Wine Burgundy / Crimson Gold" value="<?= e($_POST['color'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Work / Craft Type</label>
                        <input type="text" name="work_type" class="form-control" placeholder="e.g. Zari Weaving / Mirror Work" value="<?= e($_POST['work_type'] ?? ''); ?>">
                    </div>

                    <!-- Short Description -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">Short Summary</label>
                        <input type="text" name="short_desc" class="form-control" placeholder="Brief one-line summary for product card" value="<?= e($_POST['short_desc'] ?? ''); ?>">
                    </div>

                    <!-- Long Description -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">Detailed Description & Care Instructions</label>
                        <textarea name="description" rows="5" class="form-control" placeholder="Describe the design, dupatta, ghera, stitching details, styling tips, etc..."><?= e($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-md-12">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label small fw-semibold text-dark">Upload Main Product Image <span class="text-danger">*</span></label>
                            <input type="file" name="main_image" class="form-control" accept="image/*" required onchange="previewImage(this)">
                            <small class="text-muted d-block mt-1">Recommended: High quality vertical portrait image (3:4 ratio, JPG, PNG or WebP, max 10MB).</small>
                            <div id="imagePreviewBox" class="mt-3 d-none">
                                <img id="previewImg" src="#" alt="Preview" class="rounded border shadow-sm" style="max-height: 200px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="col-12">
                        <div class="d-flex gap-4 p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="in_stock" id="inStockCheck" checked>
                                <label class="form-check-label small fw-semibold" for="inStockCheck">In Stock / Available for Order</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedCheck">
                                <label class="form-check-label small fw-semibold" for="isFeaturedCheck">Featured Royal Pick (Show on Homepage)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                        <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-royal">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Publish Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreviewBox').classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
