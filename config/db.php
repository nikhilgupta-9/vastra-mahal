<?php
/**
 * The Vastra Mahal (द वस्त्र महल) - Smart Multi-Environment Database Configuration
 * 
 * Automatically switches between Local (XAMPP/WAMP) & Production (cPanel/VPS/Cloud)
 * Push freely to Git without risking or overwriting live production credentials!
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------------------------------
// 1. Detect Environment (Localhost vs Production)
// --------------------------------------------------------------------------
$rawHost = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
// Strip port if present for comparison
$hostName = strtolower(trim(explode(':', $rawHost)[0]));

$isLocal = false;
if (php_sapi_name() === 'cli') {
    // Terminal CLI environment
    $isLocal = (getenv('APP_ENV') !== 'production');
} else {
    // Web request environment
    $localHosts = ['localhost', '127.0.0.1', '::1'];
    $isLocal = in_array($hostName, $localHosts, true)
               || substr($hostName, -6) === '.local'
               || substr($hostName, -5) === '.test';
}

// --------------------------------------------------------------------------
// 2. Default Credentials Profiles
// --------------------------------------------------------------------------

// Local Environment (XAMPP / WAMP defaults)
$localConfig = [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'vastra_mahal_db',
];

// Production Defaults
// You can edit these directly OR use 'config/db.custom.php' / '.env' on the live server!
$prodConfig = [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_USER' => 'u950539402_vastramahal_db',
    'DB_PASS' => 'P>|X@Oq7e!',
    'DB_NAME' => 'u950539402_vastramahal_db',
];

// Active configuration initially picked by environment detection
$activeConfig = $isLocal ? $localConfig : $prodConfig;

// --------------------------------------------------------------------------
// 3. Safe Overrides (Files in .gitignore - NEVER overwritten by Git pulls!)
// --------------------------------------------------------------------------

// Priority 1: config/db.custom.php (Recommended for cPanel / Shared Hosting)
$customConfigFile = __DIR__ . '/db.custom.php';
if (file_exists($customConfigFile)) {
    $customConfig = include $customConfigFile;
    if (is_array($customConfig)) {
        $activeConfig = array_merge($activeConfig, $customConfig);
    }
}

// Priority 2: Root .env file (Standard modern environment configuration)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envLines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($envLines !== false) {
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') !== false) {
                list($envKey, $envVal) = explode('=', $line, 2);
                $envKey = trim($envKey);
                $envVal = trim($envVal, " \t\n\r\0\x0B\"'");
                if (in_array($envKey, ['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME'])) {
                    $activeConfig[$envKey] = $envVal;
                }
            }
        }
    }
}

// Priority 3: Server Environment Variables (Cloud / Docker / Apache SetEnv)
foreach (['DB_HOST', 'DB_PORT', 'DB_USER', 'DB_PASS', 'DB_NAME'] as $sysKey) {
    $sysVal = getenv($sysKey) ?: ($_ENV[$sysKey] ?? null);
    if ($sysVal !== false && $sysVal !== null && $sysVal !== '') {
        $activeConfig[$sysKey] = $sysVal;
    }
}

// --------------------------------------------------------------------------
// 4. Define Global Constants
// --------------------------------------------------------------------------
define('DB_HOST', $activeConfig['DB_HOST'] ?? 'localhost');
define('DB_PORT', $activeConfig['DB_PORT'] ?? '3306');
define('DB_USER', $activeConfig['DB_USER'] ?? 'root');
define('DB_PASS', $activeConfig['DB_PASS'] ?? '');
define('DB_NAME', $activeConfig['DB_NAME'] ?? 'vastra_mahal_db');
define('IS_LOCAL', $isLocal);

// Dynamic Site URL calculation
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
           || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$protocol = $isHttps ? 'https://' : 'http://';
$currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$siteBase = preg_replace('/\/admin.*$/', '', $baseDir);

define('SITE_URL', $protocol . $currentHost . $siteBase);
define('ADMIN_URL', SITE_URL . '/admin');

// --------------------------------------------------------------------------
// 5. Connect to Database via PDO
// --------------------------------------------------------------------------
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    if (IS_LOCAL) {
        die("<div style='font-family:Segoe UI,sans-serif;padding:30px;max-width:680px;margin:50px auto;border:2px solid #7B1113;border-radius:12px;background:#FFF;box-shadow:0 10px 30px rgba(0,0,0,0.1);'>
                <h2 style='color:#7B1113;margin-top:0;'>⚠️ Local Database Connection Error</h2>
                <p>Could not connect to database '<strong>" . htmlspecialchars(DB_NAME) . "</strong>' using user '<strong>" . htmlspecialchars(DB_USER) . "</strong>' on <strong>" . htmlspecialchars(DB_HOST) . ":" . htmlspecialchars(DB_PORT) . "</strong>.</p>
                <div style='background:#FFF5F5;border-left:4px solid #7B1113;padding:12px 15px;margin:20px 0;font-size:13px;color:#333;'>
                    <strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "
                </div>
                <p style='color:#555;font-size:14px;'>Make sure MySQL is started in XAMPP and the database <code>" . htmlspecialchars(DB_NAME) . "</code> is imported.</p>
             </div>");
    } else {
        die("<div style='font-family:Georgia,serif;padding:50px 30px;text-align:center;max-width:550px;margin:80px auto;border:2px solid #C89D4B;border-radius:12px;background:#FFF;box-shadow:0 12px 35px rgba(0,0,0,0.1);'>
                <h2 style='color:#7B1113;margin:0 0 10px;font-size:28px;'>THE VASTRA MAHAL</h2>
                <p style='color:#C89D4B;letter-spacing:2px;font-size:12px;text-transform:uppercase;margin-bottom:25px;'>Royal Heritage Ethnic Couture</p>
                <h4 style='color:#2B2B2B;margin-bottom:15px;font-family:sans-serif;'>Boutique Portal Temporarily Unavailable</h4>
                <p style='color:#666;line-height:1.6;font-family:sans-serif;font-size:14px;'>We are currently undergoing scheduled system updates. Please check back in a few moments or connect directly with our boutique on WhatsApp.</p>
                <div style='margin-top:25px;'>
                    <a href='https://wa.me/919625137860' style='background:#25D366;color:#FFF;text-decoration:none;padding:10px 22px;border-radius:25px;font-family:sans-serif;font-size:14px;font-weight:600;display:inline-block;'>
                        Contact via WhatsApp
                    </a>
                </div>
             </div>");
    }
}

// --------------------------------------------------------------------------
// 6. Global Helper Functions
// --------------------------------------------------------------------------

/**
 * Fetch a setting from site_settings table (cached in memory)
 */
