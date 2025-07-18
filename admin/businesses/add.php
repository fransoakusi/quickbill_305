<?php
/**
 * Business Management - Add New Business
 * QUICKBILL 305 - Admin Panel
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

// Check authentication and permissions
requireLogin();
if (!hasPermission('businesses.create')) {
    setFlashMessage('error', 'Access denied. You do not have permission to add businesses.');
    header('Location: index.php');
    exit();
}

// Check session expiration
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // Session expired (30 minutes)
    session_unset();
    session_destroy();
    setFlashMessage('error', 'Your session has expired. Please log in again.');
    header('Location: ../../index.php');
    exit();
}
// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

$pageTitle = 'Register New Business';
$currentUser = getCurrentUser();
$userDisplayName = getUserDisplayName($currentUser);

// Initialize variables
$errors = [];
$formData = [
    'business_name' => '',
    'owner_name' => '',
    'business_type' => '',
    'category' => '',
    'telephone' => '',
    'exact_location' => '',
    'latitude' => '',
    'longitude' => '',
    'old_bill' => '0.00',
    'previous_payments' => '0.00',
    'arrears' => '0.00',
    'current_bill' => '0.00',
    'batch' => '',
    'status' => 'Active',
    'zone_id' => '',
    'sub_zone_id' => ''
];

try {
    $db = new Database();
    
    // Get business fee structure for dynamic billing
    $businessFees = $db->fetchAll("SELECT * FROM business_fee_structure WHERE is_active = 1 ORDER BY business_type, category");
    
    // Get zones for dropdown
    $zones = $db->fetchAll("SELECT * FROM zones ORDER BY zone_name");
    
    // Get sub-zones for dropdown
    $subZones = $db->fetchAll("SELECT sz.*, z.zone_name FROM sub_zones sz 
                               LEFT JOIN zones z ON sz.zone_id = z.zone_id 
                               ORDER BY z.zone_name, sz.sub_zone_name");
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and sanitize input
        $formData['business_name'] = sanitizeInput($_POST['business_name'] ?? '');
        $formData['owner_name'] = sanitizeInput($_POST['owner_name'] ?? '');
        $formData['business_type'] = sanitizeInput($_POST['business_type'] ?? '');
        $formData['category'] = sanitizeInput($_POST['category'] ?? '');
        $formData['telephone'] = sanitizeInput($_POST['telephone'] ?? '');
        $formData['exact_location'] = sanitizeInput($_POST['exact_location'] ?? '');
        $formData['latitude'] = sanitizeInput($_POST['latitude'] ?? '');
        $formData['longitude'] = sanitizeInput($_POST['longitude'] ?? '');
        $formData['old_bill'] = floatval($_POST['old_bill'] ?? 0);
        $formData['previous_payments'] = floatval($_POST['previous_payments'] ?? 0);
        $formData['arrears'] = floatval($_POST['arrears'] ?? 0);
        $formData['current_bill'] = floatval($_POST['current_bill'] ?? 0);
        $formData['batch'] = sanitizeInput($_POST['batch'] ?? '');
        $formData['status'] = sanitizeInput($_POST['status'] ?? 'Active');
        $formData['zone_id'] = intval($_POST['zone_id'] ?? 0);
        $formData['sub_zone_id'] = intval($_POST['sub_zone_id'] ?? 0);
        
        // Validation
        if (empty($formData['business_name'])) {
            $errors[] = 'Business name is required.';
        }
        
        if (empty($formData['owner_name'])) {
            $errors[] = 'Owner name is required.';
        }
        
        if (empty($formData['business_type'])) {
            $errors[] = 'Business type is required.';
        }
        
        if (empty($formData['category'])) {
            $errors[] = 'Business category is required.';
        }
        
        if (!empty($formData['telephone']) && !preg_match('/^[\+]?[0-9\-\s\(\)]+$/', $formData['telephone'])) {
            $errors[] = 'Please enter a valid telephone number.';
        }
        
        if ($formData['zone_id'] <= 0) {
            $errors[] = 'Please select a zone.';
        }
        
        // Check if business name already exists
        if (empty($errors)) {
            $existingBusiness = $db->fetchRow(
                "SELECT business_id FROM businesses WHERE business_name = ?", 
                [$formData['business_name']]
            );
            
            if ($existingBusiness) {
                $errors[] = 'A business with this name already exists.';
            }
        }
        
        // If no errors, save the business
        if (empty($errors)) {
            try {
                $db->beginTransaction();
                
                // Calculate amount payable
                $amount_payable = $formData['old_bill'] + $formData['arrears'] + $formData['current_bill'] - $formData['previous_payments'];
                
                // Insert business
                $query = "INSERT INTO businesses (
                    business_name, owner_name, business_type, category, telephone, 
                    exact_location, latitude, longitude, old_bill, previous_payments, 
                    arrears, current_bill, amount_payable, batch, status, zone_id, 
                    sub_zone_id, created_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $params = [
                    $formData['business_name'],
                    $formData['owner_name'],
                    $formData['business_type'],
                    $formData['category'],
                    $formData['telephone'],
                    $formData['exact_location'],
                    !empty($formData['latitude']) ? $formData['latitude'] : null,
                    !empty($formData['longitude']) ? $formData['longitude'] : null,
                    $formData['old_bill'],
                    $formData['previous_payments'],
                    $formData['arrears'],
                    $formData['current_bill'],
                    $amount_payable,
                    $formData['batch'],
                    $formData['status'],
                    $formData['zone_id'] > 0 ? $formData['zone_id'] : null,
                    $formData['sub_zone_id'] > 0 ? $formData['sub_zone_id'] : null,
                    $currentUser['user_id']
                ];
                
                // Alternative approach - direct PDO usage
                try {
                    $pdo = $db->getConnection();
                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);
                    $businessId = $pdo->lastInsertId();
                } catch (Exception $e) {
                    // Fallback if getConnection() doesn't exist
                    $businessId = 1; // temporary - replace with actual insert method
                    // You'll need to replace this with the correct Database class method
                    throw new Exception("Please update insert method for your Database class");
                }
                
                // Log the action
                writeLog("Business created: {$formData['business_name']} (ID: $businessId) by user {$currentUser['username']}", 'INFO');
                
                $db->commit();
                
                setFlashMessage('success', 'Business registered successfully!');
                header('Location: view.php?id=' . $businessId);
                exit();
                
            } catch (Exception $e) {
                $db->rollback();
                writeLog("Error creating business: " . $e->getMessage(), 'ERROR');
                $errors[] = 'An error occurred while registering the business. Please try again.';
            }
        }
    }
    
} catch (Exception $e) {
    writeLog("Business add page error: " . $e->getMessage(), 'ERROR');
    $errors[] = 'An error occurred while loading the page. Please try again.';
}

// Prepare business types for JavaScript
$businessTypesJson = json_encode($businessFees);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Multiple Icon Sources -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
    
    <!-- Bootstrap for backup -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Custom Icons (fallback if Font Awesome fails) */
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
        .icon-user::before { content: "👤"; }
        .icon-plus::before { content: "➕"; }
        .icon-save::before { content: "💾"; }
        .icon-location::before { content: "📍"; }
        .icon-phone::before { content: "📞"; }
        .icon-money::before { content: "💰"; }
        
        /* Top Navigation - Same as index.php */
        .top-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .toggle-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 18px;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .toggle-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
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
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
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
        
        .dropdown-arrow {
            margin-left: 8px;
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        /* User Dropdown - Same as index.php */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1001;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .dropdown-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            margin: 0 auto 10px;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .dropdown-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dropdown-role {
            font-size: 12px;
            opacity: 0.9;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 15px;
            display: inline-block;
        }
        
        .dropdown-menu {
            padding: 0;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #2d3748;
            text-decoration: none;
            transition: all 0.3s;
            border-bottom: 1px solid #f7fafc;
        }
        
        .dropdown-item:hover {
            background: #f7fafc;
            color: #667eea;
            transform: translateX(5px);
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }
        
        .dropdown-item.logout {
            color: #e53e3e;
            border-top: 2px solid #fed7d7;
        }
        
        .dropdown-item.logout:hover {
            background: #fed7d7;
            color: #c53030;
        }
        
        /* Layout */
        .container {
            margin-top: 80px;
            display: flex;
            min-height: calc(100vh - 80px);
        }
        
        /* Sidebar - Same as index.php */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            color: white;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .sidebar.hidden {
            width: 0;
            min-width: 0;
        }
        
        .sidebar-content {
            width: 280px;
            padding: 20px 0;
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
        }
        
        /* Breadcrumb */
        .breadcrumb-nav {
            background: white;
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: #64748b;
        }
        
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }
        
        .breadcrumb-current {
            color: #2d3748;
            font-weight: 600;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        
        .back-btn {
            background: #64748b;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #475569;
            transform: translateY(-2px);
            color: white;
        }
        
        /* Form Styles */
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row.single {
            grid-template-columns: 1fr;
        }
        
        .form-row.triple {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .required {
            color: #e53e3e;
        }
        
        .form-control {
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
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-control:invalid {
            border-color: #e53e3e;
        }
        
        .form-control.error {
            border-color: #e53e3e;
            background: #fef2f2;
        }
        
        .form-help {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
        
        /* Location Picker */
        .location-picker {
            position: relative;
        }
        
        .location-input-group {
            display: flex;
            gap: 10px;
        }
        
        .location-btn {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .location-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
        }
        
        .location-status {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            display: none;
        }
        
        .location-status.success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .location-status.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Dynamic Fee Display */
        .fee-display {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-top: 15px;
            display: none;
        }
        
        .fee-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .fee-description {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #9ae6b4;
            color: #065f46;
        }
        
        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        
        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #475569;
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
            color: white;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            margin-top: 30px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100%;
                z-index: 999;
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar.hidden {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .form-row.triple {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .location-input-group {
                flex-direction: column;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()" id="toggleBtn">
                <i class="fas fa-bars"></i>
                <span class="icon-menu" style="display: none;"></span>
            </button>
            
            <a href="../index.php" class="brand">
                <i class="fas fa-receipt"></i>
                <span class="icon-receipt" style="display: none;"></span>
                <?php echo APP_NAME; ?>
            </a>
        </div>
        
        <div class="user-section">
            <!-- Notification Bell -->
            <div style="position: relative; margin-right: 10px;">
                <a href="../notifications/index.php" style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    font-size: 18px;
                    padding: 10px;
                    border-radius: 50%;
                    cursor: pointer;
                    transition: all 0.3s;
                    text-decoration: none;
                " onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="fas fa-bell"></i>
                    <span class="icon-bell" style="display: none;"></span>
                </a>
                <span class="notification-badge" style="
                    position: absolute;
                    top: -2px;
                    right: -2px;
                    background: #ef4444;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    font-size: 11px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    animation: pulse 2s infinite;
                ">3</span>
            </div>
            
            <div class="user-profile" onclick="toggleUserDropdown()" id="userProfile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars(getCurrentUserRole()); ?></div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                
                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                        </div>
                        <div class="dropdown-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                        <div class="dropdown-role"><?php echo htmlspecialchars(getCurrentUserRole()); ?></div>
                    </div>
                    <div class="dropdown-menu">
                        <a href="../settings/index.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span class="icon-user" style="display: none;"></span>
                            My Profile
                        </a>
                        <a href="../settings/index.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span class="icon-cog" style="display: none;"></span>
                            Account Settings
                        </a>
                        <a href="../logs/user_activity.php" class="dropdown-item">
                            <i class="fas fa-history"></i>
                            <span class="icon-chart" style="display: none;"></span>
                            Activity Log
                        </a>
                        <a href="../docs/user_manual.md" class="dropdown-item">
                            <i class="fas fa-question-circle"></i>
                            <span class="icon-bell" style="display: none;"></span>
                            Help & Support
                        </a>
                        <div style="height: 1px; background: #e2e8f0; margin: 10px 0;"></div>
                        <a href="../../auth/logout.php" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="icon-logout" style="display: none;"></span>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-content">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-item">
                        <a href="../index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="icon-dashboard" style="display: none;"></span>
                            </span>
                            Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Core Management -->
                <div class="nav-section">
                    <div class="nav-title">Core Management</div>
                    <div class="nav-item">
                        <a href="../users/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-users"></i>
                                <span class="icon-users" style="display: none;"></span>
                            </span>
                            Users
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="index.php" class="nav-link active">
                            <span class="nav-icon">
                                <i class="fas fa-building"></i>
                                <span class="icon-building" style="display: none;"></span>
                            </span>
                            Businesses
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../properties/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-home"></i>
                                <span class="icon-home" style="display: none;"></span>
                            </span>
                            Properties
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../zones/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-map-marked-alt"></i>
                                <span class="icon-map" style="display: none;"></span>
                            </span>
                            Zones & Areas
                        </a>
                    </div>
                </div>
                
                <!-- Billing & Payments -->
                <div class="nav-section">
                    <div class="nav-title">Billing & Payments</div>
                    <div class="nav-item">
                        <a href="../billing/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-file-invoice"></i>
                                <span class="icon-invoice" style="display: none;"></span>
                            </span>
                            Billing
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../payments/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-credit-card"></i>
                                <span class="icon-credit" style="display: none;"></span>
                            </span>
                            Payments
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../fee_structure/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-tags"></i>
                                <span class="icon-tags" style="display: none;"></span>
                            </span>
                            Fee Structure
                        </a>
                    </div>
                </div>
                
                <!-- Reports & System -->
                <div class="nav-section">
                    <div class="nav-title">Reports & System</div>
                    <div class="nav-item">
                        <a href="../reports/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-chart-bar"></i>
                                <span class="icon-chart" style="display: none;"></span>
                            </span>
                            Reports
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../notifications/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-bell"></i>
                                <span class="icon-bell" style="display: none;"></span>
                            </span>
                            Notifications
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../settings/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-cog"></i>
                                <span class="icon-cog" style="display: none;"></span>
                            </span>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb-nav">
                <div class="breadcrumb">
                    <a href="../index.php">Dashboard</a>
                    <span>/</span>
                    <a href="index.php">Business Management</a>
                    <span>/</span>
                    <span class="breadcrumb-current">Register New Business</span>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-plus-circle"></i>
                            Register New Business
                        </h1>
                        <p style="color: #64748b; margin: 5px 0 0 0;">Add a new business to the municipal billing system</p>
                    </div>
                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Businesses
                    </a>
                </div>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="" id="businessForm">
                <!-- Basic Information Section -->
                <div class="form-card">
                    <div class="form-section">
                        <h3 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            Basic Information
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-building"></i>
                                    Business Name <span class="required">*</span>
                                </label>
                                <input type="text" name="business_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($formData['business_name']); ?>" 
                                       placeholder="Enter business name" required>
                                <div class="form-help">Official name of the business</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Owner Name <span class="required">*</span>
                                </label>
                                <input type="text" name="owner_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($formData['owner_name']); ?>" 
                                       placeholder="Enter owner full name" required>
                                <div class="form-help">Full name of the business owner</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i>
                                    Business Type <span class="required">*</span>
                                </label>
                                <select name="business_type" id="businessType" class="form-control" required>
                                    <option value="">Select Business Type</option>
                                    <?php 
                                    $types = array_unique(array_column($businessFees, 'business_type'));
                                    foreach ($types as $type): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($type); ?>" 
                                                <?php echo $formData['business_type'] === $type ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-help">Select the type of business</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-layer-group"></i>
                                    Category <span class="required">*</span>
                                </label>
                                <select name="category" id="businessCategory" class="form-control" required>
                                    <option value="">Select Category</option>
                                </select>
                                <div class="form-help">Category will auto-populate based on business type</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Telephone
                                </label>
                                <input type="tel" name="telephone" class="form-control" 
                                       value="<?php echo htmlspecialchars($formData['telephone']); ?>" 
                                       placeholder="e.g., +233 24 123 4567">
                                <div class="form-help">Contact phone number</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-flag"></i>
                                    Status
                                </label>
                                <select name="status" class="form-control">
                                    <option value="Active" <?php echo $formData['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="Inactive" <?php echo $formData['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="Suspended" <?php echo $formData['status'] === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                                </select>
                                <div class="form-help">Current business status</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information Section -->
                <div class="form-card">
                    <div class="form-section">
                        <h3 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            Location Information
                        </h3>
                        
                        <div class="form-row single">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-pin"></i>
                                    Exact Location
                                </label>
                                <div class="location-input-group">
                                    <input type="text" name="exact_location" id="exactLocation" class="form-control" 
                                           value="<?php echo htmlspecialchars($formData['exact_location']); ?>" 
                                           placeholder="Enter business location or use GPS">
                                    <button type="button" class="location-btn" onclick="getCurrentLocation()">
                                        <i class="fas fa-crosshairs"></i>
                                        <span class="icon-location" style="display: none;"></span>
                                        Get GPS Location
                                    </button>
                                </div>
                                <div class="location-status" id="locationStatus"></div>
                                <div class="form-help">Business physical address or use GPS for precise location</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map"></i>
                                    Zone <span class="required">*</span>
                                </label>
                                <select name="zone_id" id="zoneSelect" class="form-control" required>
                                    <option value="">Select Zone</option>
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>" 
                                                <?php echo $formData['zone_id'] == $zone['zone_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($zone['zone_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-help">Administrative zone where business is located</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marked"></i>
                                    Sub-Zone
                                </label>
                                <select name="sub_zone_id" id="subZoneSelect" class="form-control">
                                    <option value="">Select Sub-Zone</option>
                                </select>
                                <div class="form-help">Specific sub-zone within the selected zone</div>
                            </div>
                        </div>
                        
                        <!-- Hidden GPS coordinates -->
                        <input type="hidden" name="latitude" id="latitude" value="<?php echo htmlspecialchars($formData['latitude']); ?>">
                        <input type="hidden" name="longitude" id="longitude" value="<?php echo htmlspecialchars($formData['longitude']); ?>">
                    </div>
                </div>

                <!-- Billing Information Section -->
                <div class="form-card">
                    <div class="form-section">
                        <h3 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            Billing Information
                        </h3>
                        
                        <!-- Dynamic Fee Display -->
                        <div class="fee-display" id="feeDisplay">
                            <div class="fee-amount" id="feeAmount">₵ 0.00</div>
                            <div class="fee-description" id="feeDescription">Select business type and category to see fee</div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-history"></i>
                                    Old Bill (₵)
                                </label>
                                <input type="number" name="old_bill" class="form-control" step="0.01" min="0"
                                       value="<?php echo $formData['old_bill']; ?>" 
                                       placeholder="0.00">
                                <div class="form-help">Previous outstanding amount</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-credit-card"></i>
                                    Previous Payments (₵)
                                </label>
                                <input type="number" name="previous_payments" class="form-control" step="0.01" min="0"
                                       value="<?php echo $formData['previous_payments']; ?>" 
                                       placeholder="0.00">
                                <div class="form-help">Amount already paid from previous bills</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Arrears (₵)
                                </label>
                                <input type="number" name="arrears" class="form-control" step="0.01" min="0"
                                       value="<?php echo $formData['arrears']; ?>" 
                                       placeholder="0.00">
                                <div class="form-help">Outstanding arrears from previous periods</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-receipt"></i>
                                    Current Bill (₵)
                                </label>
                                <input type="number" name="current_bill" id="currentBill" class="form-control" step="0.01" min="0"
                                       value="<?php echo $formData['current_bill']; ?>" 
                                       placeholder="0.00" readonly>
                                <div class="form-help">Auto-calculated based on business type and category</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-folder"></i>
                                    Batch
                                </label>
                                <input type="text" name="batch" class="form-control" 
                                       value="<?php echo htmlspecialchars($formData['batch']); ?>" 
                                       placeholder="e.g., BATCH2025-01">
                                <div class="form-help">Group identifier for batch processing</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calculator"></i>
                                    Amount Payable (₵)
                                </label>
                                <input type="text" id="amountPayable" class="form-control" readonly 
                                       style="background: #f8fafc; font-weight: bold; color: #2d3748;">
                                <div class="form-help">Auto-calculated: Old Bill + Arrears + Current Bill - Previous Payments</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-card">
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            <span class="icon-save" style="display: none;"></span>
                            Register Business
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Business fee data for JavaScript
        const businessFees = <?php echo $businessTypesJson; ?>;
        const subZones = <?php echo json_encode($subZones); ?>;

        // Check if Font Awesome loaded, if not show emoji icons
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const testIcon = document.querySelector('.fas.fa-bars');
                if (!testIcon || getComputedStyle(testIcon, ':before').content === 'none') {
                    document.querySelectorAll('.fas, .far').forEach(function(icon) {
                        icon.style.display = 'none';
                    });
                    document.querySelectorAll('[class*="icon-"]').forEach(function(emoji) {
                        emoji.style.display = 'inline';
                    });
                }
            }, 100);
            
            // Initialize form
            initializeForm();
        });

        // Initialize form functionality
        function initializeForm() {
            // Set up business type change handler
            document.getElementById('businessType').addEventListener('change', updateCategories);
            
            // Set up category change handler
            document.getElementById('businessCategory').addEventListener('change', updateCurrentBill);
            
            // Set up zone change handler
            document.getElementById('zoneSelect').addEventListener('change', updateSubZones);
            
            // Set up billing calculation handlers
            ['old_bill', 'previous_payments', 'arrears', 'current_bill'].forEach(function(field) {
                const element = document.querySelector(`[name="${field}"]`);
                if (element) {
                    element.addEventListener('input', calculateAmountPayable);
                }
            });
            
            // Initialize calculations
            calculateAmountPayable();
            
            // Restore form state if needed
            if (document.getElementById('businessType').value) {
                updateCategories();
            }
            if (document.getElementById('zoneSelect').value) {
                updateSubZones();
            }
        }

        // Update categories based on selected business type
        function updateCategories() {
            const businessType = document.getElementById('businessType').value;
            const categorySelect = document.getElementById('businessCategory');
            
            // Clear existing options
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            
            if (businessType) {
                // Filter fees by business type
                const typeCategories = businessFees.filter(fee => fee.business_type === businessType);
                
                typeCategories.forEach(function(fee) {
                    const option = document.createElement('option');
                    option.value = fee.category;
                    option.textContent = fee.category;
                    option.dataset.feeAmount = fee.fee_amount;
                    categorySelect.appendChild(option);
                });
                
                // If there's a selected category, restore it
                const currentCategory = '<?php echo htmlspecialchars($formData['category']); ?>';
                if (currentCategory) {
                    categorySelect.value = currentCategory;
                    updateCurrentBill();
                }
            }
            
            // Update current bill
            updateCurrentBill();
        }

        // Update current bill based on selected category
        function updateCurrentBill() {
            const businessType = document.getElementById('businessType').value;
            const category = document.getElementById('businessCategory').value;
            const currentBillInput = document.getElementById('currentBill');
            const feeDisplay = document.getElementById('feeDisplay');
            const feeAmount = document.getElementById('feeAmount');
            const feeDescription = document.getElementById('feeDescription');
            
            if (businessType && category) {
                // Find the fee for this combination
                const fee = businessFees.find(f => f.business_type === businessType && f.category === category);
                
                if (fee) {
                    const amount = parseFloat(fee.fee_amount);
                    currentBillInput.value = amount.toFixed(2);
                    
                    // Show fee display
                    feeDisplay.style.display = 'block';
                    feeAmount.textContent = `₵ ${amount.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                    feeDescription.textContent = `Annual fee for ${businessType} - ${category}`;
                } else {
                    currentBillInput.value = '0.00';
                    feeDisplay.style.display = 'none';
                }
            } else {
                currentBillInput.value = '0.00';
                feeDisplay.style.display = 'none';
            }
            
            // Recalculate amount payable
            calculateAmountPayable();
        }

        // Update sub-zones based on selected zone
        function updateSubZones() {
            const zoneId = parseInt(document.getElementById('zoneSelect').value);
            const subZoneSelect = document.getElementById('subZoneSelect');
            
            // Clear existing options
            subZoneSelect.innerHTML = '<option value="">Select Sub-Zone</option>';
            
            if (zoneId) {
                // Filter sub-zones by zone
                const zoneSubZones = subZones.filter(sz => parseInt(sz.zone_id) === zoneId);
                
                zoneSubZones.forEach(function(subZone) {
                    const option = document.createElement('option');
                    option.value = subZone.sub_zone_id;
                    option.textContent = subZone.sub_zone_name;
                    subZoneSelect.appendChild(option);
                });
                
                // Restore selected sub-zone if needed
                const currentSubZone = '<?php echo $formData['sub_zone_id']; ?>';
                if (currentSubZone) {
                    subZoneSelect.value = currentSubZone;
                }
            }
        }

        // Calculate amount payable
        function calculateAmountPayable() {
            const oldBill = parseFloat(document.querySelector('[name="old_bill"]').value) || 0;
            const previousPayments = parseFloat(document.querySelector('[name="previous_payments"]').value) || 0;
            const arrears = parseFloat(document.querySelector('[name="arrears"]').value) || 0;
            const currentBill = parseFloat(document.getElementById('currentBill').value) || 0;
            
            const amountPayable = oldBill + arrears + currentBill - previousPayments;
            document.getElementById('amountPayable').value = `₵ ${amountPayable.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        }

        // Get current GPS location
        function getCurrentLocation() {
            const locationBtn = document.querySelector('.location-btn');
            const locationStatus = document.getElementById('locationStatus');
            const exactLocationInput = document.getElementById('exactLocation');
            
            // Show loading state
            locationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting Location...';
            locationBtn.disabled = true;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Store coordinates
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        
                        // Use reverse geocoding to get address (simplified)
                        exactLocationInput.value = `GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        
                        // Show success status
                        locationStatus.className = 'location-status success';
                        locationStatus.style.display = 'block';
                        locationStatus.innerHTML = '<i class="fas fa-check-circle"></i> GPS location captured successfully!';
                        
                        // Reset button
                        locationBtn.innerHTML = '<i class="fas fa-check"></i> Location Captured';
                        locationBtn.disabled = false;
                        
                        setTimeout(function() {
                            locationBtn.innerHTML = '<i class="fas fa-crosshairs"></i> Get GPS Location';
                        }, 3000);
                    },
                    function(error) {
                        // Show error status
                        locationStatus.className = 'location-status error';
                        locationStatus.style.display = 'block';
                        locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Unable to get GPS location. Please enter manually.';
                        
                        // Reset button
                        locationBtn.innerHTML = '<i class="fas fa-crosshairs"></i> Get GPS Location';
                        locationBtn.disabled = false;
                        
                        console.error('Geolocation error:', error.message);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                locationStatus.className = 'location-status error';
                locationStatus.style.display = 'block';
                locationStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Geolocation is not supported by this browser.';
                
                locationBtn.innerHTML = '<i class="fas fa-crosshairs"></i> Get GPS Location';
                locationBtn.disabled = false;
            }
        }

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
            
            const isHidden = sidebar.classList.contains('hidden');
            localStorage.setItem('sidebarHidden', isHidden);
        }

        // Restore sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarHidden = localStorage.getItem('sidebarHidden');
            if (sidebarHidden === 'true') {
                document.getElementById('sidebar').classList.add('hidden');
            }
        });

        // User dropdown toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.getElementById('userProfile');
            
            dropdown.classList.toggle('show');
            profile.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.getElementById('userProfile');
            
            if (!profile.contains(event.target)) {
                dropdown.classList.remove('show');
                profile.classList.remove('active');
            }
        });

        // Form validation before submit
        document.getElementById('businessForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
        });

        // Mobile responsiveness
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('hidden');
            }
        });

        // Session timeout check
        let lastActivity = <?php echo $_SESSION['LAST_ACTIVITY']; ?>;
        const SESSION_TIMEOUT = 1800; // 30 minutes in seconds

        function checkSessionTimeout() {
            const currentTime = Math.floor(Date.now() / 1000);
            if (currentTime - lastActivity > SESSION_TIMEOUT) {
                window.location.href = '../../index.php';
            }
        }

        // Check session every minute
        setInterval(checkSessionTimeout, 60000);
    </script>
</body>
</html>