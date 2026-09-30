<?php
/**
 * Vastra Mahal - Production Custom Database Credentials
 * 
 * 📋 HOW TO USE THIS ON PRODUCTION / CPANEL:
 * 1. Copy or rename this file to "db.custom.php" in this same config directory:
 *    (e.g., config/db.custom.php)
 * 2. Put your live cPanel / hosting database details below.
 * 3. "db.custom.php" is in .gitignore, which means Git will NEVER overwrite or touch it
 *    when you run 'git pull' or 'git push' from your local system!
 */

return [
    'DB_HOST' => 'localhost',              // Usually 'localhost' on cPanel / shared hosting
    'DB_PORT' => '3306',
    'DB_USER' => 'cpanel_db_username',     // Your live hosting DB username
    'DB_PASS' => 'your_strong_password_here', // Your live hosting DB password
    'DB_NAME' => 'cpanel_db_name',         // Your live hosting DB name
];
