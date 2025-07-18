<?php
/**
 * System Restrictions Management - QuickBill 305
 * Super Admin only - Manage system access restrictions
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Start session
session_start();

// Include auth and security
require_once '../../includes/auth.php';
require_once '../../includes/security.php';

// Initialize auth and security
initAuth();
initSecurity();

// Check if user is logged in and is Super Admin
if (!isLoggedIn()) {
    header('Location: ../../auth/login.php');
    exit();
}

if (!isSuperAdmin()) {
    setFlashMessage('error', 'Access denied. Super Admin privileges required.');
    header('Location: ../index.php');
    exit();
}

$currentUser = getCurrentUser();
$userDisplayName = getUserDisplayName($currentUser);

// Initialize database
$db = new Database();

// Get current active restriction
$currentRestriction = null;
$restrictionHistory = [];
try {
    // Current active restriction
    $currentQuery = "SELECT * FROM system_restrictions WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1";
    $currentRestriction = $db->fetchRow($currentQuery);
    
    // Restriction history (last 5 for main view)
    $historyQuery = "SELECT sr.*, u.first_name, u.last_name, u.username 
                     FROM system_restrictions sr 
                     LEFT JOIN users u ON sr.created_by = u.user_id 
                     ORDER BY sr.created_at DESC LIMIT 5";
    $restrictionHistory = $db->fetchAll($historyQuery);
    
} catch (Exception $e) {
    error_log("Restriction Data Error: " . $e->getMessage());
    $restrictionHistory = [];
}

// Calculate restriction status
$restrictionStatus = 'none';
$daysRemaining = null;
$isActive = false;

if ($currentRestriction) {
    $startDate = new DateTime($currentRestriction['restriction_start_date']);
    $endDate = new DateTime($currentRestriction['restriction_end_date']);
    $today = new DateTime();
    
    if ($today >= $endDate) {
        $restrictionStatus = 'expired';
        $isActive = true;
    } elseif ($today >= $startDate) {
        $restrictionStatus = 'active';
        $isActive = true;
        $interval = $today->diff($endDate);
        $daysRemaining = $interval->days;
    } else {
        $restrictionStatus = 'scheduled';
        $interval = $today->diff($startDate);
        $daysRemaining = $interval->days;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create_restriction':
                    $startDate = $_POST['start_date'];
                    $endDate = $_POST['end_date'];
                    $warningDays = (int)$_POST['warning_days'];
                    
                    // Validate dates
                    if (strtotime($endDate) <= strtotime($startDate)) {
                        setFlashMessage('error', 'End date must be after start date.');
                        break;
                    }
                    
                    // Deactivate any existing active restrictions
                    $db->execute("UPDATE system_restrictions SET is_active = 0 WHERE is_active = 1");
                    
                    // Create new restriction
                    $insertQuery = "INSERT INTO system_restrictions (restriction_start_date, restriction_end_date, warning_days, is_active, created_by, created_at) 
                                   VALUES (?, ?, ?, 1, ?, NOW())";
                    $db->execute($insertQuery, [$startDate, $endDate, $warningDays, $currentUser['user_id']]);
                    
                    // Log audit
                    logActivity($currentUser['user_id'], 'CREATE_RESTRICTION', 'system_restrictions', null, 
                                   json_encode(['start_date' => $startDate, 'end_date' => $endDate, 'warning_days' => $warningDays]));
                    
                    setFlashMessage('success', 'System restriction created successfully!');
                    break;
                    
                case 'deactivate_restriction':
                    if ($currentRestriction) {
                        $db->execute("UPDATE system_restrictions SET is_active = 0 WHERE restriction_id = ?", 
                                    [$currentRestriction['restriction_id']]);
                        
                        // Log audit
                        logActivity($currentUser['user_id'], 'DEACTIVATE_RESTRICTION', 'system_restrictions', 
                                       $currentRestriction['restriction_id'], null);
                        
                        setFlashMessage('success', 'System restriction deactivated! All users can now access the system.');
                    }
                    break;
                    
                case 'reactivate_restriction':
                    if (isset($_POST['restriction_id'])) {
                        $restrictionId = (int)$_POST['restriction_id'];
                        
                        // Deactivate all other restrictions first
                        $db->execute("UPDATE system_restrictions SET is_active = 0 WHERE is_active = 1");
                        
                        // Reactivate selected restriction
                        $db->execute("UPDATE system_restrictions SET is_active = 1 WHERE restriction_id = ?", [$restrictionId]);
                        
                        // Log audit
                        logActivity($currentUser['user_id'], 'REACTIVATE_RESTRICTION', 'system_restrictions', 
                                       $restrictionId, null);
                        
                        setFlashMessage('success', 'Restriction reactivated successfully!');
                    }
                    break;
                    
                case 'extend_restriction':
                    if ($currentRestriction && isset($_POST['new_end_date'])) {
                        $newEndDate = $_POST['new_end_date'];
                        
                        $db->execute("UPDATE system_restrictions SET restriction_end_date = ? WHERE restriction_id = ?", 
                                    [$newEndDate, $currentRestriction['restriction_id']]);
                        
                        logActivity($currentUser['user_id'], 'EXTEND_RESTRICTION', 'system_restrictions', 
                                       $currentRestriction['restriction_id'], 
                                       json_encode(['old_end_date' => $currentRestriction['restriction_end_date'], 'new_end_date' => $newEndDate]));
                        
                        setFlashMessage('success', 'Restriction period extended successfully!');
                    }
                    break;
            }
        }
        
        // Refresh page after action
        header('Location: restrictions.php');
        exit();
        
    } catch (Exception $e) {
        error_log("Restriction Action Error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to process request. Please try again.');
    }
}

// Get flash messages once
$flashMessages = getFlashMessages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Restrictions - <?php echo APP_NAME; ?></title>
    
    <!-- Icons and Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --success-gradient: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
            --warning-gradient: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --light-gradient: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            --shadow-soft: 0 15px 35px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #2d3748;
        }
        
        /* Top Navigation */
        .top-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            color: #2d3748;
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .brand {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .back-btn {
            background: var(--primary-gradient);
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 18px;
        }
        
        /* Main Container */
        .main-container {
            margin-top: 80px;
            padding: 30px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Page Header */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
            text-align: center;
        }
        
        .page-title {
            font-size: 3rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Flash Messages */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            margin-bottom: 30px;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        /* Status Dashboard */
        .status-dashboard {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .status-main {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--shadow-soft);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .status-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
        }
        
        .status-main.restricted::before {
            background: var(--danger-gradient);
        }
        
        .status-main.scheduled::before {
            background: var(--warning-gradient);
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .status-icon.safe { color: #28a745; }
        .status-icon.warning { color: #ffc107; }
        .status-icon.danger { color: #dc3545; }
        
        .status-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .status-description {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .countdown-display {
            background: var(--light-gradient);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .countdown-number {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .countdown-label {
            font-size: 1rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-custom {
            padding: 15px 30px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 160px;
            justify-content: center;
        }
        
        .btn-deactivate {
            background: var(--success-gradient);
            color: white;
        }
        
        .btn-reactivate {
            background: var(--danger-gradient);
            color: white;
        }
        
        .btn-extend {
            background: var(--warning-gradient);
            color: #2d3748;
        }
        
        .btn-create {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            color: white;
        }
        
        .btn-extend:hover {
            color: #2d3748;
        }
        
        /* Quick Stats */
        .quick-stats {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .stat-icon {
            font-size: 1.5rem;
            color: #667eea;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }
        
        /* Create Restriction Form */
        .create-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-soft);
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control, .form-select {
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-help {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        /* History Section */
        .history-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 40px;
            box-shadow: var(--shadow-soft);
        }
        
        .history-grid {
            display: grid;
            gap: 20px;
        }
        
        .history-item {
            background: var(--light-gradient);
            border-radius: 15px;
            padding: 25px;
            transition: var(--transition);
            border-left: 5px solid #667eea;
        }
        
        .history-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }
        
        .history-item.active {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        }
        
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .history-date {
            font-weight: 700;
            color: #2d3748;
        }
        
        .history-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #dc3545;
            color: white;
        }
        
        .status-inactive {
            background: #6c757d;
            color: white;
        }
        
        .history-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            text-align: center;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .detail-value {
            font-weight: 600;
            color: #2d3748;
        }
        
        .history-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-sm-custom {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--shadow-hover);
        }
        
        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .modal-title {
            font-weight: 700;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .status-dashboard {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-custom {
                width: 100%;
                max-width: 300px;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }
            
            .page-header {
                padding: 30px 20px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .create-section, .status-main, .history-section {
                padding: 25px;
            }
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
            <a href="../index.php" class="brand">
                <i class="fas fa-shield-alt"></i>
                <?php echo APP_NAME; ?> Restrictions
            </a>
        </div>
        
        <div class="user-info">
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Settings
            </a>
            <div class="user-avatar">
                <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
            </div>
            <div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars($userDisplayName); ?></div>
                <div style="font-size: 0.8rem; color: #6c757d;">Super Administrator</div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header fade-in-up">
            <h1 class="page-title">System Access Control</h1>
            <p class="page-subtitle">Manage system-wide access restrictions and maintenance windows with precision and control.</p>
        </div>

        <!-- Flash Messages -->
        <?php if (getFlashMessages()): ?>
            <?php $flash = getFlashMessages(); ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> fade-in-up">
                <i class="fas fa-<?php echo $flash['type'] === 'error' ? 'exclamation-triangle' : 'check-circle'; ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Status Dashboard -->
        <div class="status-dashboard fade-in-up">
            <div class="status-main <?php echo $isActive ? 'restricted' : ($restrictionStatus === 'scheduled' ? 'scheduled' : ''); ?>">
                <?php if ($restrictionStatus === 'none'): ?>
                    <div class="status-icon safe">
                        <i class="fas fa-unlock-alt"></i>
                    </div>
                    <h2 class="status-title" style="color: #28a745;">System Unrestricted</h2>
                    <p class="status-description">All users have full access to the system. No restrictions are currently active or scheduled.</p>
                    
                    <div class="action-buttons">
                        <button class="btn-custom btn-create" onclick="showCreateForm()">
                            <i class="fas fa-plus"></i>
                            Create New Restriction
                        </button>
                    </div>
                    
                <?php elseif ($restrictionStatus === 'scheduled'): ?>
                    <div class="status-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h2 class="status-title" style="color: #ffc107;">Restriction Scheduled</h2>
                    <p class="status-description">A system restriction is scheduled to begin soon. Users will be notified before activation.</p>
                    
                    <div class="countdown-display">
                        <div class="countdown-number"><?php echo $daysRemaining; ?></div>
                        <div class="countdown-label">Days Until Activation</div>
                    </div>
                    
                    <div class="action-buttons">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="deactivate_restriction">
                            <button type="submit" class="btn-custom btn-deactivate" onclick="return confirm('Cancel this scheduled restriction?')">
                                <i class="fas fa-times"></i>
                                Cancel Restriction
                            </button>
                        </form>
                        
                        <button class="btn-custom btn-extend" onclick="showExtendModal()">
                            <i class="fas fa-edit"></i>
                            Modify Schedule
                        </button>
                    </div>
                    
                <?php elseif ($restrictionStatus === 'active'): ?>
                    <div class="status-icon danger">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h2 class="status-title" style="color: #dc3545;">Restriction Active</h2>
                    <p class="status-description">System is currently restricted. Only Super Admins can access the system.</p>
                    
                    <div class="countdown-display">
                        <div class="countdown-number"><?php echo $daysRemaining ?? 0; ?></div>
                        <div class="countdown-label">Days Remaining</div>
                    </div>
                    
                    <div class="action-buttons">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="deactivate_restriction">
                            <button type="submit" class="btn-custom btn-deactivate" onclick="return confirm('Lift the system restriction and allow all users access?')">
                                <i class="fas fa-unlock"></i>
                                Lift Restriction Now
                            </button>
                        </form>
                        
                        <button class="btn-custom btn-extend" onclick="showExtendModal()">
                            <i class="fas fa-plus-circle"></i>
                            Extend Period
                        </button>
                    </div>
                    
                <?php else: // expired ?>
                    <div class="status-icon danger pulse">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2 class="status-title" style="color: #dc3545;">System Locked</h2>
                    <p class="status-description">Restriction period has ended. System is currently locked for all users except Super Admins.</p>
                    
                    <div class="action-buttons">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="deactivate_restriction">
                            <button type="submit" class="btn-custom btn-deactivate" onclick="return confirm('Unlock the system and restore normal access?')">
                                <i class="fas fa-unlock"></i>
                                Unlock System
                            </button>
                        </form>
                        
                        <button class="btn-custom btn-extend" onclick="showExtendModal()">
                            <i class="fas fa-plus-circle"></i>
                            Extend Lock Period
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">System Status</span>
                        <i class="stat-icon fas fa-server"></i>
                    </div>
                    <div class="stat-value"><?php echo $isActive ? 'RESTRICTED' : 'NORMAL'; ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Active Users</span>
                        <i class="stat-icon fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo $isActive ? '1' : 'ALL'; ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Restrictions Made</span>
                        <i class="stat-icon fas fa-history"></i>
                    </div>
                    <div class="stat-value"><?php echo count($restrictionHistory); ?></div>
                </div>
            </div>
        </div>

        <!-- Create Restriction Form -->
        <div class="create-section fade-in-up" id="createForm" style="<?php echo $currentRestriction ? 'display: none;' : ''; ?>">
            <h3 class="section-title">
                <i class="fas fa-plus-circle"></i>
                Create New Restriction
            </h3>
            <p class="section-subtitle">Schedule a system-wide access restriction. This will prevent all users (except Super Admins) from accessing the system during the specified period.</p>
            
            <form method="POST" id="restrictionForm">
                <input type="hidden" name="action" value="create_restriction">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Start Date
                        </label>
                        <input type="date" class="form-control" name="start_date" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                        <div class="form-help">When the restriction period begins</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-check"></i>
                            End Date
                        </label>
                        <input type="date" class="form-control" name="end_date" 
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        <div class="form-help">When the restriction ends</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-bell"></i>
                            Warning Period
                        </label>
                        <select class="form-select" name="warning_days" required>
                            <option value="3">3 days before</option>
                            <option value="7" selected>7 days before</option>
                            <option value="14">14 days before</option>
                            <option value="21">21 days before</option>
                        </select>
                        <div class="form-help">When to notify users about the upcoming restriction</div>
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn-custom btn-create">
                        <i class="fas fa-shield-alt"></i>
                        Create Restriction
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent History -->
        <div class="history-section fade-in-up">
            <h3 class="section-title">
                <i class="fas fa-history"></i>
                Recent Restrictions
            </h3>
            <p class="section-subtitle">View and manage recent system restrictions. You can reactivate past restrictions if needed.</p>
            
            <?php if (empty($restrictionHistory)): ?>
                <div style="text-align: center; padding: 40px; color: #6c757d;">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No Restriction History</h5>
                    <p>No system restrictions have been created yet.</p>
                </div>
            <?php else: ?>
                <div class="history-grid">
                    <?php foreach ($restrictionHistory as $restriction): ?>
                        <div class="history-item <?php echo $restriction['is_active'] ? 'active' : ''; ?>">
                            <div class="history-header">
                                <div class="history-date">
                                    <?php echo date('M d, Y', strtotime($restriction['restriction_start_date'])); ?>
                                    <span style="color: #6c757d;"> to </span>
                                    <?php echo date('M d, Y', strtotime($restriction['restriction_end_date'])); ?>
                                </div>
                                <div class="history-status <?php echo $restriction['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $restriction['is_active'] ? 'Active' : 'Inactive'; ?>
                                </div>
                            </div>
                            
                            <div class="history-details">
                                <div class="detail-item">
                                    <div class="detail-label">Duration</div>
                                    <div class="detail-value">
                                        <?php 
                                        $start = new DateTime($restriction['restriction_start_date']);
                                        $end = new DateTime($restriction['restriction_end_date']);
                                        echo $start->diff($end)->days; ?> days
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Warning</div>
                                    <div class="detail-value"><?php echo $restriction['warning_days']; ?> days</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Created By</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($restriction['first_name'] . ' ' . $restriction['last_name']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Created</div>
                                    <div class="detail-value"><?php echo date('M d, Y', strtotime($restriction['created_at'])); ?></div>
                                </div>
                            </div>
                            
                            <?php if (!$restriction['is_active'] && strtotime($restriction['restriction_end_date']) > time()): ?>
                                <div class="history-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="reactivate_restriction">
                                        <input type="hidden" name="restriction_id" value="<?php echo $restriction['restriction_id']; ?>">
                                        <button type="submit" class="btn-sm-custom btn-reactivate" 
                                                onclick="return confirm('Reactivate this restriction? This will deactivate any current restrictions.')">
                                            <i class="fas fa-power-off"></i>
                                            Reactivate
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Extend/Modify Modal -->
    <div class="modal fade" id="extendModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modify Restriction Period
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="extend_restriction">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">New End Date</label>
                            <input type="date" class="form-control" name="new_end_date" 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            <div class="form-help">Choose a new end date for the restriction period</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Restriction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showCreateForm() {
            document.getElementById('createForm').style.display = 'block';
            document.getElementById('createForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function showExtendModal() {
            new bootstrap.Modal(document.getElementById('extendModal')).show();
        }
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('restrictionForm');
            if (form) {
                const startDateInput = form.querySelector('input[name="start_date"]');
                const endDateInput = form.querySelector('input[name="end_date"]');
                
                startDateInput.addEventListener('change', function() {
                    const startDate = new Date(this.value);
                    const minEndDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
                    endDateInput.min = minEndDate.toISOString().split('T')[0];
                    
                    if (endDateInput.value && new Date(endDateInput.value) <= startDate) {
                        endDateInput.value = '';
                    }
                });
            }
            
            // Auto-dismiss alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        });
        
        // Live countdown update
        <?php if ($currentRestriction && $daysRemaining !== null): ?>
        function updateCountdown() {
            const endDate = new Date('<?php echo $currentRestriction['restriction_end_date']; ?>T23:59:59');
            const now = new Date();
            const timeDiff = endDate.getTime() - now.getTime();
            
            if (timeDiff > 0) {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const countdownElements = document.querySelectorAll('.countdown-number');
                countdownElements.forEach(element => {
                    element.textContent = days;
                });
            } else {
                location.reload();
            }
        }
        
        setInterval(updateCountdown, 60000);
        <?php endif; ?>
    </script>
</body>
</html>