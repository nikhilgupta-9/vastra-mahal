<?php
require_once __DIR__ . '/includes/admin-header.php';

$productId = (int)($_GET['id'] ?? 0);
if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Fetch existing product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = "Edit Product: " . $product['name'];
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
    } else {
        $imagePath = $product['main_image'];

        // If new image uploaded
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImageFile('main_image', 'products');
            if (!$uploadResult['success']) {
                $error = $uploadResult['error'];
            } else {
                $imagePath = $uploadResult['path'];
            }
        }

        if (empty($error)) {
            try {
                $updStmt = $pdo->prepare("UPDATE products SET 
                    category_id = ?, name = ?, sku = ?, price = ?, sale_price = ?, 
                    fabric = ?, color = ?, work_type = ?, short_desc = ?, description = ?, 
                    main_image = ?, is_featured = ?, in_stock = ? 
                    WHERE id = ?");
                
                $updStmt->execute([
                    $categoryId, $name, $sku, $price, $salePrice, 
                    $fabric, $color, $workType, $shortDesc, $description, 
                    $imagePath, $isFeatured, $inStock, $productId
                ]);

                // Reload product data
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                $success = "Product details updated successfully!";
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!-- Edit Product Card -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">Edit Product: <?= e($product['name']); ?></h5>
                    <small class="text-muted">Update pricing, fabric details, stock, or replacement imagery.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="../product-detail.php?id=<?= $product['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                        <i class="fa-solid fa-eye me-1"></i> View Live
                    </a>
                    <a href="products.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?= e($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= e($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="product-edit.php?id=<?= $product['id']; ?>" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-dark">Product Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= e($product['name']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id']; ?>" <?= ($product['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                                    <?= e($c['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Regular Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?= e($product['price']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Discount / Sale Price (₹)</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= e($product['sale_price'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">SKU Code</label>
                        <input type="text" name="sku" class="form-control" value="<?= e($product['sku']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Fabric</label>
                        <input type="text" name="fabric" class="form-control" value="<?= e($product['fabric']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Color</label>
                        <input type="text" name="color" class="form-control" value="<?= e($product['color']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-dark">Work / Craft Type</label>
                        <input type="text" name="work_type" class="form-control" value="<?= e($product['work_type']); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">Short Summary</label>
                        <input type="text" name="short_desc" class="form-control" value="<?= e($product['short_desc']); ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">Detailed Description & Care Instructions</label>
                        <textarea name="description" rows="5" class="form-control"><?= e($product['description']); ?></textarea>
                    </div>

                    <!-- Image Section -->
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label small fw-semibold text-dark">Product Image</label>
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <img id="currentImageThumb" src="../<?= e($product['main_image']); ?>" alt="Current Image" class="rounded border shadow-sm" style="width: 80px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="col">
                                    <input type="file" name="main_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted d-block mt-1">Leave empty to keep current image. Select new file to replace.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="col-12">
                        <div class="d-flex gap-4 p-3 bg-light rounded-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="in_stock" id="inStockCheck" <?= $product['in_stock'] ? 'checked' : ''; ?>>
                                <label class="form-check-label small fw-semibold" for="inStockCheck">In Stock / Available for Order</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedCheck" <?= $product['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label small fw-semibold" for="isFeaturedCheck">Featured Royal Pick (Show on Homepage)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                        <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-royal">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Changes
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
            document.getElementById('currentImageThumb').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
