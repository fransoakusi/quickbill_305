<?php
/**
 * Debug Test File for QUICKBILL 305
 * Tests each component step by step to identify issues
 */

echo "<h1>QUICKBILL 305 - Debug Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .pass{color:green;} .fail{color:red;} .step{margin:10px 0;padding:10px;border:1px solid #ccc;}</style>";

// Step 1: Test constant definition
echo "<div class='step'>";
echo "<h3>Step 1: Define Application Constant</h3>";
define('QUICKBILL_305', true);
echo "<span class='pass'>✓ Application constant defined</span>";
echo "</div>";

// Step 2: Test config.php
echo "<div class='step'>";
echo "<h3>Step 2: Include config.php</h3>";
try {
    require_once 'config/config.php';
    echo "<span class='pass'>✓ config.php loaded successfully</span><br>";
    echo "App Name: " . APP_NAME . "<br>";
    echo "Version: " . APP_VERSION . "<br>";
    echo "Environment: " . ENVIRONMENT;
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error loading config.php: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 3: Test database.php
echo "<div class='step'>";
echo "<h3>Step 3: Include database.php</h3>";
try {
    require_once 'config/database.php';
    echo "<span class='pass'>✓ database.php loaded successfully</span><br>";
    
    // Test database connection
    $db = new Database();
    if ($db->getStatus()) {
        echo "<span class='pass'>✓ Database connection successful</span>";
    } else {
        echo "<span class='fail'>✗ Database connection failed</span>";
    }
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error with database: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 4: Test functions.php
echo "<div class='step'>";
echo "<h3>Step 4: Include functions.php</h3>";
try {
    require_once 'includes/functions.php';
    echo "<span class='pass'>✓ functions.php loaded successfully</span><br>";
    
    // Test a function
    $testData = sanitizeInput("<script>alert('test')</script>");
    echo "sanitizeInput test: " . $testData . "<br>";
    
    $testEmail = isValidEmail("test@example.com");
    echo "Email validation test: " . ($testEmail ? 'Pass' : 'Fail');
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error loading functions.php: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 5: Test session start
echo "<div class='step'>";
echo "<h3>Step 5: Start Session</h3>";
try {
    session_start();
    echo "<span class='pass'>✓ Session started successfully</span><br>";
    echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive');
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error starting session: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 6: Test auth.php
echo "<div class='step'>";
echo "<h3>Step 6: Include auth.php</h3>";
try {
    require_once 'includes/auth.php';
    echo "<span class='pass'>✓ auth.php loaded successfully</span><br>";
    
    // Test if functions exist
    if (function_exists('initAuth')) {
        echo "<span class='pass'>✓ initAuth function exists</span><br>";
        initAuth();
        echo "<span class='pass'>✓ initAuth executed successfully</span>";
    } else {
        echo "<span class='fail'>✗ initAuth function not found</span>";
    }
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error with auth.php: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 7: Test security.php
echo "<div class='step'>";
echo "<h3>Step 7: Include security.php</h3>";
try {
    require_once 'includes/security.php';
    echo "<span class='pass'>✓ security.php loaded successfully</span><br>";
    
    // Test if functions exist
    if (function_exists('initSecurity')) {
        echo "<span class='pass'>✓ initSecurity function exists</span><br>";
        initSecurity();
        echo "<span class='pass'>✓ initSecurity executed successfully</span>";
    } else {
        echo "<span class='fail'>✗ initSecurity function not found</span>";
    }
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error with security.php: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Step 8: Test overall system
echo "<div class='step'>";
echo "<h3>Step 8: Run System Test</h3>";
try {
    if (function_exists('runSystemTest')) {
        $tests = runSystemTest();
        echo "<span class='pass'>✓ System test completed</span><br>";
        echo "Database Status: " . $tests['database']['status'] . "<br>";
        echo "Total Extensions: " . count($tests['extensions']) . "<br>";
        echo "Total Directories: " . count($tests['directories']);
    } else {
        echo "<span class='fail'>✗ runSystemTest function not found</span>";
    }
} catch (Exception $e) {
    echo "<span class='fail'>✗ Error running system test: " . $e->getMessage() . "</span>";
}
echo "</div>";

echo "<h3>Debug Test Complete</h3>";
echo "<p>If all steps show green checkmarks, your system is working correctly.</p>";
echo "<p><a href='test.php'>Run Full Test</a> | <a href='auth/login.php'>Go to Login</a></p>";
?>