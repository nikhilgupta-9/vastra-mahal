<?php
/**
 * The Vastra Mahal (द वस्त्र महल) - Simple & Reliable Database Configuration
 * 
 * Automatically switches between Localhost (XAMPP) & Live Server (cPanel/Hostinger)
 * Push freely to Git without breaking local or live credentials!
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------------------------------
// 1. Detect Environment (Localhost vs Live Server)
// --------------------------------------------------------------------------
$rawHost = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
$hostName = strtolower(trim(explode(':', $rawHost)[0]));

$isLocal = (php_sapi_name() === 'cli' && getenv('APP_ENV') !== 'production')
           || in_array($hostName, ['localhost', '127.0.0.1', '::1'], true)
           || (strlen($hostName) > 6 && substr($hostName, -6) === '.local')
           || (strlen($hostName) > 5 && substr($hostName, -5) === '.test');

// --------------------------------------------------------------------------
// 2. Database Credentials
// --------------------------------------------------------------------------
if ($isLocal) {
    // LOCALHOST (XAMPP / WAMP)
    $db_host = 'localhost';
    $db_name = 'vastra_mahal_db';
    $db_user = 'root';
    $db_pass = '';
} else {
    // LIVE PRODUCTION SERVER (Hostinger / cPanel)
    $db_host = 'localhost';
    $db_name = 'u950539402_vastramahal_db';
    $db_user = 'u950539402_vastramahal_db';
    $db_pass = 'L5@QUkU!|6y'; // Primary live password
}

// Check for custom server override (config/db.custom.php is in .gitignore)
$customConfigFile = __DIR__ . '/db.custom.php';
if (file_exists($customConfigFile)) {
    $custom = @include $customConfigFile;
    if (is_array($custom)) {
        if (!empty($custom['DB_HOST'])) $db_host = $custom['DB_HOST'];
        if (!empty($custom['DB_NAME'])) $db_name = $custom['DB_NAME'];
        if (!empty($custom['DB_USER'])) $db_user = $custom['DB_USER'];
        if (isset($custom['DB_PASS']))  $db_pass = $custom['DB_PASS'];
    }
}

// --------------------------------------------------------------------------
// 3. Global Constants & Site URLs
// --------------------------------------------------------------------------
define('DB_HOST', $db_host);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('IS_LOCAL', $isLocal);

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
// 4. Database Connection (PDO with Smart Auto-Fallback)
// --------------------------------------------------------------------------
$pdo = null;
$errorList = [];

// Prepare connection candidates
$attempts = [
    ['host' => $db_host, 'pass' => $db_pass],
];

if (!$isLocal) {
    // If primary password fails on live, also attempt alternate password
    $attempts[] = ['host' => $db_host, 'pass' => 'P>|X@Oq7e!'];
    // In case socket vs TCP issue on live, try 127.0.0.1
    $attempts[] = ['host' => '127.0.0.1', 'pass' => $db_pass];
    $attempts[] = ['host' => '127.0.0.1', 'pass' => 'P>|X@Oq7e!'];
}

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

foreach ($attempts as $attempt) {
    try {
        $dsn = "mysql:host=" . $attempt['host'] . ";dbname=" . $db_name . ";charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $attempt['pass'], $pdoOptions);
        break; // Successfully connected!
    } catch (PDOException $e) {
        $errorList[] = $e->getMessage();
    }
}

// If connection failed, show clean and transparent error details so you can fix it immediately!
if (!$pdo) {
    $lastError = end($errorList);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Error - The Vastra Mahal</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; padding: 25px; margin: 0; }
            .card { max-width: 650px; margin: 40px auto; background: #ffffff; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-top: 5px solid #dc2626; padding: 30px; }
            h2 { color: #dc2626; margin-top: 0; font-size: 22px; display: flex; align-items: center; gap: 8px; }
            p { color: #475569; font-size: 14px; line-height: 1.6; }
            .details-table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
            .details-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; }
            .details-table td:first-child { font-weight: 600; color: #64748b; width: 140px; }
            .error-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 14px; font-family: Consolas, monospace; font-size: 13px; color: #991b1b; word-break: break-all; margin: 18px 0; }
            .guide-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px 20px; margin: 20px 0; font-size: 14px; color: #166534; }
            .guide-box h4 { margin: 0 0 10px; font-size: 15px; }
            .guide-box ol { margin: 0; padding-left: 20px; line-height: 1.7; }
            .btn { display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-top: 10px; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>⚠️ Database Connection Error</h2>
            <p>Database se connect nahi ho paya. Yeh details check karein:</p>
            
            <table class="details-table">
                <tr><td>Environment:</td><td><strong><?= $isLocal ? 'Localhost (XAMPP)' : 'Live Server' ?></strong></td></tr>
                <tr><td>Host:</td><td><code><?= htmlspecialchars($db_host) ?></code></td></tr>
                <tr><td>Database Name:</td><td><code><?= htmlspecialchars($db_name) ?></code></td></tr>
                <tr><td>Username:</td><td><code><?= htmlspecialchars($db_user) ?></code></td></tr>
            </table>

            <div class="error-box">
                <strong>MySQL Error Message:</strong><br>
                <?= htmlspecialchars($lastError) ?>
            </div>

            <div class="guide-box">
                <h4>🛠️ Hostinger / cPanel me kaise thik karein:</h4>
                <ol>
                    <li>Hostinger / cPanel open karein &rarr; <strong>MySQL Databases</strong> me jayein.</li>
                    <li>Check karein ki database <code><?= htmlspecialchars($db_name) ?></code> create hai ya nahi.</li>
                    <li>Check karein ki user <code><?= htmlspecialchars($db_user) ?></code> ko database me <strong>ALL PRIVILEGES</strong> ke sath Add kiya gaya hai.</li>
                    <li>Agar password change kiya hai, toh <code>config/db.php</code> me update karein ya <code>config/db.custom.php</code> file banayein.</li>
                </ol>
            </div>
            
            <div style="text-align: center;">
                <a href="?" class="btn">🔄 Refresh Page</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// --------------------------------------------------------------------------
// 5. Auto-Sync Official Business Information (Permanent Live Database Sync)
// --------------------------------------------------------------------------
try {
    $officialUpdates = [
        'store_name'      => 'The Vastra Mahal',
        'store_tagline'   => 'Royal Heritage Ethnic Couture & Handloom Silks',
        'phone_number'    => '+91 96251 37860',
        'whatsapp_number' => '+919625137860',
        'store_email'     => 'thevastramahal60@gmail.com',
        'store_address'   => 'RZ K1A/272, Gandhi Market, West Sagar Pur, New Delhi - 110046',
        'google_map_url'  => 'https://share.google/4X3xcWrgXxZ754XWa',
        'store_timings'   => 'Mon - Sun: 10:30 AM to 9:00 PM',
        'instagram_url'   => 'https://instagram.com/thevastramahal',
        'facebook_url'    => 'https://facebook.com/thevastramahal',
    ];

    $checkStmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('store_address', 'phone_number', 'store_email', 'google_map_url')");
    $dbSettings = $checkStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $needsSync = false;
    if (isset($dbSettings['store_address']) && (stripos($dbSettings['store_address'], 'Janakpuri') !== false || stripos($dbSettings['store_address'], 'Uttam Nagar') !== false || stripos($dbSettings['store_address'], 'Heritage Fashion') !== false)) {
        $needsSync = true;
    }
    if (isset($dbSettings['phone_number']) && stripos($dbSettings['phone_number'], '98765') !== false) {
        $needsSync = true;
    }
    if (isset($dbSettings['store_email']) && stripos($dbSettings['store_email'], 'contact@vastramahal.com') !== false) {
        $needsSync = true;
    }
    if (isset($dbSettings['google_map_url']) && stripos($dbSettings['google_map_url'], '73nDqtqFGqEFEmUf6') !== false) {
        $needsSync = true;
    }

    if ($needsSync) {
        $upStmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        foreach ($officialUpdates as $k => $v) {
            $upStmt->execute([$k, $v]);
        }
    }
} catch (Exception $e) {
    // Fail-safe during initial install
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
                $val = $row['setting_value'];

                // Live safety filter: Ensure legacy dummy values are never rendered
                if ($row['setting_key'] === 'store_address' && (stripos($val, 'Janakpuri') !== false || stripos($val, 'Uttam Nagar') !== false || stripos($val, 'Heritage Fashion') !== false)) {
                    $val = 'RZ K1A/272, Gandhi Market, West Sagar Pur, New Delhi - 110046';
                } elseif ($row['setting_key'] === 'phone_number' && stripos($val, '98765') !== false) {
                    $val = '+91 96251 37860';
                } elseif ($row['setting_key'] === 'whatsapp_number' && stripos($val, '98765') !== false) {
                    $val = '+919625137860';
                } elseif ($row['setting_key'] === 'store_email' && stripos($val, 'contact@vastramahal.com') !== false) {
                    $val = 'thevastramahal60@gmail.com';
                } elseif ($row['setting_key'] === 'google_map_url' && stripos($val, '73nDqtqFGqEFEmUf6') !== false) {
                    $val = 'https://share.google/4X3xcWrgXxZ754XWa';
                }

                $settingsCache[$row['setting_key']] = $val;
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
