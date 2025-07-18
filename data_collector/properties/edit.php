<?php
/**
 * Data Collector - Edit Property Form
 * properties/edit.php
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

// Check if user is data collector
$currentUser = getCurrentUser();
if (!isDataCollector() && !isAdmin()) {
    setFlashMessage('error', 'Access denied. Data Collector privileges required.');
    header('Location: ../../auth/login.php');
    exit();
}

$userDisplayName = getUserDisplayName($currentUser);
$errors = [];
$success = false;

// Get property ID
$property_id = intval($_GET['id'] ?? 0);

if ($property_id <= 0) {
    setFlashMessage('error', 'Invalid property ID.');
    header('Location: index.php');
    exit();
}

try {
    $db = new Database();
    
    // Get current property data
    $property = $db->fetchRow("
        SELECT * FROM properties WHERE property_id = ?
    ", [$property_id]);
    
    if (!$property) {
        setFlashMessage('error', 'Property not found.');
        header('Location: index.php');
        exit();
    }
    
} catch (Exception $e) {
    setFlashMessage('error', 'Database error: ' . $e->getMessage());
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate required fields
        $owner_name = trim($_POST['owner_name'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $latitude = floatval($_POST['latitude'] ?? 0);
        $longitude = floatval($_POST['longitude'] ?? 0);
        $structure = trim($_POST['structure'] ?? '');
        $ownership_type = trim($_POST['ownership_type'] ?? 'Self');
        $property_type = trim($_POST['property_type'] ?? 'Modern');
        $number_of_rooms = intval($_POST['number_of_rooms'] ?? 0);
        $property_use = trim($_POST['property_use'] ?? '');
        $zone_id = intval($_POST['zone_id'] ?? 0);
        $old_bill = floatval($_POST['old_bill'] ?? 0);
        $previous_payments = floatval($_POST['previous_payments'] ?? 0);
        $arrears = floatval($_POST['arrears'] ?? 0);
        $batch = trim($_POST['batch'] ?? '');
        
        // Validation
        if (empty($owner_name)) {
            $errors[] = "Owner name is required.";
        }
        
        if (empty($location)) {
            $errors[] = "Location is required.";
        }
        
        if ($latitude == 0 || $longitude == 0) {
            $errors[] = "Please capture GPS coordinates using the location button.";
        }
        
        if (empty($structure)) {
            $errors[] = "Structure is required.";
        }
        
        if ($number_of_rooms <= 0) {
            $errors[] = "Number of rooms must be greater than 0.";
        }
        
        if (empty($property_use)) {
            $errors[] = "Property use is required.";
        }
        
        if ($zone_id == 0) {
            $errors[] = "Zone is required.";
        }
        
        // Check if structure and property use combination exists in fee structure
        $fee_check = $db->fetchRow(
            "SELECT fee_per_room FROM property_fee_structure 
             WHERE structure = ? AND property_use = ? AND is_active = 1",
            [$structure, $property_use]
        );
        
        if (!$fee_check) {
            $errors[] = "Invalid structure and property use combination.";
        }
        
        // Store old values for audit log
        $old_values = [
            'owner_name' => $property['owner_name'],
            'location' => $property['location'],
            'structure' => $property['structure'],
            'property_use' => $property['property_use'],
            'number_of_rooms' => $property['number_of_rooms']
        ];
        
        // If no errors, update property
        if (empty($errors)) {
            // Calculate current bill
            $current_bill = $fee_check['fee_per_room'] * $number_of_rooms;
            
            // Calculate amount payable
            $amount_payable = $old_bill + $arrears + $current_bill - $previous_payments;
            
            // Update property using execute method
            $update_result = $db->execute(
                "UPDATE properties SET 
                    owner_name = ?, telephone = ?, gender = ?, location = ?, 
                    latitude = ?, longitude = ?, structure = ?, ownership_type = ?, 
                    property_type = ?, number_of_rooms = ?, property_use = ?, 
                    old_bill = ?, previous_payments = ?, arrears = ?, current_bill = ?, 
                    amount_payable = ?, batch = ?, zone_id = ?, updated_at = NOW()
                WHERE property_id = ?",
                [
                    $owner_name, $telephone, $gender, $location, $latitude, $longitude,
                    $structure, $ownership_type, $property_type, $number_of_rooms, $property_use,
                    $old_bill, $previous_payments, $arrears, $current_bill, $amount_payable,
                    $batch, $zone_id, $property_id
                ]
            );
            
            if ($update_result) {
                // Log the action using execute method
                $new_values = [
                    'owner_name' => $owner_name,
                    'location' => $location,
                    'structure' => $structure,
                    'property_use' => $property_use,
                    'number_of_rooms' => $number_of_rooms
                ];
                
                $audit_result = $db->execute(
                    "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent, created_at)
                     VALUES (?, 'UPDATE_PROPERTY', 'properties', ?, ?, ?, ?, ?, NOW())",
                    [
                        $currentUser['id'], $property_id, 
                        json_encode($old_values), json_encode($new_values),
                        $_SERVER['REMOTE_ADDR'] ?? 'Unknown', 
                        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                    ]
                );
                
                setFlashMessage('success', 'Property updated successfully!');
                header('Location: view.php?id=' . $property_id);
                exit();
            } else {
                $errors[] = "Failed to update property. Please try again.";
            }
        }
        
    } catch (Exception $e) {
        $errors[] = "Database error: " . $e->getMessage();
        error_log("Property update error: " . $e->getMessage());
    }
} else {
    // Pre-populate form with existing data
    $_POST = [
        'owner_name' => $property['owner_name'],
        'telephone' => $property['telephone'],
        'gender' => $property['gender'],
        'location' => $property['location'],
        'latitude' => $property['latitude'],
        'longitude' => $property['longitude'],
        'structure' => $property['structure'],
        'ownership_type' => $property['ownership_type'],
        'property_type' => $property['property_type'],
        'number_of_rooms' => $property['number_of_rooms'],
        'property_use' => $property['property_use'],
        'zone_id' => $property['zone_id'],
        'old_bill' => $property['old_bill'],
        'previous_payments' => $property['previous_payments'],
        'arrears' => $property['arrears'],
        'batch' => $property['batch']
    ];
}

// Get reference data
try {
    // Get fee structure for properties
    $fee_structure = $db->fetchAll(
        "SELECT structure, property_use, fee_per_room 
         FROM property_fee_structure 
         WHERE is_active = 1 
         ORDER BY structure, property_use"
    );
    
    // Get zones
    $zones = $db->fetchAll("SELECT zone_id, zone_name FROM zones ORDER BY zone_name");
    
} catch (Exception $e) {
    $fee_structure = [];
    $zones = [];
    error_log("Error fetching reference data: " . $e->getMessage());
}

// Organize fee structure
$structures_data = [];
if ($fee_structure) {
    foreach ($fee_structure as $fee) {
        if (!isset($structures_data[$fee['structure']])) {
            $structures_data[$fee['structure']] = [];
        }
        $structures_data[$fee['structure']][] = [
            'property_use' => $fee['property_use'],
            'fee_per_room' => $fee['fee_per_room']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - <?php echo htmlspecialchars($property['property_number']); ?> - <?php echo APP_NAME; ?></title>
    
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
        .icon-dashboard::before { content: "📊"; }
        .icon-building::before { content: "🏢"; }
        .icon-home::before { content: "🏠"; }
        .icon-map::before { content: "🗺️"; }
        .icon-menu::before { content: "☰"; }
        .icon-logout::before { content: "🚪"; }
        .icon-plus::before { content: "➕"; }
        .icon-users::before { content: "👥"; }
        .icon-cog::before { content: "⚙️"; }
        .icon-history::before { content: "📜"; }
        .icon-question::before { content: "❓"; }
        .icon-user::before { content: "👤"; }
        .icon-location::before { content: "📍"; }
        .icon-edit::before { content: "✏️"; }
        .icon-eye::before { content: "👁️"; }
        .icon-save::before { content: "💾"; }
        .icon-times::before { content: "❌"; }
        .icon-exclamation-triangle::before { content: "⚠️"; }
        .icon-info-circle::before { content: "ℹ️"; }
        .icon-map-marker-alt::before { content: "📍"; }
        .icon-calculator::before { content: "🧮"; }
        
        /* Top Navigation */
        .top-nav {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
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
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
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
            color: #38a169;
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
            border-left-color: #38a169;
        }
        
        .nav-link.active {
            background: rgba(56, 161, 105, 0.3);
            color: white;
            border-left-color: #38a169;
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
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
        }
        
        .form-subtitle {
            color: #718096;
            font-size: 16px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }
        
        .form-label.required::after {
            content: " *";
            color: #e53e3e;
        }
        
        .form-control {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #38a169;
            box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.1);
        }
        
        .form-control.error {
            border-color: #e53e3e;
        }
        
        .form-help {
            font-size: 12px;
            color: #718096;
            margin-top: 5px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #38a169;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2f855a;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #718096;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #4a5568;
        }
        
        .btn-location {
            background: #4299e1;
            color: white;
        }
        
        .btn-location:hover {
            background: #3182ce;
        }
        
        .btn-warning {
            background: #ed8936;
            color: white;
        }
        
        .btn-warning:hover {
            background: #dd6b20;
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }
        
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        
        .alert-info {
            background: #bee3f8;
            color: #2a4365;
            border: 1px solid #90cdf4;
        }
        
        .alert-warning {
            background: #faf0e6;
            color: #c05621;
            border: 1px solid #f6ad55;
        }
        
        /* Location Section */
        .location-section {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .coordinates-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .coordinate-item {
            background: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }
        
        .coordinate-label {
            font-size: 12px;
            color: #718096;
            margin-bottom: 5px;
        }
        
        .coordinate-value {
            font-weight: bold;
            color: #2d3748;
        }
        
        /* Bill Calculator */
        .bill-calculator {
            background: #e6fffa;
            border: 2px solid #38b2ac;
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }
        
        .calculator-title {
            font-weight: bold;
            color: #234e52;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .calculation-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(56, 178, 172, 0.2);
        }
        
        .calculation-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            background: rgba(56, 178, 172, 0.1);
            margin: 10px -10px -10px -10px;
            padding: 15px 10px;
            border-radius: 0 0 6px 6px;
        }
        
        /* Actions */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
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
                <i class="fas fa-clipboard-list"></i>
                <span class="icon-dashboard" style="display: none;"></span>
                Data Collector
            </a>
        </div>
        
        <div class="user-section">
            <div class="user-profile" onclick="toggleUserDropdown()" id="userProfile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                    <div class="user-role">Data Collector</div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                
                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <?php echo strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="dropdown-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                        <div class="dropdown-role">Data Collector</div>
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
                
                <!-- Data Collection -->
                <div class="nav-section">
                    <div class="nav-title">Data Collection</div>
                    <div class="nav-item">
                        <a href="../businesses/index.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-building"></i>
                                <span class="icon-building" style="display: none;"></span>
                            </span>
                            Businesses
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="index.php" class="nav-link active">
                            <span class="nav-icon">
                                <i class="fas fa-home"></i>
                                <span class="icon-home" style="display: none;"></span>
                            </span>
                            Properties
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
                            Business Locations
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="../map/properties.php" class="nav-link">
                            <span class="nav-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="icon-location" style="display: none;"></span>
                            </span>
                            Property Locations
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-container fade-in">
                <div class="form-header">
                    <h1 class="form-title">Edit Property</h1>
                    <p class="form-subtitle">Update property information - <?php echo htmlspecialchars($property['property_number']); ?></p>
                </div>
                
                <!-- Alert for editing existing property -->
                <div class="alert alert-warning">
                    <h4>
                        <i class="fas fa-edit"></i>
                        <span class="icon-edit" style="display: none;"></span>
                        Editing Existing Property
                    </h4>
                    <p><strong>Property Number:</strong> <?php echo htmlspecialchars($property['property_number']); ?></p>
                    <p><strong>Owner:</strong> <?php echo htmlspecialchars($property['owner_name']); ?></p>
                    <p>You are editing an existing property record. Changes will be logged for audit purposes.</p>
                </div>
                
                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h4>
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="icon-exclamation-triangle" style="display: none;"></span>
                            Please fix the following errors:
                        </h4>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Edit Form -->
                <form method="POST" action="" id="propertyForm">
                    <!-- Owner Information -->
                    <div class="form-section">
                        <h3 class="section-title">Owner Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Owner Name</label>
                                <input type="text" name="owner_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['owner_name'] ?? ''); ?>" 
                                       required>
                                <div class="form-help">Enter the full name of the property owner</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Telephone</label>
                                <input type="tel" name="telephone" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['telephone'] ?? ''); ?>" 
                                       placeholder="0XX XXX XXXX">
                                <div class="form-help">Contact number for the property owner</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($_POST['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($_POST['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo ($_POST['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <div class="form-help">Optional gender information</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Property Number</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo htmlspecialchars($property['property_number']); ?>" 
                                       readonly style="background: #f7fafc;">
                                <div class="form-help">Property number cannot be changed</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Batch</label>
                                <input type="text" name="batch" class="form-control" 
                                       value="<?php echo htmlspecialchars($_POST['batch'] ?? ''); ?>" 
                                       placeholder="e.g., BATCH001">
                                <div class="form-help">Optional batch identifier for bulk operations</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Property Details -->
                    <div class="form-section">
                        <h3 class="section-title">Property Details</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Structure</label>
                                <select name="structure" class="form-control" id="structureSelect" required>
                                    <option value="">Select Structure</option>
                                    <?php foreach ($structures_data as $structure => $uses): ?>
                                        <option value="<?php echo htmlspecialchars($structure); ?>"
                                                <?php echo (($_POST['structure'] ?? '') == $structure) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($structure); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-help">Select the building structure type</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Property Use</label>
                                <select name="property_use" class="form-control" id="propertyUseSelect" required>
                                    <option value="">Select Property Use</option>
                                </select>
                                <div class="form-help">Property use will be populated based on structure</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ownership Type</label>
                                <select name="ownership_type" class="form-control">
                                    <option value="Self" <?php echo ($_POST['ownership_type'] ?? 'Self') == 'Self' ? 'selected' : ''; ?>>Self</option>
                                    <option value="Family" <?php echo ($_POST['ownership_type'] ?? '') == 'Family' ? 'selected' : ''; ?>>Family</option>
                                    <option value="Corporate" <?php echo ($_POST['ownership_type'] ?? '') == 'Corporate' ? 'selected' : ''; ?>>Corporate</option>
                                    <option value="Others" <?php echo ($_POST['ownership_type'] ?? '') == 'Others' ? 'selected' : ''; ?>>Others</option>
                                </select>
                                <div class="form-help">Type of ownership</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Property Type</label>
                                <select name="property_type" class="form-control">
                                    <option value="Modern" <?php echo ($_POST['property_type'] ?? 'Modern') == 'Modern' ? 'selected' : ''; ?>>Modern</option>
                                    <option value="Traditional" <?php echo ($_POST['property_type'] ?? '') == 'Traditional' ? 'selected' : ''; ?>>Traditional</option>
                                </select>
                                <div class="form-help">Modern or traditional construction</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Number of Rooms</label>
                                <input type="number" name="number_of_rooms" class="form-control" 
                                       id="numberOfRooms"
                                       value="<?php echo $_POST['number_of_rooms'] ?? '1'; ?>" 
                                       min="1" max="50" required>
                                <div class="form-help">Total number of rooms in the property</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Zone</label>
                                <select name="zone_id" class="form-control" required>
                                    <option value="">Select Zone</option>
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>"
                                                <?php echo (($_POST['zone_id'] ?? '') == $zone['zone_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($zone['zone_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-help">Select the zone where property is located</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location Information -->
                    <div class="form-section">
                        <h3 class="section-title">Location Information</h3>
                        
                        <div class="location-section">
                            <div class="form-group">
                                <label class="form-label required">Location</label>
                                <textarea name="location" class="form-control" rows="3" 
                                          placeholder="Enter detailed location description..." required><?php echo htmlspecialchars($_POST['location'] ?? ''); ?></textarea>
                                <div class="form-help">Provide detailed directions or landmarks</div>
                            </div>
                            
                            <div style="margin-top: 15px; text-align: center;">
                                <button type="button" class="btn btn-location" onclick="getCurrentLocation()">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span class="icon-map-marker-alt" style="display: none;"></span>
                                    Update GPS Coordinates
                                </button>
                            </div>
                            
                            <div class="coordinates-display">
                                <div class="coordinate-item">
                                    <div class="coordinate-label">Latitude</div>
                                    <div class="coordinate-value" id="latitudeDisplay">
                                        <?php echo $_POST['latitude'] ? number_format($_POST['latitude'], 6) : 'Not captured'; ?>
                                    </div>
                                </div>
                                <div class="coordinate-item">
                                    <div class="coordinate-label">Longitude</div>
                                    <div class="coordinate-value" id="longitudeDisplay">
                                        <?php echo $_POST['longitude'] ? number_format($_POST['longitude'], 6) : 'Not captured'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="latitude" id="latitude" value="<?php echo $_POST['latitude'] ?? ''; ?>">
                            <input type="hidden" name="longitude" id="longitude" value="<?php echo $_POST['longitude'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <!-- Billing Information -->
                    <div class="form-section">
                        <h3 class="section-title">Billing Information</h3>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <span class="icon-info-circle" style="display: none;"></span>
                            <strong>Current Bill</strong> will be automatically updated based on structure, property use, and number of rooms.
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Old Bill (₵)</label>
                                <input type="number" name="old_bill" class="form-control" 
                                       value="<?php echo $_POST['old_bill'] ?? '0'; ?>" 
                                       step="0.01" min="0">
                                <div class="form-help">Previous outstanding bill amount</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Previous Payments (₵)</label>
                                <input type="number" name="previous_payments" class="form-control" 
                                       value="<?php echo $_POST['previous_payments'] ?? '0'; ?>" 
                                       step="0.01" min="0">
                                <div class="form-help">Amount previously paid</div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Arrears (₵)</label>
                                <input type="number" name="arrears" class="form-control" 
                                       value="<?php echo $_POST['arrears'] ?? '0'; ?>" 
                                       step="0.01" min="0">
                                <div class="form-help">Outstanding arrears amount</div>
                            </div>
                        </div>
                        
                        <!-- Bill Calculator -->
                        <div class="bill-calculator" id="billCalculator" style="display: none;">
                            <div class="calculator-title">
                                <i class="fas fa-calculator"></i>
                                <span class="icon-calculator" style="display: none;"></span>
                                Updated Bill Calculation
                            </div>
                            <div class="calculation-row">
                                <span>Fee per room:</span>
                                <span id="feePerRoom">₵ 0.00</span>
                            </div>
                            <div class="calculation-row">
                                <span>Number of rooms:</span>
                                <span id="roomCount">0</span>
                            </div>
                            <div class="calculation-row">
                                <span>Current Bill:</span>
                                <span id="calculatedBill">₵ 0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="view.php?id=<?php echo $property_id; ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            <span class="icon-times" style="display: none;"></span>
                            Cancel
                        </a>
                        <a href="view.php?id=<?php echo $property_id; ?>" class="btn btn-warning">
                            <i class="fas fa-eye"></i>
                            <span class="icon-eye" style="display: none;"></span>
                            View Property
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <span class="icon-save" style="display: none;"></span>
                            Update Property
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
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

        // Structures and property uses data
        const structuresData = <?php echo json_encode($structures_data); ?>;
        
        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize structure change handler
            const structureSelect = document.getElementById('structureSelect');
            const propertyUseSelect = document.getElementById('propertyUseSelect');
            const numberOfRoomsInput = document.getElementById('numberOfRooms');
            const billCalculator = document.getElementById('billCalculator');
            
            structureSelect.addEventListener('change', function() {
                const selectedStructure = this.value;
                
                // Clear property uses
                propertyUseSelect.innerHTML = '<option value="">Select Property Use</option>';
                
                if (selectedStructure && structuresData[selectedStructure]) {
                    structuresData[selectedStructure].forEach(function(use) {
                        const option = document.createElement('option');
                        option.value = use.property_use;
                        option.textContent = use.property_use;
                        option.dataset.feePerRoom = use.fee_per_room;
                        propertyUseSelect.appendChild(option);
                    });
                }
                
                // Hide calculator
                billCalculator.style.display = 'none';
            });
            
            // Initialize property use and room count change handlers
            function updateBillCalculation() {
                const selectedUseOption = propertyUseSelect.options[propertyUseSelect.selectedIndex];
                const feePerRoom = parseFloat(selectedUseOption.dataset.feePerRoom || 0);
                const rooms = parseInt(numberOfRoomsInput.value || 0);
                
                if (feePerRoom > 0 && rooms > 0) {
                    const currentBill = feePerRoom * rooms;
                    
                    document.getElementById('feePerRoom').textContent = '₵ ' + feePerRoom.toFixed(2);
                    document.getElementById('roomCount').textContent = rooms.toString();
                    document.getElementById('calculatedBill').textContent = '₵ ' + currentBill.toFixed(2);
                    
                    billCalculator.style.display = 'block';
                } else {
                    billCalculator.style.display = 'none';
                }
            }
            
            propertyUseSelect.addEventListener('change', updateBillCalculation);
            numberOfRoomsInput.addEventListener('input', updateBillCalculation);
            
            // Restore selections on page load
            if (structureSelect.value) {
                structureSelect.dispatchEvent(new Event('change'));
                setTimeout(() => {
                    const savedPropertyUse = '<?php echo $_POST['property_use'] ?? ''; ?>';
                    if (savedPropertyUse) {
                        propertyUseSelect.value = savedPropertyUse;
                        updateBillCalculation();
                    }
                }, 100);
            }
        });
        
        // Get current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        // Update hidden inputs
                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;
                        
                        // Update display
                        document.getElementById('latitudeDisplay').textContent = latitude.toFixed(6);
                        document.getElementById('longitudeDisplay').textContent = longitude.toFixed(6);
                        
                        // Try to get address
                        if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                            getAddressFromCoordinates(latitude, longitude);
                        }
                    },
                    function(error) {
                        let errorMessage = 'Error getting location: ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += "User denied the request for Geolocation.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += "Location information is unavailable.";
                                break;
                            case error.TIMEOUT:
                                errorMessage += "The request to get user location timed out.";
                                break;
                            default:
                                errorMessage += "An unknown error occurred.";
                                break;
                        }
                        alert(errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        
        // Get address from coordinates
        function getAddressFromCoordinates(latitude, longitude) {
            if (typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                const geocoder = new google.maps.Geocoder();
                const latLng = new google.maps.LatLng(latitude, longitude);
                
                geocoder.geocode({ location: latLng }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const address = results[0].formatted_address;
                        const locationField = document.querySelector('textarea[name="location"]');
                        
                        // Ask user if they want to update location with new address
                        if (confirm('Update location description with new GPS address?')) {
                            locationField.value = 'GPS: ' + latitude.toFixed(6) + ', ' + longitude.toFixed(6) + '\n' + address;
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>