function getSetting($pdo, $key, $default = '') {
    static $settingsCache = [];
    if (empty($settingsCache)) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
            while ($row = $stmt->fetch()) {
                $settingsCache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            return $default;
        }
    }
    return $settingsCache[$key] ?? $default;
}

/**
 * Format Indian Rupee currency
 */
function formatRupee($amount) {
    if ($amount === null || $amount === '') return '₹0';
    return '₹' . number_format((float)$amount, 0, '.', ',');
}

/**
 * Sanitize string output
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL safe slug
 */
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Handle image upload safely
 */
function uploadImageFile($fileInputName, $subfolder = 'products') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error code: ' . ($_FILES[$fileInputName]['error'] ?? 'None')];
    }

    $file = $_FILES[$fileInputName];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Only JPG, PNG, and WebP images are allowed.'];
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
        return ['success' => false, 'error' => 'Image size must be less than 10MB.'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = 'vm_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($extension);
    
    $targetDir = __DIR__ . '/../images/' . trim($subfolder, '/') . '/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . $newFileName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => 'images/' . trim($subfolder, '/') . '/' . $newFileName];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file. Check folder permissions.'];
}

/**
 * Check if admin is authenticated
 */
function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

/**
 * Generate a complete SQL Dump of all database tables using PDO
 */
function generateDatabaseSqlDump($pdo) {
    $tables = ['admin_users', 'categories', 'products', 'contact_inquiries', 'site_settings'];
    $out = "-- ========================================================\n";
    $out .= "-- The Vastra Mahal (द वस्त्र महल) - Database Backup\n";
    $out .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $out .= "-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB 10.x+\n";
    $out .= "-- Safe for Git deployment and live database synchronization\n";
    $out .= "-- ========================================================\n\n";
    $out .= "SET FOREIGN_KEY_CHECKS=0;\n";
    $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $out .= "SET time_zone = \"+00:00\";\n\n";

    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            if (!$row) continue;

            $createSql = $row[1];
            if (stripos($createSql, 'CREATE TABLE IF NOT EXISTS') === false) {
                $createSql = preg_replace('/^CREATE TABLE/i', 'CREATE TABLE IF NOT EXISTS', $createSql);
            }

            $out .= "-- --------------------------------------------------------\n";
            $out .= "-- Table structure for `{$table}`\n";
            $out .= "-- --------------------------------------------------------\n";
            $out .= $createSql . ";\n\n";

            $dataStmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $out .= "-- Data for `{$table}` (" . count($rows) . " rows)\n";
                $columns = array_keys($rows[0]);
                $colList = implode('`, `', $columns);

                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($r as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $pdo->quote($val);
                        }
                    }
                    $valList = implode(', ', $vals);

                    if ($table === 'site_settings') {
                        $out .= "INSERT INTO `{$table}` (`{$colList}`) VALUES ({$valList}) ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);\n";
                    } elseif (in_array('id', $columns)) {
                        $out .= "INSERT INTO `{$table}` (`{$colList}`) VALUES ({$valList}) ON DUPLICATE KEY UPDATE `id`=`id`;\n";
                    } else {
                        $out .= "INSERT IGNORE INTO `{$table}` (`{$colList}`) VALUES ({$valList});\n";
                    }
                }
                $out .= "\n";
            }
        } catch (Exception $e) {
            error_log("Failed dumping table {$table}: " . $e->getMessage());
        }
    }

    $out .= "SET FOREIGN_KEY_CHECKS=1;\n";
    return $out;
}

/**
 * Auto-sync database dump to database/vastra_mahal_db.sql
 * Keeps Git repo always updated with latest products
 */
function autoSyncDatabaseSql($pdo) {
    try {
        $dump = generateDatabaseSqlDump($pdo);
        $file = __DIR__ . '/../database/vastra_mahal_db.sql';
        file_put_contents($file, $dump);
        return true;
    } catch (Exception $e) {
        error_log("Database auto-sync failed: " . $e->getMessage());
        return false;
    }
}

