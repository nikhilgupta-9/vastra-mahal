<?php
/**
 * Vastra Mahal - Database Configuration & Global Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vastra_mahal_db');

// Site URL configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
// Clean up for admin subfolder
$siteBase = preg_replace('/\/admin.*$/', '', $baseDir);
define('SITE_URL', $protocol . $host . $siteBase);
define('ADMIN_URL', SITE_URL . '/admin');

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:30px;text-align:center;'>
            <h2>Database Connection Error</h2>
            <p>Could not connect to database 'vastra_mahal_db'. Ensure MySQL is running in XAMPP.</p>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}

/**
 * Fetch a setting from site_settings table
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
