<?php
$pageTitle = "Database Backup & Git Safety";
require_once __DIR__ . '/includes/admin-header.php';

$success = '';
$error = '';

// Handle Direct SQL Download
if (isset($_GET['action']) && $_GET['action'] === 'download_sql') {
    $dump = generateDatabaseSqlDump($pdo);
    $filename = 'the_vastra_mahal_db_backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($dump));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $dump;
    exit;
}

// Handle Product Images ZIP Download
if (isset($_GET['action']) && $_GET['action'] === 'download_images') {
    if (!class_exists('ZipArchive')) {
        $error = "ZipArchive extension is not enabled on this PHP installation.";
    } else {
        $zip = new ZipArchive();
        $zipFilename = sys_get_temp_dir() . '/the_vastra_mahal_images_' . time() . '.zip';
        
        if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $imagesDir = __DIR__ . '/../images/products';
            if (is_dir($imagesDir)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($imagesDir),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = 'products/' . substr($filePath, strlen($imagesDir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }
            $zip->close();
            
            if (file_exists($zipFilename)) {
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="the_vastra_mahal_product_images_' . date('Y-m-d') . '.zip"');
                header('Content-Length: ' . filesize($zipFilename));
                header('Cache-Control: no-cache, no-store, must-revalidate');
                readfile($zipFilename);
                @unlink($zipFilename);
                exit;
            } else {
                $error = "Could not generate ZIP archive.";
            }
        } else {
            $error = "Failed to create ZIP file.";
        }
    }
}

// Handle Manual Auto-Sync to Git SQL File
if (isset($_POST['action']) && $_POST['action'] === 'sync_sql_file') {
    if (autoSyncDatabaseSql($pdo)) {
        $success = "Database successfully exported to <strong>database/vastra_mahal_db.sql</strong>! All current products are now synced for Git.";
    } else {
        $error = "Could not write to database/vastra_mahal_db.sql. Please check file permissions.";
    }
}

// Fetch System Counts
$totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$activeProducts = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
$totalCategories = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalInquiries = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries")->fetchColumn();

// Images count & size
$productsDir = __DIR__ . '/../images/products';
$imageCount = 0;
$imageSizeTotal = 0;
if (is_dir($productsDir)) {
    foreach (glob($productsDir . '/*.*') as $imgFile) {
        $imageCount++;
        $imageSizeTotal += filesize($imgFile);
    }
}
$imageSizeFormatted = round($imageSizeTotal / (1024 * 1024), 2) . ' MB';

// Last SQL file modification time
$sqlFilePath = __DIR__ . '/../database/vastra_mahal_db.sql';
$sqlLastModified = file_exists($sqlFilePath) ? date('d M Y, h:i A', filemtime($sqlFilePath)) : 'Not generated yet';
$sqlFileSize = file_exists($sqlFilePath) ? round(filesize($sqlFilePath) / 1024, 1) . ' KB' : '0 KB';
?>

<!-- Alerts -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?= $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= e($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Header Title -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h4 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">Database Backup & Git Safety Center</h4>
        <p class="text-muted small mb-0">Protect your newly added products, backup database anytime, and sync safely with Git code updates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="backup.php?action=download_sql" class="btn btn-royal shadow-sm">
            <i class="fa-solid fa-download me-1"></i> Download .SQL Backup
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Total Products</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalProducts; ?></h3>
                    <small class="text-success"><i class="fa-solid fa-circle-check me-1"></i><?= $activeProducts; ?> Active</small>
                </div>
                <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-4">
                    <i class="fa-solid fa-vest-patches"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Categories</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalCategories; ?></h3>
                    <small class="text-muted">Ethnic Wear Sections</small>
                </div>
                <div class="p-3 rounded-circle bg-warning-subtle text-warning fs-4">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Product Photos</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $imageCount; ?></h3>
                    <small class="text-muted"><?= $imageSizeFormatted; ?> storage</small>
                </div>
                <div class="p-3 rounded-circle bg-info-subtle text-info fs-4">
                    <i class="fa-solid fa-images"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-bold">Git SQL Sync</span>
                    <h5 class="fw-bold mb-0 text-dark"><?= $sqlFileSize; ?></h5>
                    <small class="text-muted text-truncate d-block" title="<?= $sqlLastModified; ?>">Updated: <?= $sqlLastModified; ?></small>
                </div>
                <div class="p-3 rounded-circle bg-success-subtle text-success fs-4">
                    <i class="fa-solid fa-file-code"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Cards Row -->
<div class="row g-4 mb-4">
    <!-- Card 1: SQL Backup -->
    <div class="col-md-4">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-database text-danger fs-3"></i>
                    <h5 class="fw-bold mb-0" style="font-family: var(--vm-font-title);">1. Download SQL Backup</h5>
                </div>
                <p class="text-muted small">
                    Downloads an instant, complete SQL backup of all products, categories, inquiries, and boutique settings directly to your computer.
                </p>
                <div class="p-2 bg-light rounded small mb-3 text-secondary">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Formatted with <code>ON DUPLICATE KEY UPDATE</code> to prevent any data loss or overwrite.
                </div>
            </div>
            <a href="backup.php?action=download_sql" class="btn btn-royal w-100">
                <i class="fa-solid fa-file-arrow-down me-1"></i> Download .SQL File
            </a>
        </div>
    </div>

    <!-- Card 2: Sync to Git File -->
    <div class="col-md-4">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-code-commit text-success fs-3"></i>
                    <h5 class="fw-bold mb-0" style="font-family: var(--vm-font-title);">2. Sync to Git SQL File</h5>
                </div>
                <p class="text-muted small">
                    Updates <code>database/vastra_mahal_db.sql</code> right on your server disk with all products added from admin.
                </p>
                <div class="p-2 bg-light rounded small mb-3 text-secondary">
                    <i class="fa-solid fa-bolt text-warning me-1"></i> <strong>Automatic:</strong> Whenever you add or edit a product, this file is automatically updated for Git!
                </div>
            </div>
            <form method="POST" action="backup.php">
                <input type="hidden" name="action" value="sync_sql_file">
                <button type="submit" class="btn btn-dark w-100">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Force Re-Sync SQL File
                </button>
            </form>
        </div>
    </div>

    <!-- Card 3: Download Images ZIP -->
    <div class="col-md-4">
        <div class="card border-0 rounded-3 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa-solid fa-file-zipper text-warning fs-3"></i>
                    <h5 class="fw-bold mb-0" style="font-family: var(--vm-font-title);">3. Download Photos (.ZIP)</h5>
                </div>
                <p class="text-muted small">
                    Packs all product photos from <code>images/products/</code> into a single ZIP archive for safe local archival and backup.
                </p>
                <div class="p-2 bg-light rounded small mb-3 text-secondary">
                    <i class="fa-solid fa-folder-tree text-info me-1"></i> Contains <?= $imageCount; ?> images (<?= $imageSizeFormatted; ?>).
                </div>
            </div>
            <a href="backup.php?action=download_images" class="btn btn-outline-dark w-100">
                <i class="fa-solid fa-download me-1"></i> Download Images (.ZIP)
            </a>
        </div>
    </div>
</div>

<!-- Detailed Git Safety & Product Persistence Guide -->
<div class="card border-0 rounded-3 shadow-sm bg-white p-4 mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="fa-solid fa-circle-question text-danger fs-4"></i>
        <h5 class="fw-bold mb-0" style="font-family: var(--vm-font-title);">
            Git aur Database Safety Guide (Admin Products Hatne Se Kaise Bachein)
        </h5>
    </div>

    <div class="row g-4 mt-1">
        <!-- Hindi Explanation -->
        <div class="col-lg-6">
            <div class="p-3 rounded-3 border bg-light h-100">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-language text-danger me-1"></i> हिन्दी में समझें (In Hindi):
                </h6>
                <ul class="small text-secondary mb-0" style="line-height:1.8;">
                    <li><strong>Git Pull se product kabhi nahi hatte:</strong> Jab aap Git par code update karte hain aur <code>git pull</code> chalate hain, to sirf PHP, HTML, CSS files update hoti hain. Aapka MySQL database aur usme add kiye huye products <strong>100% safe</strong> rehte hain.</li>
                    <li><strong>Product remove hone ka asal karan:</strong> Kuch developers code update karne ke baad galti se purana <code>database.sql</code> file phpMyAdmin me firse import kar dete hain. Aisa karne se naye products overwrite ho jaate hain.</li>
                    <li><strong>Sunahara Niyam (Golden Rule):</strong> Website par naye products add karne ke baad, live production server par purana SQL file <strong>KABHI BHI re-import mat kijiye</strong>.</li>
                    <li><strong>Automatic Protection Added:</strong> Humne system me automatic auto-sync laga diya hai. Jaise hi aap Admin se naya product add karenge, <code>database/vastra_mahal_db.sql</code> automatically naye products ke sath update ho jayega.</li>
                </ul>
            </div>
        </div>

        <!-- English Explanation & Safe Workflow -->
        <div class="col-lg-6">
            <div class="p-3 rounded-3 border bg-light h-100">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> 3-Step Safe Deployment Workflow:
                </h6>
                <div class="d-flex flex-column gap-2 small text-secondary">
                    <div class="d-flex gap-2">
                        <span class="badge bg-danger rounded-circle p-2" style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;">1</span>
                        <div><strong>Add Products from Admin:</strong> Add your sarees, suits, and collections freely. All data is saved in MySQL and auto-mirrored to <code>database/vastra_mahal_db.sql</code>.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;">2</span>
                        <div><strong>Download Backup Anytime:</strong> Click "Download .SQL Backup" above before making major code changes for complete peace of mind.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success rounded-circle p-2" style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;">3</span>
                        <div><strong>Update Code via Git Safely:</strong> Run <code>git pull origin main</code> on your server. Your live products remain intact, and your code gets upgraded effortlessly!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
