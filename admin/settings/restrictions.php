<?php
/**
<<<<<<< HEAD
 * System Restrictions Management - QuickBill 305
 * Super Admin only - Manage system access restrictions
=======
 * System Restrictions Management - QUICKBILL 305
 * Only accessible by Super Admin
 * Manages system restrictions and countdown warnings
>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
// Check if user is logged in and is Super Admin
=======
// Check if user is logged in
>>>>>>> c9ccaba (Initial commit)
if (!isLoggedIn()) {
    header('Location: ../../auth/login.php');
    exit();
}

<<<<<<< HEAD
=======
// Check if user is Super Admin ONLY
>>>>>>> c9ccaba (Initial commit)
if (!isSuperAdmin()) {
    setFlashMessage('error', 'Access denied. Super Admin privileges required.');
    header('Location: ../index.php');
    exit();
}

$currentUser = getCurrentUser();
$userDisplayName = getUserDisplayName($currentUser);

<<<<<<< HEAD
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
=======
$error = '';
$success = '';

try {
    $db = new Database();
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrfToken()) {
            $error = 'Security validation failed. Please try again.';
        } else {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'set_restriction') {
                $startDate = sanitizeInput($_POST['start_date'] ?? '');
                $months = intval($_POST['restriction_months'] ?? 0);
                $warningDays = intval($_POST['warning_days'] ?? 7);
                $reason = sanitizeInput($_POST['reason'] ?? '');
                
                // Validate input
                if (empty($startDate)) {
                    $error = 'Start date is required.';
                } elseif ($months < 2 || $months > 4) {
                    $error = 'Restriction period must be between 2 and 4 months.';
                } elseif ($warningDays < 1 || $warningDays > 30) {
                    $error = 'Warning period must be between 1 and 30 days.';
                } else {
                    // Validate start date
                    $startDateTime = DateTime::createFromFormat('Y-m-d', $startDate);
                    if (!$startDateTime) {
                        $error = 'Invalid start date format.';
                    } elseif ($startDateTime < new DateTime('today')) {
                        $error = 'Start date cannot be in the past.';
                    } else {
                        // Calculate end date from start date
                        $endDate = date('Y-m-d', strtotime($startDate . " +{$months} months"));
                        
                        try {
                            // Begin transaction
                            $db->execute("START TRANSACTION");
                            
                            // Deactivate any existing restrictions
                            $db->execute("UPDATE system_restrictions SET is_active = 0 WHERE is_active = 1");
                            
                            // Insert new restriction
                            $restrictionResult = $db->execute("
                                INSERT INTO system_restrictions 
                                (restriction_start_date, restriction_end_date, warning_days, is_active, created_by) 
                                VALUES (?, ?, ?, 1, ?)
                            ", [$startDate, $endDate, $warningDays, getCurrentUserId()]);
                            
                            if (!$restrictionResult) {
                                throw new Exception("Failed to create restriction record");
                            }
                            
                            // Update system settings
                            $settingResult = $db->execute("
                                UPDATE system_settings 
                                SET setting_value = ?, updated_by = ?, updated_at = NOW() 
                                WHERE setting_key = 'restriction_start_date'
                            ", [$startDate, getCurrentUserId()]);
                            
                            if (!$settingResult) {
                                // Insert if doesn't exist
                                $db->execute("
                                    INSERT INTO system_settings (setting_key, setting_value, setting_type, description, updated_by) 
                                    VALUES ('restriction_start_date', ?, 'date', 'System restriction start date', ?)
                                ", [$startDate, getCurrentUserId()]);
                            }
                            
                            // Reset system_restricted to false since we're scheduling a future restriction
                            $db->execute("
                                UPDATE system_settings 
                                SET setting_value = 'false', updated_by = ?, updated_at = NOW() 
                                WHERE setting_key = 'system_restricted'
                            ", [getCurrentUserId()]);
                            
                            // Commit transaction
                            $db->execute("COMMIT");
                            
                            // Log action
                            logUserAction('RESTRICTION_SCHEDULED', 'system_restrictions', null, null, [
                                'restriction_months' => $months,
                                'warning_days' => $warningDays,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'reason' => $reason
                            ]);
                            
                            $startDateFormatted = date('F j, Y', strtotime($startDate));
                            $endDateFormatted = date('F j, Y', strtotime($endDate));
                            
                            $success = "System restriction has been scheduled! Users will see countdown warnings {$warningDays} days before {$startDateFormatted}. The system will be restricted from {$startDateFormatted} to {$endDateFormatted}.";
                            
                        } catch (Exception $e) {
                            $db->execute("ROLLBACK");
                            $error = "Failed to set restriction: " . $e->getMessage();
                            writeLog("Restriction setting error: " . $e->getMessage(), 'ERROR');
                        }
                    }
                }
            } elseif ($action === 'lift_restriction') {
                try {
                    // Begin transaction
                    $db->execute("START TRANSACTION");
                    
                    // Deactivate current restrictions
                    $db->execute("UPDATE system_restrictions SET is_active = 0 WHERE is_active = 1");
                    
                    // Update system restricted setting
                    $db->execute("
                        UPDATE system_settings 
                        SET setting_value = 'false', updated_by = ?, updated_at = NOW() 
                        WHERE setting_key = 'system_restricted'
                    ", [getCurrentUserId()]);
                    
                    // Commit transaction
                    $db->execute("COMMIT");
                    
                    // Log action
                    logUserAction('RESTRICTION_LIFTED', 'system_restrictions', null);
                    
                    $success = 'System restriction has been lifted successfully! All users can now access the system.';
                    
                } catch (Exception $e) {
                    $db->execute("ROLLBACK");
                    $error = "Failed to lift restriction: " . $e->getMessage();
                }
            } elseif ($action === 'activate_restriction') {
                try {
                    // Update system to restricted mode
                    $db->execute("
                        UPDATE system_settings 
                        SET setting_value = 'true', updated_by = ?, updated_at = NOW() 
                        WHERE setting_key = 'system_restricted'
                    ", [getCurrentUserId()]);
                    
                    // Log action
                    logUserAction('RESTRICTION_ACTIVATED', 'system_settings', null);
                    
                    $success = 'System restriction has been activated manually! All users (except Super Admin) are now blocked.';
                    
                } catch (Exception $e) {
                    $error = "Failed to activate restriction: " . $e->getMessage();
                }
            }
        }
    }
    
    // Get current restriction info with enhanced date calculations
    $currentRestriction = $db->fetchRow("
        SELECT sr.*, ss.setting_value as system_restricted,
               DATEDIFF(sr.restriction_start_date, CURDATE()) as days_until_start,
               DATEDIFF(sr.restriction_end_date, CURDATE()) as days_until_end,
               DATEDIFF(CURDATE(), sr.restriction_start_date) as days_since_start,
               CASE 
                   WHEN CURDATE() < sr.restriction_start_date THEN 'before_start'
                   WHEN CURDATE() >= sr.restriction_start_date AND CURDATE() <= sr.restriction_end_date THEN 'active_period'
                   WHEN CURDATE() > sr.restriction_end_date THEN 'expired'
               END as restriction_phase,
               u.first_name, u.last_name
        FROM system_restrictions sr
        LEFT JOIN system_settings ss ON ss.setting_key = 'system_restricted'
        LEFT JOIN users u ON sr.created_by = u.user_id
        WHERE sr.is_active = 1
        ORDER BY sr.created_at DESC
        LIMIT 1
    ");
    
    // Get restriction history
    $restrictionHistory = $db->fetchAll("
        SELECT sr.*, u.first_name, u.last_name,
               DATEDIFF(sr.restriction_end_date, sr.restriction_start_date) as total_days,
               CASE 
                   WHEN CURDATE() < sr.restriction_start_date THEN 'scheduled'
                   WHEN CURDATE() >= sr.restriction_start_date AND CURDATE() <= sr.restriction_end_date THEN 'active'
                   WHEN CURDATE() > sr.restriction_end_date THEN 'expired'
               END as status_phase
        FROM system_restrictions sr
        LEFT JOIN users u ON sr.created_by = u.user_id
        ORDER BY sr.created_at DESC
        LIMIT 10
    ");
    
    // Calculate restriction status with enhanced logic
    $restrictionStatus = 'none';
    $isSystemRestricted = false;
    $daysUntilStart = null;
    $daysUntilEnd = null;
    $daysSinceStart = null;
    $warningActive = false;
    $restrictionActive = false;
    $restrictionPhase = 'none';
    
    if ($currentRestriction) {
        $isSystemRestricted = ($currentRestriction['system_restricted'] === 'true');
        $daysUntilStart = intval($currentRestriction['days_until_start']);
        $daysUntilEnd = intval($currentRestriction['days_until_end']);
        $daysSinceStart = intval($currentRestriction['days_since_start']);
        $warningDays = intval($currentRestriction['warning_days']);
        $restrictionPhase = $currentRestriction['restriction_phase'];
        
        $today = new DateTime();
        $startDate = new DateTime($currentRestriction['restriction_start_date']);
        $endDate = new DateTime($currentRestriction['restriction_end_date']);
        
        if ($restrictionPhase === 'before_start') {
            // Before start date
            if ($daysUntilStart <= $warningDays) {
                $restrictionStatus = 'warning_countdown';
                $warningActive = true;
            } else {
                $restrictionStatus = 'scheduled';
            }
        } elseif ($restrictionPhase === 'active_period') {
            // Between start and end date
            if ($isSystemRestricted) {
                $restrictionStatus = 'active_enforced';
                $restrictionActive = true;
            } else {
                $restrictionStatus = 'active_pending';
            }
        } else {
            // After end date
            $restrictionStatus = 'expired';
        }
    }
    
} catch (Exception $e) {
    $error = 'System error: ' . $e->getMessage();
    writeLog("Restrictions page error: " . $e->getMessage(), 'ERROR');
}
>>>>>>> c9ccaba (Initial commit)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Restrictions - <?php echo APP_NAME; ?></title>
    
<<<<<<< HEAD
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
        
=======
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
>>>>>>> c9ccaba (Initial commit)
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
<<<<<<< HEAD
        
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
=======

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Emoji Icons */
        .icon-dashboard::before { content: "📊"; }
        .icon-users::before { content: "👥"; }
        .icon-building::before { content: "🏢"; }
        .icon-home::before { content: "🏠"; }
        .icon-map::before { content: "🗺️"; }
        .icon-invoice::before { content: "📄"; }
        .icon-credit::before { content: "💳"; }
        .icon-tags::before { content: "🏷️"; }
        .icon-chart::before { content: "📈"; }
        .icon-bell::before { content: "🔔"; }
        .icon-cog::before { content: "⚙️"; }
        .icon-receipt::before { content: "🧾"; }
        .icon-menu::before { content: "☰"; }
        .icon-logout::before { content: "🚪"; }
        .icon-shield::before { content: "🛡️"; }
        .icon-lock::before { content: "🔒"; }
        .icon-unlock::before { content: "🔓"; }
        .icon-warning::before { content: "⚠️"; }
        .icon-check::before { content: "✅"; }
        .icon-times::before { content: "❌"; }
        .icon-clock::before { content: "⏰"; }
        .icon-calendar::before { content: "📅"; }
        .icon-history::before { content: "📜"; }
        .icon-plus::before { content: "➕"; }
        .icon-user::before { content: "👤"; }
        .icon-chevron-down::before { content: "⌄"; }
        .icon-timer::before { content: "⏱️"; }
        .icon-play::before { content: "▶️"; }

        /* Top Navigation */
        .top-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
