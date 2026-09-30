<?php
require_once __DIR__ . '/../../config/db.php';
checkAdminAuth();

$activeAdminPage = basename($_SERVER['PHP_SELF']);

// Count unread inquiries
try {
    $unreadCountStmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read = 0");
    $unreadInquiriesCount = (int)$unreadCountStmt->fetchColumn();
} catch (Exception $e) {
    $unreadInquiriesCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | The Vastra Mahal Admin' : 'Admin Portal | The Vastra Mahal'; ?></title>
    
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../css/all.min.css" rel="stylesheet">
    <link href="../css/vastra-mahal-luxury.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-sidebar-bg: #22080A;
            --admin-sidebar-hover: #3D0F13;
            --admin-active: #7B1113;
        }
        body {
            background-color: #F8F9FA;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }
        .admin-sidebar {
            width: 260px;
            background: var(--admin-sidebar-bg);
            min-height: 100vh;
            color: #E8DCCB;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            border-right: 1px solid rgba(200, 157, 75, 0.2);
            transition: all 0.3s;
        }
        .admin-sidebar-brand {
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .admin-nav {
            padding: 15px 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .admin-nav-item {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #CFC5B8;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .admin-nav-item i {
            width: 22px;
            margin-right: 10px;
            font-size: 16px;
        }
        .admin-nav-item:hover {
            background: var(--admin-sidebar-hover);
            color: #FFFFFF;
        }
        .admin-nav-item.active {
            background: linear-gradient(135deg, var(--vm-maroon), #98191D);
            color: #FFFFFF;
            font-weight: 600;
            border: 1px solid var(--vm-gold);
            box-shadow: 0 4px 12px rgba(123, 17, 19, 0.4);
        }
        .admin-main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-topbar {
            background: #FFFFFF;
            border-bottom: 1px solid #E9ECEF;
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .admin-content {
            padding: 28px;
            flex-grow: 1;
        }
        @media (max-width: 992px) {
            .admin-sidebar {
                margin-left: -260px;
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .admin-main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Start -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="index.php" class="admin-sidebar-brand">
            <div class="vastra-brand-emblem" style="width:38px;height:38px;font-size:13px;">
                <span>TVM</span>
            </div>
            <div>
                <div class="text-white fw-bold" style="font-family: var(--vm-font-title); font-size:17px; letter-spacing:1px;">The Vastra Mahal</div>
                <small class="text-warning text-uppercase" style="font-size:10px; letter-spacing:1px;">Admin Control</small>
            </div>
        </a>

        <div class="admin-nav">
            <span class="text-uppercase text-secondary px-3 py-1 fw-bold" style="font-size:11px; letter-spacing:1px;">Overview</span>
            <a href="index.php" class="admin-nav-item <?= $activeAdminPage === 'index.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>

            <span class="text-uppercase text-secondary px-3 py-1 mt-3 fw-bold" style="font-size:11px; letter-spacing:1px;">Catalog Management</span>
            <a href="products.php" class="admin-nav-item <?= in_array($activeAdminPage, ['products.php', 'product-edit.php']) ? 'active' : ''; ?>">
                <i class="fa-solid fa-vest-patches"></i> All Products
            </a>
            <a href="product-add.php" class="admin-nav-item <?= $activeAdminPage === 'product-add.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-circle-plus"></i> Add New Product
            </a>
            <a href="categories.php" class="admin-nav-item <?= $activeAdminPage === 'categories.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>

            <span class="text-uppercase text-secondary px-3 py-1 mt-3 fw-bold" style="font-size:11px; letter-spacing:1px;">Customers & Store</span>
            <a href="inquiries.php" class="admin-nav-item <?= $activeAdminPage === 'inquiries.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-envelope-open-text"></i> Inquiries
                <?php if ($unreadInquiriesCount > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-auto"><?= $unreadInquiriesCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="store-qr.php" class="admin-nav-item <?= $activeAdminPage === 'store-qr.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-qrcode"></i> Store QR Standee
            </a>

            <span class="text-uppercase text-secondary px-3 py-1 mt-3 fw-bold" style="font-size:11px; letter-spacing:1px;">System & Safety</span>
            <a href="backup.php" class="admin-nav-item <?= $activeAdminPage === 'backup.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cloud-arrow-down"></i> Backup & Git Safety
            </a>

            <span class="text-uppercase text-secondary px-3 py-1 mt-3 fw-bold" style="font-size:11px; letter-spacing:1px;">External</span>
            <a href="../index.php" target="_blank" class="admin-nav-item">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Live Site
            </a>
            <a href="logout.php" class="admin-nav-item text-danger">
                <i class="fa-solid fa-power-off text-danger"></i> Logout
            </a>
        </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main Wrapper Start -->
    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light border d-lg-none" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark" style="font-family: var(--vm-font-title);"><?= isset($pageTitle) ? e($pageTitle) : 'Admin Dashboard'; ?></h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="product-add.php" class="btn btn-sm btn-royal d-none d-sm-inline-flex">
                    <i class="fa-solid fa-plus me-1"></i> Add Product
                </a>
                <div class="d-flex align-items-center gap-2 border-start ps-3">
                    <div class="text-end d-none d-md-block">
                        <div class="small fw-bold text-dark"><?= e($_SESSION['admin_name'] ?? 'Store Admin'); ?></div>
                        <small class="text-muted" style="font-size:11px;">Administrator</small>
                    </div>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Admin Content Area -->
        <main class="admin-content">
