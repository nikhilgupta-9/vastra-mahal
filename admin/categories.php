<?php
$pageTitle = "Category Management";
require_once __DIR__ . '/includes/admin-header.php';

$success = '';
$error = '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        $slug = createSlug($name);
        $imagePath = 'images/categories/cat-banarasi.jpg';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upResult = uploadImageFile('image', 'categories');
            if ($upResult['success']) {
                $imagePath = $upResult['path'];
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image, is_featured, status) VALUES (?, ?, ?, ?, 1, 'active')");
            $stmt->execute([$name, $slug, $description, $imagePath]);
            $success = "Category '{$name}' created successfully!";
        } catch (Exception $e) {
            $error = "Error adding category: " . $e->getMessage();
        }
    }
}

// Handle Delete Category
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $catId = (int)$_GET['id'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$catId]);
        $success = "Category deleted.";
    } catch (Exception $e) {
        $error = "Error deleting category: " . $e->getMessage();
    }
}

// Fetch categories with product counts
$categories = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                          FROM categories c 
                          LEFT JOIN products p ON c.id = p.category_id 
                          GROUP BY c.id 
                          ORDER BY c.id ASC")->fetchAll();
?>

<!-- Alerts -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?= e($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= e($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left: Add Category Form -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4">
            <h5 class="fw-bold mb-3" style="font-family: var(--vm-font-title);">Add New Category</h5>
            
            <form action="categories.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Designer Sharara Suits" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Brief details about this ethnic wear collection..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">Category Cover Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted" style="font-size:11px;">Vertical portrait image recommended (3:4 ratio).</small>
                </div>

                <button type="submit" class="btn btn-royal w-100 py-2 justify-content-center">
                    <i class="fa-solid fa-plus me-1"></i> Save Category
                </button>
            </form>
        </div>
    </div>

    <!-- Right: Categories List Table -->
    <div class="col-lg-8">
        <div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark" style="font-family: var(--vm-font-title);">Current Categories (<?= count($categories); ?>)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-secondary">
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Category Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th class="text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>
                                    <img src="../<?= e($cat['image'] ?? 'images/categories/cat-banarasi.jpg'); ?>" alt="" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($cat['name']); ?></div>
                                    <small class="text-muted d-inline-block text-truncate" style="max-width:240px;"><?= e($cat['description']); ?></small>
                                </td>
                                <td><code><?= e($cat['slug']); ?></code></td>
                                <td>
                                    <a href="products.php?category=<?= e($cat['slug']); ?>" class="badge bg-light text-danger border fw-bold text-decoration-none">
                                        <?= (int)$cat['product_count']; ?> Products
                                    </a>
                                </td>
                                <td class="text-end">
                                    <a href="categories.php?action=delete&id=<?= $cat['id']; ?>" class="btn btn-sm btn-light border text-danger" title="Delete Category" onclick="return confirm('Deleting category will also remove or uncategorize its products. Continue?');">
                                        <i class="fa-solid fa-trash-can"></i>
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

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
