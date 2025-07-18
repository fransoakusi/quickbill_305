<?php
/**
 * Main Application Configuration for QUICKBILL 305
 * Contains all application settings, API keys, and constants
 */

// Prevent direct access
if (!defined('QUICKBILL_305')) {
    die('Direct access not permitted');
}

// Application Information
define('APP_NAME', 'QUICKBILL 360');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Billing Software for Business Operating Permits and Property Rates');

// Environment Configuration
define('ENVIRONMENT', 'development'); // development, staging, production
define('DEBUG_MODE', true);

// Directory Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// URL Configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path = dirname($_SERVER['SCRIPT_NAME'] ?? '');
define('BASE_URL', $protocol . '://' . $host . $path);

// Database Configuration (moved to database.php)
define('DB_HOST', 'localhost');
define('DB_NAME', 'quickbill_305');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_NAME', 'quickbill_305_session');
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false); // Set to true in production with HTTPS
define('SESSION_HTTP_ONLY', true);

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv']);

// API Configuration
$api_config = [
    'google_maps' => [
        'api_key' => 'AIzaSyDg1CWNtJ8BHeclYP7VfltZZLIcY3TVHaI',
        'enabled' => true
    ],
    'twilio' => [
        'account_sid' => '831JD7BHZAHE9M7EWNW1FCUB',
        'auth_token' => 'ZQHijuboaimCs7Ali3X9aRzizbjztN8a',
        'enabled' => true
    ],
    'paystack' => [
        'secret_key' => 'sk_test_b6d5e56246149f160bf5e572f715714dcb375e72',
        'public_key' => 'pk_test_6a0be5c8c08f05e97fd19eb697df4c37876b49f8',
        'enabled' => true,
        'test_mode' => true
    ]
];

// Email Configuration
define('MAIL_HOST', 'localhost');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_FROM_EMAIL', 'noreply@quickbill305.com');
define('MAIL_FROM_NAME', 'QUICKBILL 305');

// Application Settings
$app_settings = [
    'assembly_name' => 'Municipal Assembly',
    'billing_start_date' => '2024-11-01',
    'auto_bill_generation' => true,
    'sms_notifications' => true,
    'email_notifications' => true,
    'default_timezone' => 'Africa/Accra',
    'currency' => 'GHS',
    'currency_symbol' => '₵',
    'date_format' => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i:s',
    'items_per_page' => 20,
    'backup_retention_days' => 30
];

// User Roles and Permissions
$user_roles = [
    'super_admin' => [
        'name' => 'Super Admin',
        'permissions' => ['*'] // All permissions
    ],
    'admin' => [
        'name' => 'Admin',
        'permissions' => ['*'] // All permissions, including those for restriction page
    ],
    'officer' => [
        'name' => 'Officer',
        'permissions' => [
            'businesses.view', 'businesses.create', 'businesses.edit',
            'properties.view', 'properties.create', 'properties.edit',
            'payments.create', 'payments.view',
            'bills.view', 'bills.create', 'bills.print',
            'map.view'
        ]
    ],
    'revenue_officer' => [
        'name' => 'Revenue Officer',
        'permissions' => [
            'payments.create', 'payments.view',
            'businesses.view', 'properties.view',
            'map.view'
        ]
    ],
    'data_collector' => [
        'name' => 'Data Collector',
        'permissions' => [
            'businesses.view', 'businesses.create', 'businesses.edit',
            'properties.view', 'properties.create', 'properties.edit',
            'map.view'
        ]
    ]
];

// Logging Configuration
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FILE_PATH', STORAGE_PATH . '/logs/app.log');
define('ERROR_LOG_PATH', STORAGE_PATH . '/logs/error.log');
define('ACCESS_LOG_PATH', STORAGE_PATH . '/logs/access.log');
define('PAYMENT_LOG_PATH', STORAGE_PATH . '/logs/payment.log');

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', ERROR_LOG_PATH);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ERROR_LOG_PATH);
}

// Set timezone
date_default_timezone_set($app_settings['default_timezone']);

// Start session configuration (only if session hasn't started yet)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.name', SESSION_NAME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_httponly', SESSION_HTTP_ONLY);
    if (SESSION_SECURE) {
        ini_set('session.cookie_secure', 1);
    }
}

/**
 * Get configuration value
 */
function getConfig($key, $default = null) {
    global $app_settings, $api_config, $user_roles;
    
    // Check app settings first
    if (isset($app_settings[$key])) {
        return $app_settings[$key];
    }
    
    // Check API config
    if (isset($api_config[$key])) {
        return $api_config[$key];
    }
    
    // Check user roles
    if (isset($user_roles[$key])) {
        return $user_roles[$key];
    }
    
    return $default;
}

// Make configurations available globally
$GLOBALS['app_settings'] = $app_settings;
$GLOBALS['api_config'] = $api_config;
$GLOBALS['user_roles'] = $user_roles;
?>