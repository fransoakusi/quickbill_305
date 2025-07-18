<?php
/**
 * System Test File for QUICKBILL 305
 * Tests database connection, file permissions, and core functionality
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files first (before starting session)
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Start session after configuration
session_start();

// Run system tests (without requiring authentication)
$tests = runSystemTest();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Test - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .test-container {
            margin: 2rem auto;
            max-width: 800px;
        }
        
        .test-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .test-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        
        .test-body {
            padding: 1.5rem;
        }
        
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .test-item:last-child {
            border-bottom: none;
        }
        
        .test-name {
            font-weight: 500;
            color: #374151;
        }
        
        .test-status {
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
        }
        
        .status-pass {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .summary-item {
            display: inline-block;
            margin: 0 1rem;
        }
        
        .summary-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }
        
        .summary-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 2rem;
        }
        
        .btn-custom {
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #2563eb;
            border: none;
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
            border: none;
            color: white;
        }
        
        .database-details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .code-block {
            background: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 8px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <!-- Header -->
        <div class="test-card">
            <div class="test-header">
                <h1><i class="fas fa-cogs me-2"></i><?php echo APP_NAME; ?> - System Test</h1>
                <p class="mb-0">Checking system requirements and configuration</p>
            </div>
        </div>

        <?php
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;

        // Count tests
        foreach ($tests as $category => $categoryTests) {
            if (is_array($categoryTests)) {
                foreach ($categoryTests as $test => $status) {
                    $totalTests++;
                    if ($status === true || (is_array($status) && $status['status'] === 'success')) {
                        $passedTests++;
                    } else {
                        $failedTests++;
                    }
                }
            } else {
                $totalTests++;
                if ($categoryTests === true || (is_array($categoryTests) && $categoryTests['status'] === 'success')) {
                    $passedTests++;
                } else {
                    $failedTests++;
                }
            }
        }
        ?>

        <!-- Summary -->
        <div class="summary-card">
            <h3><i class="fas fa-chart-line me-2"></i>Test Summary</h3>
            <div class="row mt-3">
                <div class="col">
                    <div class="summary-item">
                        <span class="summary-number"><?php echo $totalTests; ?></span>
                        <span class="summary-label">Total Tests</span>
                    </div>
                </div>
                <div class="col">
                    <div class="summary-item">
                        <span class="summary-number"><?php echo $passedTests; ?></span>
                        <span class="summary-label">Passed</span>
                    </div>
                </div>
                <div class="col">
                    <div class="summary-item">
                        <span class="summary-number"><?php echo $failedTests; ?></span>
                        <span class="summary-label">Failed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Status Alert -->
        <?php if ($failedTests == 0): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>All tests passed!</strong> Your system is ready to run QUICKBILL 305.
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong><?php echo $failedTests; ?> test(s) failed.</strong> Please fix the issues below before proceeding.
            </div>
        <?php endif; ?>

        <!-- Database Test -->
        <div class="test-card">
            <div class="test-body">
                <h4><i class="fas fa-database me-2"></i>Database Connection</h4>
                <div class="test-item">
                    <span class="test-name">Connection Status</span>
                    <span class="test-status <?php echo $tests['database']['status'] === 'success' ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $tests['database']['status'] === 'success' ? 'PASS' : 'FAIL'; ?>
                    </span>
                </div>
                <div class="database-details">
                    <strong>Message:</strong> <?php echo htmlspecialchars($tests['database']['message']); ?><br>
                    <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                    <strong>Database:</strong> <?php echo DB_NAME; ?><br>
                    <strong>User:</strong> <?php echo DB_USER; ?>
                </div>
            </div>
        </div>

        <!-- Directory Permissions -->
        <div class="test-card">
            <div class="test-body">
                <h4><i class="fas fa-folder me-2"></i>Directory Permissions</h4>
                <?php if (isset($tests['directories'])): ?>
                    <?php foreach ($tests['directories'] as $dir => $writable): ?>
                        <div class="test-item">
                            <span class="test-name"><?php echo basename($dir); ?></span>
                            <span class="test-status <?php echo $writable ? 'status-pass' : 'status-fail'; ?>">
                                <?php echo $writable ? 'WRITABLE' : 'NOT WRITABLE'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="test-card">
            <div class="test-body">
                <h4><i class="fas fa-code me-2"></i>PHP Extensions</h4>
                <?php if (isset($tests['extensions'])): ?>
                    <?php foreach ($tests['extensions'] as $extension => $loaded): ?>
                        <div class="test-item">
                            <span class="test-name"><?php echo strtoupper($extension); ?></span>
                            <span class="test-status <?php echo $loaded ? 'status-pass' : 'status-fail'; ?>">
                                <?php echo $loaded ? 'LOADED' : 'NOT LOADED'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Configuration Details -->
        <div class="test-card">
            <div class="test-body">
                <h4><i class="fas fa-cog me-2"></i>System Configuration</h4>
                <div class="test-item">
                    <span class="test-name">Environment</span>
                    <span class="test-status status-<?php echo ENVIRONMENT === 'development' ? 'warning' : 'pass'; ?>">
                        <?php echo strtoupper(ENVIRONMENT); ?>
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-name">Debug Mode</span>
                    <span class="test-status status-<?php echo DEBUG_MODE ? 'warning' : 'pass'; ?>">
                        <?php echo DEBUG_MODE ? 'ENABLED' : 'DISABLED'; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-name">PHP Version</span>
                    <span class="test-status status-pass">
                        <?php echo PHP_VERSION; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span class="test-name">Session Status</span>
                    <span class="test-status status-pass">
                        <?php echo session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Default Admin User Setup -->
        <div class="test-card">
            <div class="test-body">
                <h4><i class="fas fa-user-shield me-2"></i>Default Admin User Setup</h4>
                <p>To complete the setup, you need to create a default admin user in the database:</p>
                <div class="code-block">
INSERT INTO users (username, email, password_hash, role_id, first_name, last_name, phone) 
VALUES (
    'admin', 
    'admin@quickbill305.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    2, 
    'System', 
    'Administrator', 
    '+233000000000'
);
                </div>
                <div class="mt-3">
                    <strong>Default Login Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code><br>
                    <small class="text-muted">You will be required to change this password on first login.</small>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if ($tests['database']['status'] === 'success' && $passedTests >= $totalTests - 1): ?>
                <a href="auth/login.php" class="btn btn-primary btn-custom">
                    <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                </a>
            <?php endif; ?>
            <a href="javascript:location.reload()" class="btn btn-secondary btn-custom">
                <i class="fas fa-redo me-2"></i>Rerun Tests
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <small class="text-muted">
                <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - 
                System Test Completed at <?php echo date('Y-m-d H:i:s'); ?>
            </small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>