>>>>>>> c9ccaba (Initial commit)
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
<<<<<<< HEAD
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
=======
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

>>>>>>> c9ccaba (Initial commit)
        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
<<<<<<< HEAD
        
        .brand {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
=======

        .toggle-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 18px;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .toggle-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
>>>>>>> c9ccaba (Initial commit)
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
<<<<<<< HEAD
        
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
        
=======

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 10px;
            transition: all 0.3s;
            position: relative;
        }

        .user-profile:hover {
            background: rgba(255,255,255,0.1);
        }

>>>>>>> c9ccaba (Initial commit)
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
<<<<<<< HEAD
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
=======
            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: white;
        }

        .user-role {
            font-size: 12px;
            opacity: 0.8;
            color: rgba(255,255,255,0.8);
        }

        /* Layout */
        .container {
            margin-top: 80px;
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            color: white;
            transition: all 0.3s ease;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar.hidden {
            width: 0;
            min-width: 0;
        }

        .sidebar-content {
            width: 280px;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .sidebar.hidden .sidebar-content {
            opacity: 0;
            transform: translateX(-20px);
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-title {
            color: #a0aec0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 0 20px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .nav-item {
            margin-bottom: 2px;
        }

        .nav-link {
            color: #e2e8f0;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }

        .nav-link.active {
            background: rgba(102, 126, 234, 0.3);
            color: white;
            border-left-color: #667eea;
        }

        .nav-icon {
            display: inline-block;
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            min-width: 0;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
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

        .page-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .page-subtitle {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .page-icon {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 60px;
            opacity: 0.3;
            z-index: 1;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 25px;
        }

        /* Status Cards */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }

        .status-card:hover {
            transform: translateY(-5px);
        }

        .status-card.success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .status-card.warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .status-card.danger {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }

        .status-card.info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }

        .status-card.purple {
            background: linear-gradient(135deg, #805ad5 0%, #667eea 100%);
            color: white;
        }

        .status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .status-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .status-icon {
            font-size: 24px;
            opacity: 0.8;
        }

        .status-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .status-description {
            font-size: 13px;
            opacity: 0.8;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .btn-secondary {
            background: #718096;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
            border-left: 4px solid #48bb78;
        }

        .alert-danger {
            background: rgba(229, 62, 62, 0.1);
            color: #c53030;
            border-left: 4px solid #e53e3e;
        }

        .alert-warning {
            background: rgba(237, 137, 54, 0.1);
            color: #dd6b20;
            border-left: 4px solid #ed8936;
        }

        .alert-info {
            background: rgba(66, 153, 225, 0.1);
            color: #3182ce;
            border-left: 4px solid #4299e1;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        .table th {
            background: #f7fafc;
            color: #2d3748;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background: #f7fafc;
        }

        /* Status Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(72, 187, 120, 0.2);
            color: #38a169;
        }

        .badge-warning {
            background: rgba(237, 137, 54, 0.2);
            color: #dd6b20;
        }

        .badge-danger {
            background: rgba(229, 62, 62, 0.2);
            color: #c53030;
        }

        .badge-info {
            background: rgba(66, 153, 225, 0.2);
            color: #3182ce;
        }

        .badge-secondary {
            background: rgba(113, 128, 150, 0.2);
            color: #718096;
        }

        .badge-purple {
            background: rgba(128, 90, 213, 0.2);
            color: #805ad5;
        }

        /* Countdown */
        .countdown {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .countdown-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .countdown-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .countdown-label {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: calc(100vh - 80px);
                top: 80px;
                left: 0;
                z-index: 999;
                transform: translateX(-100%);
                width: 280px !important;
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 24px;
            }

            .page-icon {
                display: none;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .btn.loading {
            position: relative;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Enhanced Visual Effects */
        .glow-effect {
            animation: glow 2s infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 5px rgba(102, 126, 234, 0.5); }
            to { box-shadow: 0 0 20px rgba(102, 126, 234, 0.8); }
        }

        .pulse-effect {
            animation: pulse 2s infinite;
        }

>>>>>>> c9ccaba (Initial commit)
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
<<<<<<< HEAD
        
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
=======

        /* Live Countdown Display */
        .live-countdown {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .countdown-unit {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 10px;
            min-width: 60px;
        }

        .countdown-number {
            font-size: 20px;
            font-weight: bold;
        }

        .countdown-label {
            font-size: 10px;
            opacity: 0.8;
            text-transform: uppercase;
            margin-top: 2px;
>>>>>>> c9ccaba (Initial commit)
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
<<<<<<< HEAD
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
=======
            <button class="toggle-btn" onclick="toggleSidebar()" id="toggleBtn" title="Toggle Sidebar">
                <span class="icon-menu"></span>
            </button>

            <a href="../index.php" class="brand">
                <span class="icon-receipt"></span>
                <?php echo APP_NAME; ?>
            </a>
        </div>

        <div class="user-section">
            <div class="user-profile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars(getCurrentUserRole()); ?></div>
                </div>
>>>>>>> c9ccaba (Initial commit)
            </div>
        </div>
    </div>

<<<<<<< HEAD
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
=======
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-content">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="../index.php" class="nav-link">
                            <span class="nav-icon icon-dashboard"></span>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Core Management -->
                <div class="nav-section">
                    <div class="nav-title">Core Management</div>
                    <div class="nav-item">
                        <a href="../users/index.php" class="nav-link">
                            <span class="nav-icon icon-users"></span>
                            Users
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../businesses/index.php" class="nav-link">
                            <span class="nav-icon icon-building"></span>
                            Businesses
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../properties/index.php" class="nav-link">
                            <span class="nav-icon icon-home"></span>
                            Properties
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../zones/index.php" class="nav-link">
                            <span class="nav-icon icon-map"></span>
                            Zones & Areas
                        </a>
                    </div>
                </div>

                <!-- Billing & Payments -->
                <div class="nav-section">
                    <div class="nav-title">Billing & Payments</div>
                    <div class="nav-item">
                        <a href="../billing/index.php" class="nav-link">
                            <span class="nav-icon icon-invoice"></span>
                            Billing
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../payments/index.php" class="nav-link">
                            <span class="nav-icon icon-credit"></span>
                            Payments
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../fee_structure/index.php" class="nav-link">
                            <span class="nav-icon icon-tags"></span>
                            Fee Structure
                        </a>
                    </div>
                </div>

                <!-- Reports & System -->
                <div class="nav-section">
                    <div class="nav-title">Reports & System</div>
                    <div class="nav-item">
                        <a href="../reports/index.php" class="nav-link">
                            <span class="nav-icon icon-chart"></span>
                            Reports
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../notifications/index.php" class="nav-link">
                            <span class="nav-icon icon-bell"></span>
                            Notifications
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="index.php" class="nav-link">
                            <span class="nav-icon icon-cog"></span>
                            Settings
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="restrictions.php" class="nav-link active">
                            <span class="nav-icon icon-shield"></span>
                            System Restrictions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-icon">
                    <span class="icon-shield"></span>
                </div>
                <h1 class="page-title">🛡️ System Restrictions</h1>
                <p class="page-subtitle">Schedule and manage system access restrictions with countdown warnings</p>
            </div>

            <!-- Flash Messages -->
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="icon-times"></span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span class="icon-check"></span>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Current Status -->
            <div class="status-grid">
                <?php if ($restrictionStatus === 'active_enforced'): ?>
                    <div class="status-card danger pulse-effect">
                        <div class="status-header">
                            <div class="status-title">System Status</div>
                            <div class="status-icon">
                                <span class="icon-lock"></span>
                            </div>
                        </div>
                        <div class="status-value">RESTRICTED</div>
                        <div class="status-description">System is currently restricted. Only Super Admin can access.</div>
                    </div>
                <?php elseif ($restrictionStatus === 'warning_countdown'): ?>
                    <div class="status-card warning glow-effect">
                        <div class="status-header">
                            <div class="status-title">Countdown Active</div>
                            <div class="status-icon">
                                <span class="icon-timer"></span>
                            </div>
                        </div>
                        <div class="status-value"><?php echo $daysUntilStart; ?> DAYS</div>
                        <div class="status-description">Until restriction begins on <?php echo date('M j, Y', strtotime($currentRestriction['restriction_start_date'])); ?></div>
                        <?php if ($daysUntilStart <= 7): ?>
                            <div class="live-countdown" id="liveCountdown">
                                <!-- JavaScript will populate this -->
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($restrictionStatus === 'scheduled'): ?>
                    <div class="status-card info">
                        <div class="status-header">
                            <div class="status-title">Restriction Scheduled</div>
                            <div class="status-icon">
                                <span class="icon-calendar"></span>
                            </div>
                        </div>
                        <div class="status-value"><?php echo $daysUntilStart; ?> DAYS</div>
                        <div class="status-description">Until warnings start for restriction on <?php echo date('M j, Y', strtotime($currentRestriction['restriction_start_date'])); ?></div>
                    </div>
                <?php elseif ($restrictionStatus === 'active_pending'): ?>
                    <div class="status-card purple glow-effect">
                        <div class="status-header">
                            <div class="status-title">Restriction Period Active</div>
                            <div class="status-icon">
                                <span class="icon-clock"></span>
                            </div>
                        </div>
                        <div class="status-value">NOT ENFORCED</div>
                        <div class="status-description">In restriction period but not activated. <?php echo $daysUntilEnd; ?> days until end.</div>
                    </div>
                <?php elseif ($restrictionStatus === 'expired'): ?>
                    <div class="status-card secondary">
                        <div class="status-header">
                            <div class="status-title">Restriction Expired</div>
                            <div class="status-icon">
                                <span class="icon-history"></span>
                            </div>
                        </div>
                        <div class="status-value">EXPIRED</div>
                        <div class="status-description">Restriction period ended on <?php echo date('M j, Y', strtotime($currentRestriction['restriction_end_date'])); ?></div>
                    </div>
                <?php else: ?>
                    <div class="status-card success">
                        <div class="status-header">
                            <div class="status-title">System Status</div>
                            <div class="status-icon">
                                <span class="icon-unlock"></span>
                            </div>
                        </div>
                        <div class="status-value">UNRESTRICTED</div>
                        <div class="status-description">System is fully accessible to all users</div>
                    </div>
                <?php endif; ?>

                <?php if ($currentRestriction): ?>
                    <div class="status-card info">
                        <div class="status-header">
                            <div class="status-title">Start Date</div>
                            <div class="status-icon">
                                <span class="icon-calendar"></span>
                            </div>
                        </div>
                        <div class="status-value"><?php echo date('M j', strtotime($currentRestriction['restriction_start_date'])); ?></div>
                        <div class="status-description"><?php echo date('Y', strtotime($currentRestriction['restriction_start_date'])); ?></div>
                    </div>

                    <div class="status-card info">
                        <div class="status-header">
                            <div class="status-title">End Date</div>
                            <div class="status-icon">
                                <span class="icon-calendar"></span>
                            </div>
                        </div>
                        <div class="status-value"><?php echo date('M j', strtotime($currentRestriction['restriction_end_date'])); ?></div>
                        <div class="status-description"><?php echo date('Y', strtotime($currentRestriction['restriction_end_date'])); ?></div>
                    </div>

                    <div class="status-card warning">
                        <div class="status-header">
                            <div class="status-title">Warning Period</div>
                            <div class="status-icon">
                                <span class="icon-bell"></span>
                            </div>
                        </div>
                        <div class="status-value"><?php echo $currentRestriction['warning_days']; ?> DAYS</div>
                        <div class="status-description">Warning period before restriction starts</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Current Restriction Actions -->
            <?php if ($currentRestriction): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="icon-cog"></span>
                            Current Restriction Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <?php if ($restrictionPhase === 'active_period' && !$isSystemRestricted): ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to activate the restriction now? This will block all users except Super Admin.')">
                                    <?php echo csrfField(); ?>
                                    <input type="hidden" name="action" value="activate_restriction">
                                    <button type="submit" class="btn btn-warning">
                                        <span class="icon-lock"></span>
                                        Activate Restriction Now
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to lift the restriction? This will allow all users to access the system again.')">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="action" value="lift_restriction">
                                <button type="submit" class="btn btn-success">
                                    <span class="icon-unlock"></span>
                                    Lift Restriction
                                </button>
                            </form>
                        </div>

                        <div style="margin-top: 20px; padding: 15px; background: #f7fafc; border-radius: 10px;">
                            <strong>Current Restriction Details:</strong><br>
                            <small class="text-muted">
                                Created by: <?php echo htmlspecialchars($currentRestriction['first_name'] . ' ' . $currentRestriction['last_name']); ?><br>
                                Start Date: <?php echo date('M j, Y \a\t g:i A', strtotime($currentRestriction['restriction_start_date'])); ?><br>
                                End Date: <?php echo date('M j, Y \a\t g:i A', strtotime($currentRestriction['restriction_end_date'])); ?><br>
                                Warning Period: <?php echo $currentRestriction['warning_days']; ?> days before start<br>
                                Phase: <strong><?php echo ucfirst(str_replace('_', ' ', $restrictionPhase)); ?></strong>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Set New Restriction -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="icon-plus"></span>
                        Schedule New System Restriction
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="restrictionForm">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="set_restriction">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date" class="form-label">
                                    <span class="icon-calendar"></span>
                                    Start Date
                                </label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <small style="color: #718096; font-size: 12px; margin-top: 5px; display: block;">
                                    Restriction will start at midnight on this date
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="restriction_months" class="form-label">
                                    <span class="icon-timer"></span>
                                    Duration (Months)
                                </label>
                                <select name="restriction_months" id="restriction_months" class="form-control" required>
                                    <option value="">Select duration...</option>
                                    <option value="2">2 Months</option>
                                    <option value="3">3 Months</option>
                                    <option value="4">4 Months</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="warning_days" class="form-label">
                                    <span class="icon-bell"></span>
                                    Warning Days Before Start
                                </label>
                                <select name="warning_days" id="warning_days" class="form-control" required>
                                    <option value="">Select warning period...</option>
                                    <option value="7">7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="21">21 Days</option>
                                    <option value="30">30 Days</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason" class="form-label">Reason for Restriction (Optional)</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter the reason for scheduling this restriction..."></textarea>
                        </div>

                        <!-- Calculated End Date Display -->
                        <div id="calculated-info" style="display: none; background: #e6fffa; color: #234e52; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #38b2ac;">
                            <h6 style="margin-bottom: 10px;">📊 Calculated Schedule:</h6>
                            <div id="schedule-details"></div>
                        </div>

                        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                            <strong>⚠️ How This Works:</strong>
                            <ul style="margin: 10px 0 0 20px; padding: 0;">
                                <li>Users will see countdown warnings during the warning period</li>
                                <li>System will automatically show warnings but won't block access until you manually activate</li>
                                <li>Only Super Admin can access the system once restriction is activated</li>
                                <li>You can activate the restriction anytime during the restriction period</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to schedule this restriction? Users will start seeing countdown warnings during the warning period.')">
                            <span class="icon-play"></span>
                            Schedule System Restriction
                        </button>
                    </form>
                </div>
            </div>

            <!-- Restriction History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="icon-history"></span>
                        Restriction History
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($restrictionHistory)): ?>
                        <div class="text-center" style="padding: 40px; color: #718096;">
                            <span class="icon-history" style="font-size: 48px; opacity: 0.5;"></span>
                            <p style="margin-top: 15px;">No restriction history available.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Duration</th>
                                        <th>Warning Days</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Phase</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($restrictionHistory as $restriction): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y', strtotime($restriction['restriction_start_date'])); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($restriction['restriction_end_date'])); ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo ceil($restriction['total_days'] / 30); ?> months
                                                </span>
                                            </td>
                                            <td><?php echo $restriction['warning_days']; ?> days</td>
                                            <td><?php echo htmlspecialchars(($restriction['first_name'] ?? '') . ' ' . ($restriction['last_name'] ?? '')); ?></td>
                                            <td>
                                                <?php if ($restriction['is_active']): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $phase = $restriction['status_phase'];
                                                $badgeClass = '';
                                                switch ($phase) {
                                                    case 'scheduled':
                                                        $badgeClass = 'badge-info';
                                                        break;
                                                    case 'active':
                                                        $badgeClass = 'badge-warning';
                                                        break;
                                                    case 'expired':
                                                        $badgeClass = 'badge-secondary';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($phase); ?>
                                                </span>
                                            </td>
                                            <td><?php echo timeAgo($restriction['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
>>>>>>> c9ccaba (Initial commit)
            </div>
        </div>
    </div>

<<<<<<< HEAD
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
=======
    <script>
        // Global variables
        let isMobile = window.innerWidth <= 768;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            checkMobile();
            restoreSidebarState();
            
            // Setup form change listeners
            setupFormListeners();
            
            // Setup live countdown if needed
            <?php if ($restrictionStatus === 'warning_countdown' && $daysUntilStart <= 7): ?>
            setupLiveCountdown();
            <?php endif; ?>
        });

        // Check if mobile
        function checkMobile() {
            isMobile = window.innerWidth <= 768;
        }

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            
            if (isMobile) {
                sidebar.classList.toggle('mobile-show');
            } else {
                sidebar.classList.toggle('hidden');
                localStorage.setItem('sidebarHidden', sidebar.classList.contains('hidden'));
            }
        }

        // Restore sidebar state
        function restoreSidebarState() {
            const sidebar = document.getElementById('sidebar');
            const sidebarHidden = localStorage.getItem('sidebarHidden');
            
            if (!isMobile && sidebarHidden === 'true') {
                sidebar.classList.add('hidden');
            }
        }

        // Setup form listeners
        function setupFormListeners() {
            const startDateInput = document.getElementById('start_date');
            const monthsSelect = document.getElementById('restriction_months');
            const warningDaysSelect = document.getElementById('warning_days');
            
            [startDateInput, monthsSelect, warningDaysSelect].forEach(element => {
                if (element) {
                    element.addEventListener('change', updateCalculatedInfo);
                }
            });
        }

        // Update calculated information
        function updateCalculatedInfo() {
            const startDate = document.getElementById('start_date').value;
            const months = parseInt(document.getElementById('restriction_months').value);
            const warningDays = parseInt(document.getElementById('warning_days').value);
            
            if (startDate && months && warningDays) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setMonth(end.getMonth() + months);
                
                const warningStart = new Date(start);
                warningStart.setDate(warningStart.getDate() - warningDays);
                
                const today = new Date();
                const daysUntilWarning = Math.ceil((warningStart - today) / (1000 * 60 * 60 * 24));
                const daysUntilStart = Math.ceil((start - today) / (1000 * 60 * 60 * 24));
                
                const calculatedInfo = document.getElementById('calculated-info');
                const scheduleDetails = document.getElementById('schedule-details');
                
                scheduleDetails.innerHTML = `
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div>
                            <strong>Warning Starts:</strong><br>
                            ${warningStart.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                            <small>(${daysUntilWarning > 0 ? `in ${daysUntilWarning} days` : 'Started'})</small>
                        </div>
                        <div>
                            <strong>Restriction Starts:</strong><br>
                            ${start.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                            <small>(${daysUntilStart > 0 ? `in ${daysUntilStart} days` : 'Today/Past'})</small>
                        </div>
                        <div>
                            <strong>Restriction Ends:</strong><br>
                            ${end.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                            <small>(${months} months duration)</small>
                        </div>
                    </div>
                `;
                
                calculatedInfo.style.display = 'block';
            } else {
                document.getElementById('calculated-info').style.display = 'none';
            }
        }

        // Setup live countdown
        function setupLiveCountdown() {
            const restrictionStartDate = new Date('<?php echo $currentRestriction['restriction_start_date']; ?>T00:00:00');
            
            function updateCountdown() {
                const now = new Date();
                const timeLeft = restrictionStartDate - now;
                
                if (timeLeft <= 0) {
                    location.reload(); // Restriction has started
                    return;
                }
                
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                const liveCountdown = document.getElementById('liveCountdown');
                if (liveCountdown) {
                    liveCountdown.innerHTML = `
                        <div class="countdown-unit">
                            <div class="countdown-number">${days}</div>
                            <div class="countdown-label">Days</div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-number">${hours.toString().padStart(2, '0')}</div>
                            <div class="countdown-label">Hours</div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-number">${minutes.toString().padStart(2, '0')}</div>
                            <div class="countdown-label">Minutes</div>
                        </div>
                        <div class="countdown-unit">
                            <div class="countdown-number">${seconds.toString().padStart(2, '0')}</div>
                            <div class="countdown-label">Seconds</div>
                        </div>
                    `;
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Form submission with loading state
        document.getElementById('restrictionForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            checkMobile();
        });

        // Enhanced visual effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to status cards
            const statusCards = document.querySelectorAll('.status-card');
            statusCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click feedback to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });
        });
>>>>>>> c9ccaba (Initial commit)
    </script>
</body>
</html>