<?php
/**
 * QUICKBILL 305 Debug Script
 * Run this to identify missing functions and methods
 */

// Define application constant
define('QUICKBILL_305', true);

// Start session
session_start();

// Include your existing files
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<h1>🔍 QUICKBILL 305 Debug Report</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";

// Test Database class
echo "<div class='section'>";
echo "<h2>📊 Database Class Analysis</h2>";

try {
    $db = new Database();
    echo "<span class='success'>✅ Database class exists</span><br>";
    
    // Check available methods
    $methods = get_class_methods($db);
    echo "<h3>Available Methods:</h3>";
    echo "<pre>" . implode("\n", $methods) . "</pre>";
    
    // Test specific methods we need
    $requiredMethods = [
        'execute', 'fetchRow', 'fetchAll', 'getLastInsertId', 
        'beginTransaction', 'commit', 'rollback'
    ];
    
    echo "<h3>Method Check:</h3>";
    foreach ($requiredMethods as $method) {
        if (method_exists($db, $method)) {
            echo "<span class='success'>✅ $method()</span><br>";
        } else {
            echo "<span class='error'>❌ $method() - MISSING</span><br>";
            
            // Suggest alternatives
            if ($method === 'getLastInsertId') {
                $alternatives = ['lastInsertId', 'insert_id', 'getInsertId'];
                foreach ($alternatives as $alt) {
                    if (method_exists($db, $alt)) {
                        echo "<span class='warning'>   💡 Found alternative: $alt()</span><br>";
                    }
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Database class error: " . $e->getMessage() . "</span>";
}

echo "</div>";

// Test required functions
echo "<div class='section'>";
echo "<h2>🔧 Required Functions Check</h2>";

$requiredFunctions = [
    'sanitizeInput',
    'generateCSRFToken',
    'validateCSRFToken', 
    'setFlashMessage',
    'getFlashMessages',
    'writeLog',
    'getClientIP',
    'sendJsonResponse'
];

foreach ($requiredFunctions as $func) {
    if (function_exists($func)) {
        echo "<span class='success'>✅ $func()</span><br>";
    } else {
        echo "<span class='error'>❌ $func() - MISSING</span><br>";
    }
}

echo "</div>";

// Test constants
echo "<div class='section'>";
echo "<h2>🏷️ Constants Check</h2>";

$requiredConstants = [
    'APP_NAME',
    'BASE_URL',
    'SESSION_LIFETIME',
    'LOGIN_ATTEMPTS_LIMIT',
    'LOGIN_LOCKOUT_TIME'
];

foreach ($requiredConstants as $const) {
    if (defined($const)) {
        echo "<span class='success'>✅ $const = " . constant($const) . "</span><br>";
    } else {
        echo "<span class='error'>❌ $const - MISSING</span><br>";
    }
}

echo "</div>";

// Test session and auth
echo "<div class='section'>";
echo "<h2>🔐 Session & Auth Check</h2>";

echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "<span class='success'>✅ Active</span>" : "<span class='error'>❌ Not Active</span>") . "<br>";

$authFunctions = ['isLoggedIn', 'getCurrentUser', 'hasPermission'];
foreach ($authFunctions as $func) {
    if (function_exists($func)) {
        echo "<span class='success'>✅ $func()</span><br>";
    } else {
        echo "<span class='error'>❌ $func() - MISSING</span><br>";
    }
}

echo "</div>";

// Show PHP version and environment
echo "<div class='section'>";
echo "<h2>🖥️ Environment Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current File: " . __FILE__ . "<br>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li>Copy this report</li>";
echo "<li>I'll create fixed versions of all files based on your actual setup</li>";
echo "<li>The fixed files will work with your existing Database class and functions</li>";
echo "</ol>";
echo "</div>";
?>