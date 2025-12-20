<?php
/**
 * Database Configuration
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'invoice_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Invoice Management');
define('APP_URL', 'http://127.0.0.1/invoice-management/public');
define('UPLOAD_PATH', __DIR__ . '/../public/assets/uploads/');
define('LOGO_PATH', UPLOAD_PATH . 'logos/');

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Timezone
date_default_timezone_set('Asia/Kolkata');
