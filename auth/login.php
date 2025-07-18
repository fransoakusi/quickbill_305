<?php
/**
 * Login Page for QUICKBILL 305
 * Handles user authentication and redirects to appropriate dashboards
 */

// Define application constant
define('QUICKBILL_305', true);

// Include required files first (before starting session)
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Start session after configuration
session_start();

// Include auth and security after session is started
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Initialize auth and security
initAuth();
initSecurity();

// Redirect if already logged in
if (isLoggedIn()) {
    $userRole = getCurrentUserRole();
    
    switch ($userRole) {
        case 'Super Admin':
        case 'Admin':
            header('Location: ../admin/index.php');
            break;
        case 'Officer':
            header('Location: ../officer/index.php');
            break;
        case 'Revenue Officer':
            header('Location: ../revenue_officer/index.php');
            break;
        case 'Data Collector':
            header('Location: ../data_collector/index.php');
            break;
        default:
            logout();
            break;
    }
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCsrfToken()) {
        $error = 'Security validation failed. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);
        
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $loginResult = loginUser($username, $password, $remember_me);
            
            if ($loginResult['success']) {
                // Check if it's first login
                if ($loginResult['first_login']) {
                    header('Location: first_login.php');
                } else {
                    // Redirect based on role
                    $userRole = $loginResult['user']['role_name'];
                    
                    switch ($userRole) {
                        case 'Super Admin':
                        case 'Admin':
                            header('Location: ../admin/index.php');
                            break;
                        case 'Officer':
                            header('Location: ../officer/index.php');
                            break;
                        case 'Revenue Officer':
                            header('Location: ../revenue_officer/index.php');
                            break;
                        case 'Data Collector':
                            header('Location: ../data_collector/index.php');
                            break;
                        default:
                            logout();
                            $error = 'Invalid user role. Please contact administrator.';
                            break;
                    }
                }
                exit();
            } else {
                $error = $loginResult['message'];
            }
        }
    }
}

