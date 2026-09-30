<?php
$pageTitle = "Product Management";
require_once __DIR__ . '/includes/admin-header.php';

$success = '';
$error = '';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    try {
        $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delStmt->execute([$delId]);
        $success = "Product #{$delId} has been successfully deleted.";
        autoSyncDatabaseSql($pdo);
    } catch (Exception $e) {
        $error = "Error deleting product: " . $e->getMessage();
    }
}

// Handle Status / Stock Toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle_stock' && isset($_GET['id'])) {
    $togId = (int)$_GET['id'];
    try {
        $pdo->prepare("UPDATE products SET in_stock = 1 - in_stock WHERE id = ?")->execute([$togId]);
        $success = "Stock status updated.";
        autoSyncDatabaseSql($pdo);
    } catch (Exception $e) {
        $error = "Could not update stock status.";
    }
}

// Parameters
$catFilter = trim($_GET['category'] ?? '');
$search = trim($_GET['q'] ?? '');

$where = ["1=1"];
$params = [];

if (!empty($catFilter)) {
    $where[] = "c.slug = ?";
    $params[] = $catFilter;
}

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.fabric LIKE ?)";
    $like = '%' . $search . '%';
    $params = array_merge($params, [$like, $like, $like]);
}

$whereSql = implode(' AND ', $where);

// Fetch products
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE $whereSql 
        ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
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

<!-- Header Actions -->
<div class="card border-0 rounded-3 shadow-sm bg-white p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">All Listed Products (<?= count($products); ?>)</h5>
            <small class="text-muted">Manage your suits, sarees, lehengas and girl suits catalog.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="product-add.php" class="btn btn-royal">
                <i class="fa-solid fa-plus me-1"></i> Add New Product
            </a>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 rounded-3 shadow-sm bg-white p-3 mb-4">
    <form action="products.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by name, SKU, or fabric..." value="<?= e($search); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- All Categories --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['slug']); ?>" <?= $catFilter === $cat['slug'] ? 'selected' : ''; ?>>
                        <?= e($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark flex-grow-1">Filter</button>
            <?php if (!empty($search) || !empty($catFilter)): ?>
                <a href="products.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th style="width: 70px;">Image</th>
                    <th>Product Name & SKU</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Badges</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-1 mb-2 d-block text-secondary"></i>
                            No products match your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): 
                        $hasDisc = !empty($p['sale_price']) && $p['sale_price'] < $p['price'];
                        $curPrice = $hasDisc ? $p['sale_price'] : $p['price'];
                    ?>
                        <tr>
                            <td>
                                <img src="../<?= e($p['main_image']); ?>" alt="" class="rounded border" style="width:54px;height:54px;object-fit:cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($p['name']); ?></div>
                                <div class="small text-muted">
                                    SKU: <code><?= e($p['sku'] ?? 'N/A'); ?></code>
                                    <?php if (!empty($p['fabric'])): ?>
                                        | <span class="text-secondary"><?= e($p['fabric']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= e($p['category_name']); ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-danger"><?= formatRupee($curPrice); ?></div>
                                <?php if ($hasDisc): ?>
                                    <small class="text-muted text-decoration-line-through"><?= formatRupee($p['price']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="products.php?action=toggle_stock&id=<?= $p['id']; ?>" class="text-decoration-none" title="Click to toggle stock">
                                    <?php if ($p['in_stock']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-check me-1"></i> In Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-xmark me-1"></i> Out of Stock</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($p['category_slug'] === 'girl-suits'): ?>
                                    <span class="badge bg-warning text-dark fw-bold" style="font-size:10px;">Girl Suit</span>
                                <?php endif; ?>
                                <?php if ($p['is_featured']): ?>
                                    <span class="badge bg-primary text-white" style="font-size:10px;">Featured</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="../product-detail.php?id=<?= $p['id']; ?>" target="_blank" class="btn btn-sm btn-light border text-muted" title="View Public Page">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="product-edit.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-light border text-primary" title="Edit Product">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="products.php?action=delete&id=<?= $p['id']; ?>" class="btn btn-sm btn-light border text-danger" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
