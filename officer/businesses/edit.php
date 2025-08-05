<<<<<<< HEAD
 
=======
<?php
/**
 * Officer - Edit Business
 * businesses/edit.php
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

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../../auth/login.php');
    exit();
}

// Check if user is officer or admin
$currentUser = getCurrentUser();
if (!isOfficer() && !isAdmin()) {
    setFlashMessage('error', 'Access denied. Officer privileges required.');
    header('Location: ../../auth/login.php');
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

$userDisplayName = getUserDisplayName($currentUser);

// Get business ID
$business_id = intval($_GET['id'] ?? 0);

if ($business_id <= 0) {
    setFlashMessage('error', 'Invalid business ID.');
    header('Location: index.php');
    exit();
}

try {
    $db = new Database();
    
    // Get business details
    $business = $db->fetchRow("
        SELECT * FROM businesses WHERE business_id = ?
    ", [$business_id]);
    
    if (!$business) {
        setFlashMessage('error', 'Business not found.');
        header('Location: index.php');
        exit();
    }
    
    // Get zones and sub-zones for dropdowns
    $zones = $db->fetchAll("SELECT zone_id, zone_name FROM zones ORDER BY zone_name");
    $subZones = $db->fetchAll("SELECT sub_zone_id, sub_zone_name, zone_id FROM sub_zones ORDER BY sub_zone_name");
    
    // Get business types and categories
    $businessTypes = $db->fetchAll("SELECT DISTINCT business_type FROM business_fee_structure ORDER BY business_type");
    $businessCategories = $db->fetchAll("SELECT DISTINCT category FROM business_fee_structure ORDER BY category");
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
        } else {
            // Get form data
            $business_name = trim($_POST['business_name'] ?? '');
            $owner_name = trim($_POST['owner_name'] ?? '');
            $business_type = trim($_POST['business_type'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $exact_location = trim($_POST['exact_location'] ?? '');
            $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
            $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
            $zone_id = !empty($_POST['zone_id']) ? intval($_POST['zone_id']) : null;
            $sub_zone_id = !empty($_POST['sub_zone_id']) ? intval($_POST['sub_zone_id']) : null;
            $status = $_POST['status'] ?? 'Active';
            $batch = trim($_POST['batch'] ?? '');
            
            // Validation
            $errors = [];
            
            if (empty($business_name)) {
                $errors[] = 'Business name is required.';
            }
            
            if (empty($owner_name)) {
                $errors[] = 'Owner name is required.';
            }
            
            if (empty($business_type)) {
                $errors[] = 'Business type is required.';
            }
            
            if (empty($category)) {
                $errors[] = 'Category is required.';
            }
            
            if (!empty($telephone) && !preg_match('/^[\+]?[\d\s\-\(\)]+$/', $telephone)) {
                $errors[] = 'Invalid telephone number format.';
            }
            
            if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
                $errors[] = 'Latitude must be between -90 and 90.';
            }
            
            if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
                $errors[] = 'Longitude must be between -180 and 180.';
            }
            
            // Check if business name already exists (excluding current business)
            $existingBusiness = $db->fetchRow("
                SELECT business_id FROM businesses 
                WHERE business_name = ? AND business_id != ?
            ", [$business_name, $business_id]);
            
            if ($existingBusiness) {
                $errors[] = 'A business with this name already exists.';
            }
            
            if (empty($errors)) {
                try {
                    // Update business
                    $db->execute("
                        UPDATE businesses 
                        SET business_name = ?, owner_name = ?, business_type = ?, category = ?, 
                            telephone = ?, exact_location = ?, latitude = ?, longitude = ?, 
                            zone_id = ?, sub_zone_id = ?, status = ?, batch = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE business_id = ?
                    ", [
                        $business_name, $owner_name, $business_type, $category,
                        $telephone, $exact_location, $latitude, $longitude,
                        $zone_id, $sub_zone_id, $status, $batch, $business_id
                    ]);
                    
                    // Log the update
                    logUserAction('UPDATE_BUSINESS', 'businesses', $business_id, 
                        $business, 
                        [
                            'business_name' => $business_name,
                            'owner_name' => $owner_name,
                            'business_type' => $business_type,
                            'category' => $category,
                            'telephone' => $telephone,
                            'exact_location' => $exact_location,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'zone_id' => $zone_id,
                            'sub_zone_id' => $sub_zone_id,
                            'status' => $status,
                            'batch' => $batch
                        ]
                    );
                    
                    setFlashMessage('success', 'Business updated successfully.');
                    header('Location: view.php?id=' . $business_id);
                    exit();
                    
                } catch (Exception $e) {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
            
            if (!empty($errors)) {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
    }
    
} catch (Exception $e) {
    setFlashMessage('error', 'Database error: ' . $e->getMessage());
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Business - <?php echo htmlspecialchars($business['business_name']); ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Multiple Icon Sources -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Bootstrap for backup -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg1CWNtJ8BHeclYP7VfltZZLIcY3TVHaI&libraries=places"></script>
    
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
        .icon-dashboard::before { content: "⚡"; }
        .icon-building::before { content: "🏢"; }
        .icon-home::before { content: "🏠"; }
        .icon-money::before { content: "💰"; }
        .icon-receipt::before { content: "🧾"; }
        .icon-map::before { content: "🗺️"; }
        .icon-menu::before { content: "☰"; }
        .icon-logout::before { content: "🚪"; }
        .icon-plus::before { content: "➕"; }
        .icon-search::before { content: "🔍"; }
        .icon-user::before { content: "👤"; }
        .icon-edit::before { content: "✏️"; }
        .icon-arrow-left::before { content: "⬅️"; }
        .icon-save::before { content: "💾"; }
        .icon-question::before { content: "❓"; }
        .icon-map-marker::before { content: "📍"; }
        .icon-cash::before { content: "💵"; }
        .icon-file-invoice::before { content: "📄"; }
        .icon-location::before { content: "📍"; }
        
        /* Top Navigation */
        .top-nav {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
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
        
        /* User Dropdown */
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
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
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
            color: #4299e1;
            transform: translateX(5px);
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
        
        /* Sidebar */
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
            border-left-color: #4299e1;
        }
        
        .nav-link.active {
            background: rgba(66, 153, 225, 0.3);
            color: white;
            border-left-color: #4299e1;
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
        
        /* Page Header */
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #718096;
            font-size: 16px;
        }
        
        /* Form Container */
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-label.required::after {
            content: ' *';
            color: #e53e3e;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
            outline: none;
        }
        
        .form-control.error {
            border-color: #e53e3e;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #4299e1;
            color: white;
        }
        
        .btn-primary:hover {
            background: #3182ce;
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-secondary {
            background: #718096;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4a5568;
            color: white;
        }
        
        .btn-success {
            background: #38a169;
            color: white;
        }
        
        .btn-success:hover {
            background: #2f855a;
            color: white;
        }
        
        /* Map Container */
        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 15px;
            border: 2px solid #e2e8f0;
        }
        
        .map-controls {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .coordinate-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        /* Form Actions */
        .form-actions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            border-top: 2px solid #e2e8f0;
            margin-top: 30px;
        }
        
        /* Loading Spinner */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4299e1;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100%;
                z-index: 999;
                transform: translateX(-100%);
                width: 280px;
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
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .container {
                flex-direction: column;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-out;
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
                <i class="fas fa-user-tie"></i>
                <span class="icon-user" style="display: none;"></span>
                Officer Portal
            </a>
        </div>
        
        <div class="user-section">
            <div class="user-profile" onclick="toggleUserDropdown()" id="userProfile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                    <div class="user-role">Officer</div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                
                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <?php echo strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="dropdown-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                        <div class="dropdown-role">Officer</div>
                    </div>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item" onclick="alert('Profile management coming soon!')">
                            <i class="fas fa-user"></i>
                            <span class="icon-user" style="display: none;"></span>
                            My Profile
                        </a>
                        <a href="#" class="dropdown-item" onclick="alert('Help documentation coming soon!')">
                            <i class="fas fa-question-circle"></i>
                            <span class="icon-question" style="display: none;"></span>
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
        <div class="sidebar hidden" id="sidebar">
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
                
                <!-- Registration -->
                <div class="nav-section">
                    <div class="nav-title">Registration</div>
                    <div class="nav-item">
                        <a href="add.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-plus-circle"></i>
                                <span class="icon-plus" style="display: none;"></span>
                            </span>
                            Register Business
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../properties/add.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-plus-circle"></i>
                                <span class="icon-plus" style="display: none;"></span>
                            </span>
                            Register Property
                        </a>
                    </div>
                </div>
                
                <!-- Management -->
                <div class="nav-section">
                    <div class="nav-title">Management</div>
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
                </div>
                
                <!-- Payments & Bills -->
                <div class="nav-section">
                    <div class="nav-title">Payments & Bills</div>
                    <div class="nav-item">
                        <a href="../payments/record.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-cash-register"></i>
                                <span class="icon-cash" style="display: none;"></span>
                            </span>
                            Record Payment
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../payments/search.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-search"></i>
                                <span class="icon-search" style="display: none;"></span>
                            </span>
                            Search Accounts
                        </a>
                    </div>
                
                </div>
                
                <!-- Maps & Locations -->
                <div class="nav-section">
                    <div class="nav-title">Maps & Locations</div>
                    <div class="nav-item">
                        <a href="../map/businesses.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-map-marked-alt"></i>
                                <span class="icon-map" style="display: none;"></span>
                            </span>
                            Business Map
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../map/properties.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="icon-map" style="display: none;"></span>
                            </span>
                            Property Map
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <h1 class="page-title">
                    <i class="fas fa-edit text-warning"></i>
                    Edit Business
                </h1>
                <p class="page-subtitle">Update business information and location details</p>
            </div>

            <!-- Flash Messages -->
            <?php if (getFlashMessages()): ?>
                <?php $flash = getFlashMessages(); ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <form method="POST" class="form-container fade-in" id="businessForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Business Name</label>
                            <input type="text" class="form-control" name="business_name" 
                                   value="<?php echo htmlspecialchars($business['business_name']); ?>" 
                                   required maxlength="200">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Owner Name</label>
                            <input type="text" class="form-control" name="owner_name" 
                                   value="<?php echo htmlspecialchars($business['owner_name']); ?>" 
                                   required maxlength="100">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required">Business Type</label>
                            <select class="form-control" name="business_type" required id="businessType">
                                <option value="">Select Business Type</option>
                                <?php foreach ($businessTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['business_type']); ?>"
                                            <?php echo $business['business_type'] === $type['business_type'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['business_type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Category</label>
                            <select class="form-control" name="category" required id="category">
                                <option value="">Select Category</option>
                                <?php foreach ($businessCategories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                            <?php echo $business['category'] === $cat['category'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Telephone</label>
                            <input type="tel" class="form-control" name="telephone" 
                                   value="<?php echo htmlspecialchars($business['telephone']); ?>" 
                                   placeholder="e.g., +233 24 123 4567">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="Active" <?php echo $business['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo $business['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="Suspended" <?php echo $business['status'] === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Batch</label>
                        <input type="text" class="form-control" name="batch" 
                               value="<?php echo htmlspecialchars($business['batch']); ?>" 
                               placeholder="Optional batch identifier">
                    </div>
                </div>
                
                <!-- Location Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Location Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Zone</label>
                            <select class="form-control" name="zone_id" id="zoneSelect">
                                <option value="">Select Zone</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?php echo $zone['zone_id']; ?>"
                                            <?php echo $business['zone_id'] == $zone['zone_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($zone['zone_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Sub-Zone</label>
                            <select class="form-control" name="sub_zone_id" id="subZoneSelect">
                                <option value="">Select Sub-Zone</option>
                                <?php foreach ($subZones as $subZone): ?>
                                    <option value="<?php echo $subZone['sub_zone_id']; ?>" 
                                            data-zone="<?php echo $subZone['zone_id']; ?>"
                                            <?php echo $business['sub_zone_id'] == $subZone['sub_zone_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subZone['sub_zone_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Exact Location Description</label>
                        <textarea class="form-control" name="exact_location" rows="3" 
                                  placeholder="Detailed description of the business location"><?php echo htmlspecialchars($business['exact_location']); ?></textarea>
                    </div>
                    
                    <div class="coordinate-inputs">
                        <div class="form-group">
                            <label class="form-label">Latitude</label>
                            <input type="number" class="form-control" name="latitude" id="latitude" 
                                   value="<?php echo $business['latitude']; ?>" 
                                   step="0.00000001" min="-90" max="90" 
                                   placeholder="e.g., 5.593020">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Longitude</label>
                            <input type="number" class="form-control" name="longitude" id="longitude" 
                                   value="<?php echo $business['longitude']; ?>" 
                                   step="0.00000001" min="-180" max="180" 
                                   placeholder="e.g., -0.077100">
                        </div>
                    </div>
                    
                    <div class="map-controls">
                        <button type="button" class="btn btn-secondary" onclick="getCurrentLocation()">
                            <i class="fas fa-location-arrow"></i>
                            <span class="icon-location" style="display: none;"></span>
                            Get Current Location
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="searchLocation()">
                            <i class="fas fa-search"></i>
                            <span class="icon-search" style="display: none;"></span>
                            Search Location
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearLocation()">
                            <i class="fas fa-times"></i>
                            Clear Location
                        </button>
                    </div>
                    
                    <div class="map-container" id="map"></div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="view.php?id=<?php echo $business_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span class="icon-arrow-left" style="display: none;"></span>
                        Back to List
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <span class="icon-save" style="display: none;"></span>
                        Update Business
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let map;
        let marker;
        let geocoder;
        let autocomplete;
        
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
            
            // Initialize map
            initMap();
            
            // Setup zone/sub-zone dependency
            setupZoneSubZone();
        });

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.toggle('show');
                sidebar.classList.toggle('hidden');
            } else {
                sidebar.classList.toggle('hidden');
            }
            
            const isHidden = sidebar.classList.contains('hidden');
            localStorage.setItem('sidebarHidden', isHidden);
        }

        // Restore sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarHidden = localStorage.getItem('sidebarHidden');
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('show');
            } else if (sidebarHidden === 'true') {
                sidebar.classList.add('hidden');
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

        // Close sidebar when clicking outside in mobile view
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('show');
                sidebar.classList.add('hidden');
                localStorage.setItem('sidebarHidden', true);
            }
        });

        // Initialize map
        function initMap() {
            const lat = <?php echo $business['latitude'] ?: '5.593020'; ?>;
            const lng = <?php echo $business['longitude'] ?: '-0.077100'; ?>;
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: { lat: lat, lng: lng },
                mapTypeId: 'satellite'
            });
            
            geocoder = new google.maps.Geocoder();
            
            // Add existing marker if coordinates exist
            if (lat && lng) {
                addMarker(lat, lng);
            }
            
            // Map click event
            map.addListener('click', function(event) {
                const clickedLat = event.latLng.lat();
                const clickedLng = event.latLng.lng();
                
                addMarker(clickedLat, clickedLng);
                updateCoordinates(clickedLat, clickedLng);
            });
        }
        
        function addMarker(lat, lng) {
            if (marker) {
                marker.setMap(null);
            }
            
            marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map,
                draggable: true,
                title: 'Business Location',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                }
            });
            
            marker.addListener('dragend', function() {
                const position = marker.getPosition();
                updateCoordinates(position.lat(), position.lng());
            });
            
            map.setCenter({ lat: lat, lng: lng });
        }
        
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }
        
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    addMarker(lat, lng);
                    updateCoordinates(lat, lng);
                }, function(error) {
                    alert('Error getting location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        
        function searchLocation() {
            const address = prompt('Enter address or place name to search:');
            if (address) {
                geocoder.geocode({ address: address }, function(results, status) {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;
                        const lat = location.lat();
                        const lng = location.lng();
                        
                        addMarker(lat, lng);
                        updateCoordinates(lat, lng);
                    } else {
                        alert('Location not found: ' + status);
                    }
                });
            }
        }
        
        function clearLocation() {
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }
        
        // Setup zone/sub-zone dependency
        function setupZoneSubZone() {
            const zoneSelect = document.getElementById('zoneSelect');
            const subZoneSelect = document.getElementById('subZoneSelect');
            
            zoneSelect.addEventListener('change', function() {
                const selectedZone = this.value;
                const subZoneOptions = subZoneSelect.querySelectorAll('option');
                
                // Reset sub-zone
                subZoneSelect.value = '';
                
                // Show/hide sub-zone options based on selected zone
                subZoneOptions.forEach(function(option) {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        const optionZone = option.getAttribute('data-zone');
                        option.style.display = (selectedZone === '' || optionZone === selectedZone) ? 'block' : 'none';
                    }
                });
            });
            
            // Trigger zone change to filter sub-zones on page load
            zoneSelect.dispatchEvent(new Event('change'));
        }
        
        // Form validation
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
            }
        });
        
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success')) {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            });
        }, 5000);
    </script>
</body>
</html>
>>>>>>> c9ccaba (Initial commit)