// Check for URL parameters
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_role':
            $error = 'Invalid user role. Please contact administrator.';
            break;
        case 'session_expired':
            $error = 'Your session has expired. Please login again.';
            break;
        case 'access_denied':
            $error = 'Access denied. Please login to continue.';
            break;
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'password_changed':
            $success = 'Password changed successfully. Please login with your new password.';
            break;
        case 'logout':
            $success = 'You have been logged out successfully.';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-purple: #667eea;
            --secondary-purple: #764ba2;
            --accent-blue: #4299e1;
            --success-green: #48bb78;
            --warning-orange: #ed8936;
            --danger-red: #e53e3e;
            --dark-text: #2d3748;
            --light-gray: #f8f9fa;
            --medium-gray: #718096;
            --white: #ffffff;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 8px 40px rgba(0, 0, 0, 0.12);
            --shadow-heavy: 0 16px 60px rgba(0, 0, 0, 0.15);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Custom Icons (fallback) */
        .icon-receipt::before { content: "🧾"; }
        .icon-user::before { content: "👤"; }
        .icon-lock::before { content: "🔒"; }
        .icon-eye::before { content: "👁️"; }
        .icon-check::before { content: "✓"; }
        .icon-warning::before { content: "⚠️"; }
        .icon-spinner::before { content: "⏳"; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 30%, var(--accent-blue) 70%, var(--success-green) 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="grad1" cx="20%" cy="20%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient><radialGradient id="grad2" cx="80%" cy="80%"><stop offset="0%" stop-color="rgba(255,255,255,0.15)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23grad1)"/><circle cx="800" cy="800" r="200" fill="url(%23grad2)"/></svg>');
            opacity: 0.4;
            animation: float 25s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-15px) rotate(2deg); }
            66% { transform: translateY(10px) rotate(-1deg); }
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 2;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: var(--shadow-heavy);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideInUp 0.8s ease-out;
            position: relative;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            animation: pulse 2s infinite;
            position: relative;
            z-index: 2;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .login-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 2;
        }
        
        .login-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }
        
        .login-body {
            padding: 2.5rem;
            background: var(--white);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.75rem;
            display: block;
            font-size: 0.95rem;
        }
        
        .input-group {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }
        
        .input-group-text {
            background: var(--light-gray);
            border: 2px solid #e2e8f0;
            border-right: none;
            color: var(--medium-gray);
            padding: 0.75rem 1rem;
            border-radius: 12px 0 0 12px;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            color: var(--dark-text);
            flex: 1;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-purple);
        }
        
        .toggle-password {
            background: var(--light-gray);
            border: 2px solid #e2e8f0;
            border-left: none;
            color: var(--medium-gray);
            padding: 0.75rem;
            border-radius: 0 12px 12px 0;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .toggle-password:hover {
            background: #e2e8f0;
            color: var(--primary-purple);
        }
        
        .form-control.password-input {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-purple), var(--secondary-purple));
            border: none;
            color: var(--white);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            background: var(--medium-gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .alert {
            border-radius: 12px;
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeInDown 0.5s ease-out;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: rgba(229, 62, 62, 0.1);
            color: var(--danger-red);
            border-left: 4px solid var(--danger-red);
        }
        
        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success-green);
            border-left: 4px solid var(--success-green);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .form-check-input:checked {
            background: var(--primary-purple);
            border-color: var(--primary-purple);
        }
        
        .form-check-label {
            font-size: 0.95rem;
            color: var(--medium-gray);
            cursor: pointer;
            user-select: none;
        }
        
        .system-info {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: var(--medium-gray);
            border: 1px solid #e2e8f0;
        }
        
        .system-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .system-info-item:last-child {
            margin-bottom: 0;
        }
        
        .system-info-label {
            font-weight: 600;
            color: var(--dark-text);
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-flex;
            align-items: center;
        }
        
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .back-to-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            z-index: 3;
        }
        
        .back-to-home:hover {
            background: rgba(255, 255, 255, 0.3);
            color: var(--white);
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        /* Responsive Design */
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
            
            .back-to-home {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 1rem;
                display: inline-block;
                width: auto;
            }
        }
        
        /* Focus states for accessibility */
        .form-control:focus,
        .form-check-input:focus,
        .btn-login:focus,
        .toggle-password:focus {
            outline: 2px solid var(--primary-purple);
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .login-card {
                background: var(--white);
                border: 2px solid var(--dark-text);
            }
            
            .form-control {
                border-color: var(--dark-text);
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <a href="../index.php" class="back-to-home">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-receipt"></i>
                    <span class="icon-receipt" style="display: none;"></span>
                </div>
                <h1><?php echo APP_NAME; ?></h1>
                <p>Modern Assembly Revenue Management</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="icon-warning" style="display: none;"></span>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <span class="icon-check" style="display: none;"></span>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm">
                    <?php echo csrfField(); ?>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                                <span class="icon-user" style="display: none;"></span>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   placeholder="Enter your username or email"
                                   required
                                   autocomplete="username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                                <span class="icon-lock" style="display: none;"></span>
                            </span>
                            <input type="password" 
                                   class="form-control password-input" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="toggle-password" id="togglePassword">
                                <i class="fas fa-eye"></i>
                                <span class="icon-eye" style="display: none;"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember_me" 
                               name="remember_me"
                               <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="remember_me">
                            Remember me for 30 days
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <span class="loading" id="loadingSpinner">
                            <i class="fas fa-spinner spinner"></i>
                            <span class="icon-spinner" style="display: none;"></span>
                            &nbsp;
                        </span>
                        <span id="loginText">Sign In to Dashboard</span>
                    </button>
                </form>
                
                <div class="system-info">
                    <div class="system-info-item">
                        <span class="system-info-label">System Version:</span>
                        <span><?php echo APP_VERSION; ?></span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Environment:</span>
                        <span><?php echo ucfirst(ENVIRONMENT); ?></span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Status:</span>
                        <span style="color: var(--success-green); font-weight: 600;">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Check if Font Awesome loaded, if not show emoji icons
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const testIcon = document.querySelector('.fas.fa-user');
                if (!testIcon || getComputedStyle(testIcon, ':before').content === 'none') {
                    document.querySelectorAll('.fas, .far').forEach(function(icon) {
                        icon.style.display = 'none';
                    });
                    document.querySelectorAll('[class*="icon-"]').forEach(function(emoji) {
                        emoji.style.display = 'inline';
                    });
                }
            }, 100);

            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
            
            // Form submission with loading state
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loginText = document.getElementById('loginText');
            
            loginForm.addEventListener('submit', function(e) {
                // Show loading state
                loginBtn.disabled = true;
                loadingSpinner.classList.add('show');
                loginText.textContent = 'Signing In...';
                
                // Add visual feedback
                loginBtn.style.transform = 'translateY(0)';
            });
            
            // Auto-focus on username field
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }
            
            // Enhanced form validation
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('blur', function() {
                    if (this.value.trim() === '' && this.hasAttribute('required')) {
                        this.style.borderColor = 'var(--danger-red)';
                    } else {
                        this.style.borderColor = '#e2e8f0';
                    }
                });
                
                control.addEventListener('input', function() {
                    if (this.style.borderColor === 'var(--danger-red)' && this.value.trim() !== '') {
                        this.style.borderColor = 'var(--success-green)';
                    }
                });
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Alt + H to go back to home
                if (e.altKey && e.key === 'h') {
                    e.preventDefault();
                    window.location.href = '../index.php';
                }
            });
            
            // Add ripple effect to button
            function addRippleEffect(button) {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255,255,255,0.5);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            }
            
            // Add ripple to login button
            addRippleEffect(loginBtn);
            
            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>