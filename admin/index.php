<?php
/**
 * Admin Dashboard for QUICKBILL 305
 * Updated with Fixed Revenue Calculation for Current Year and Enhanced Charts
<<<<<<< HEAD
=======
 * Fixed Sidebar Toggle Functionality
 * FIXED: User ID reference error
>>>>>>> c9ccaba (Initial commit)
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Start session
session_start();

// Include auth and security
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Initialize auth and security
initAuth();
initSecurity();
<<<<<<< HEAD
=======
require_once '../includes/restriction_warning.php';
>>>>>>> c9ccaba (Initial commit)

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../auth/login.php');
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    setFlashMessage('error', 'Access denied. Admin privileges required.');
    header('Location: ../auth/login.php');
    exit();
}

<<<<<<< HEAD
=======

>>>>>>> c9ccaba (Initial commit)
$currentUser = getCurrentUser();
$userDisplayName = getUserDisplayName($currentUser);

// Fixed stats calculation
try {
    $db = new Database();
    $businessCount = 0;
    $propertyCount = 0;
    $userCount = 0;
    $totalRevenue = 0;
    $monthlyRevenue = 0;
    $previousYearRevenue = 0;
    $revenueChange = 0;

    try {
        $result = $db->fetchRow("SELECT COUNT(*) as count FROM businesses");
        $businessCount = $result['count'] ?? 0;
    } catch (Exception $e) {}

    try {
        $result = $db->fetchRow("SELECT COUNT(*) as count FROM properties");
        $propertyCount = $result['count'] ?? 0;
    } catch (Exception $e) {}

    try {
        $result = $db->fetchRow("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
        $userCount = $result['count'] ?? 0;
    } catch (Exception $e) {}

    // FIXED: Correct revenue calculation for current year only
    try {
        $result = $db->fetchRow("
            SELECT SUM(amount_paid) as total_revenue 
            FROM payments 
            WHERE payment_status = 'Successful' 
            AND YEAR(payment_date) = YEAR(CURDATE())
        ");
        $totalRevenue = $result['total_revenue'] ?? 0;
    } catch (Exception $e) {
        $totalRevenue = 0;
    }

    // Get additional revenue statistics
    try {
        // Get current month revenue
        $currentMonthResult = $db->fetchRow("
            SELECT SUM(amount_paid) as monthly_revenue 
            FROM payments 
            WHERE payment_status = 'Successful' 
            AND YEAR(payment_date) = YEAR(CURDATE())
            AND MONTH(payment_date) = MONTH(CURDATE())
        ");
        $monthlyRevenue = $currentMonthResult['monthly_revenue'] ?? 0;

        // Get previous year revenue for comparison
        $previousYearResult = $db->fetchRow("
            SELECT SUM(amount_paid) as previous_year_revenue 
            FROM payments 
            WHERE payment_status = 'Successful' 
            AND YEAR(payment_date) = YEAR(CURDATE()) - 1
        ");
        $previousYearRevenue = $previousYearResult['previous_year_revenue'] ?? 0;

        // Calculate percentage change
        $revenueChange = 0;
        if ($previousYearRevenue > 0) {
            $revenueChange = (($totalRevenue - $previousYearRevenue) / $previousYearRevenue) * 100;
        }

    } catch (Exception $e) {
        $monthlyRevenue = 0;
        $previousYearRevenue = 0;
        $revenueChange = 0;
    }

    // Get bills by zone for pie chart (showing current year bills)
    $billsByZone = [];
    try {
        // Get business bills by zone - corrected query
        $businessBills = $db->fetchAll("
            SELECT 
                z.zone_name,
                SUM(b.current_bill) as total_bills
            FROM bills b
            INNER JOIN businesses bs ON b.reference_id = bs.business_id AND b.bill_type = 'Business'
            INNER JOIN zones z ON bs.zone_id = z.zone_id
            WHERE YEAR(b.generated_at) = YEAR(CURDATE())
            GROUP BY z.zone_id, z.zone_name
        ");
        
        // Get property bills by zone - corrected query
        $propertyBills = $db->fetchAll("
            SELECT 
                z.zone_name,
                SUM(b.current_bill) as total_bills
            FROM bills b
            INNER JOIN properties p ON b.reference_id = p.property_id AND b.bill_type = 'Property'
            INNER JOIN zones z ON p.zone_id = z.zone_id
            WHERE YEAR(b.generated_at) = YEAR(CURDATE())
            GROUP BY z.zone_id, z.zone_name
        ");
        
        // Merge business and property bills by zone
        $zoneData = [];
        foreach ($businessBills as $row) {
            $zoneName = $row['zone_name'];
            $zoneData[$zoneName] = ($zoneData[$zoneName] ?? 0) + floatval($row['total_bills']);
        }
        
        foreach ($propertyBills as $row) {
            $zoneName = $row['zone_name'];
            $zoneData[$zoneName] = ($zoneData[$zoneName] ?? 0) + floatval($row['total_bills']);
        }
        
        $billsByZone = $zoneData;
        
<<<<<<< HEAD
        // Debug: Show what we found
        error_log("Bills by Zone Query Results: " . print_r($billsByZone, true));
        
=======
>>>>>>> c9ccaba (Initial commit)
    } catch (Exception $e) {
        error_log("Error fetching bills by zone: " . $e->getMessage());
        $billsByZone = [];
    }

    // Get monthly payments for line chart (actual payments, not bills)
    $monthlyPayments = [];
    try {
        $paymentResults = $db->fetchAll("
            SELECT 
                MONTH(payment_date) as month,
                SUM(amount_paid) as total_paid
            FROM payments 
            WHERE payment_status = 'Successful' 
            AND YEAR(payment_date) = YEAR(CURDATE())
            GROUP BY MONTH(payment_date)
            ORDER BY MONTH(payment_date)
        ");
        
        // Initialize all months with 0
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPayments[$i] = 0;
        }
        
        // Fill in actual data
        foreach ($paymentResults as $row) {
            $monthlyPayments[$row['month']] = floatval($row['total_paid']);
        }
        
<<<<<<< HEAD
        // Debug: Show what we found
        error_log("Monthly Payments Query Results: " . print_r($monthlyPayments, true));
        
=======
>>>>>>> c9ccaba (Initial commit)
    } catch (Exception $e) {
        error_log("Error fetching monthly payments: " . $e->getMessage());
        // Initialize empty array
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPayments[$i] = 0;
        }
    }

} catch (Exception $e) {
    $businessCount = 0;
    $propertyCount = 0;
    $userCount = 0;
    $totalRevenue = 0;
    $monthlyRevenue = 0;
    $previousYearRevenue = 0;
    $revenueChange = 0;
    $billsByZone = [];
    $monthlyPayments = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>

<<<<<<< HEAD
    <!-- Chart.js from CDNJS (CSP-friendly) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <!-- Local Chart.js fallback -->
    <script>
        // Check if Chart.js loaded, if not try local version
        window.addEventListener('load', function() {
            if (typeof Chart === 'undefined') {
                console.log('Chart.js not loaded from CDN, trying local version...');
                const script = document.createElement('script');
                script.src = '../assets/js/chart.umd.min.js';
                script.onload = function() {
                    console.log('Local Chart.js loaded successfully');
                    // Reinitialize charts after local Chart.js loads
                    setTimeout(initializeCharts, 100);
                };
                script.onerror = function() {
                    console.log('Chart.js not available, using custom charts');
                    window.chartsUnavailable = true;
                    // Initialize custom charts
                    setTimeout(initializeCharts, 100);
                };
                document.head.appendChild(script);
            }
        });
    </script>
    
    <!-- Multiple Icon Sources -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">

    <!-- Bootstrap for backup -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
=======
    <!-- Local Chart.js -->
    <script src="../assets/js/chart.min.js"></script>
>>>>>>> c9ccaba (Initial commit)
    
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

<<<<<<< HEAD
        /* Custom Icons (fallback if Font Awesome fails) */
=======
        /* Emoji Icons */
>>>>>>> c9ccaba (Initial commit)
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
        .icon-money::before { content: "💰"; }
        .icon-plus::before { content: "➕"; }
<<<<<<< HEAD
        .icon-server::before { content: "🖥️"; }
        .icon-database::before { content: "💾"; }
        .icon-shield::before { content: "🛡️"; }
        .icon-user-plus::before { content: "👤➕"; }
        .icon-history::before { content: "📜"; }
        .icon-question::before { content: "❓"; }
=======
        .icon-user-plus::before { content: "👤➕"; }
        .icon-history::before { content: "📜"; }
        .icon-question::before { content: "❓"; }
        .icon-user::before { content: "👤"; }
        .icon-chevron-down::before { content: "⌄"; }
>>>>>>> c9ccaba (Initial commit)

        /* Top Navigation */
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
<<<<<<< HEAD
=======
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
>>>>>>> c9ccaba (Initial commit)
        }

        .toggle-btn:hover {
            background: rgba(255,255,255,0.3);
<<<<<<< HEAD
=======
            transform: scale(1.05);
        }

        .toggle-btn:active {
            transform: scale(0.95);
>>>>>>> c9ccaba (Initial commit)
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-decoration: none;
<<<<<<< HEAD
=======
            display: flex;
            align-items: center;
            gap: 10px;
>>>>>>> c9ccaba (Initial commit)
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

        .user-profile:hover .user-avatar {
            transform: scale(1.05);
            border-color: rgba(255,255,255,0.4);
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

        .user-profile.active .dropdown-arrow {
            transform: rotate(180deg);
        }

<<<<<<< HEAD
=======
        .notification-badge {
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
        }

>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
        .dropdown-item i {
=======
        .dropdown-item .icon-user,
        .dropdown-item .icon-cog,
        .dropdown-item .icon-history,
        .dropdown-item .icon-question,
        .dropdown-item .icon-logout {
>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
        /* Sidebar */
=======
        /* Sidebar - FIXED */
>>>>>>> c9ccaba (Initial commit)
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            color: white;
            transition: all 0.3s ease;
            overflow: hidden;
<<<<<<< HEAD
=======
            flex-shrink: 0;
>>>>>>> c9ccaba (Initial commit)
        }

        .sidebar.hidden {
            width: 0;
            min-width: 0;
        }

        .sidebar-content {
            width: 280px;
            padding: 20px 0;
<<<<<<< HEAD
=======
            transition: all 0.3s ease;
        }

        .sidebar.hidden .sidebar-content {
            opacity: 0;
            transform: translateX(-20px);
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
            display: flex;
            align-items: center;
>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
        /* Main Content */
=======
        /* Main Content - FIXED */
>>>>>>> c9ccaba (Initial commit)
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f8f9fa;
            transition: all 0.3s ease;
<<<<<<< HEAD
=======
            min-width: 0; /* Allows flex item to shrink */
>>>>>>> c9ccaba (Initial commit)
        }

        /* Welcome Section */
        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            color: #718096;
            font-size: 16px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }

        .stat-card.info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 12px;
            opacity: 0.9;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-icon {
            font-size: 24px;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
        }

        .stat-subtitle {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .revenue-change {
            font-size: 12px;
            margin-top: 6px;
            padding: 3px 6px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: inline-block;
        }

        .revenue-change.positive {
            background: rgba(72, 187, 120, 0.3);
        }

        .revenue-change.negative {
            background: rgba(245, 101, 101, 0.3);
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
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .card-body {
            padding: 25px;
        }

        /* Action Buttons */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
<<<<<<< HEAD
            display: inline-block;
=======
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
>>>>>>> c9ccaba (Initial commit)
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }

        .action-btn.success { background: #48bb78; }
        .action-btn.info { background: #4299e1; }
        .action-btn.warning { background: #ed8936; }
        .action-btn.purple { background: #9f7aea; }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .chart-card h6 {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .pie-chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* No Data Message */
        .no-data-message {
            text-align: center;
            color: #718096;
            padding: 40px;
            font-style: italic;
        }

<<<<<<< HEAD
        /* Custom Chart Styles (Fallback when Chart.js is not available) */
        .custom-line-chart {
            padding: 15px;
            background: white;
            border-radius: 8px;
            height: 300px;
            position: relative;
        }

        .chart-grid {
            position: relative;
            height: 100%;
            width: 100%;
        }

        .line-chart-svg {
            width: 100%;
            height: 100%;
        }

        .chart-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 12px;
            color: #718096;
            border: 1px solid #e2e8f0;
        }

        .custom-pie-chart {
            padding: 15px;
            background: white;
            border-radius: 8px;
            height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pie-chart-svg {
            width: 200px;
            height: 200px;
            margin-bottom: 15px;
        }

        .pie-slice {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pie-slice:hover {
            opacity: 0.8;
            filter: brightness(1.1);
        }

        .pie-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
            max-height: 80px;
            overflow-y: auto;
        }

        .pie-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
        }

        .pie-legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .pie-legend-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pie-legend-label {
            font-weight: 600;
            color: #2d3748;
        }

        .pie-legend-value {
            color: #4a5568;
            font-size: 10px;
        }

        .chart-title {
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .chart-bars {
            display: flex;
            align-items: end;
            justify-content: space-between;
            height: 200px;
            padding: 0 10px;
            border-bottom: 2px solid #e2e8f0;
            position: relative;
        }

        .chart-bar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            margin: 0 2px;
        }

        .chart-bar {
            width: 100%;
            max-width: 20px;
            min-height: 3px;
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .chart-bar:hover {
            opacity: 0.8;
            transform: scaleY(1.05);
        }

        .chart-bar-label {
            margin-top: 8px;
            font-size: 11px;
            color: #4a5568;
            text-align: center;
        }

        .pie-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            height: 200px;
        }

        .pie-item {
            padding: 15px;
            border-radius: 8px;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .pie-item:hover {
            transform: scale(1.05);
        }

        .pie-item-label {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .pie-item-value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .pie-item-percentage {
            font-size: 11px;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100%;
                z-index: 999;
                transform: translateX(-100%);
            }

            .sidebar.show {
=======
        /* Mobile Responsive - FIXED */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: calc(100vh - 80px);
                top: 80px;
                left: 0;
                z-index: 999;
                transform: translateX(-100%);
                width: 280px !important; /* Force width on mobile */
            }

            .sidebar.mobile-show {
>>>>>>> c9ccaba (Initial commit)
                transform: translateX(0);
            }

            .sidebar.hidden {
                transform: translateX(-100%);
<<<<<<< HEAD
                width: 280px;
=======
            }

            .sidebar-content {
                opacity: 1;
                transform: none;
>>>>>>> c9ccaba (Initial commit)
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .container {
<<<<<<< HEAD
                flex-direction: column;
=======
                flex-direction: row; /* Keep flex direction */
            }

            /* Mobile overlay */
            .mobile-overlay {
                position: fixed;
                top: 80px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
                display: none;
            }

            .mobile-overlay.show {
                display: block;
>>>>>>> c9ccaba (Initial commit)
            }
        }

        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
<<<<<<< HEAD
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

            <a href="../admin/index.php" class="brand">
                <i class="fas fa-receipt"></i>
                <span class="icon-receipt" style="display: none;"></span>
=======

        /* Sidebar indicator */
        .sidebar-indicator {
            position: fixed;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            transition: all 0.3s;
        }

        .sidebar-indicator:hover {
            background: #5a67d8;
            transform: translateY(-50%) scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
            <button class="toggle-btn" onclick="toggleSidebar()" id="toggleBtn" title="Toggle Sidebar">
                <span class="icon-menu"></span>
            </button>

            <a href="../admin/index.php" class="brand">
                <span class="icon-receipt"></span>
>>>>>>> c9ccaba (Initial commit)
                <?php echo APP_NAME; ?>
            </a>
        </div>

        <div class="user-section">
            <!-- Notification Bell -->
            <div style="position: relative; margin-right: 10px;">
                <a href="notifications/index.php" style="
                    background: rgba(255,255,255,0.2);
                    border: none;
                    color: white;
                    font-size: 18px;
                    padding: 10px;
                    border-radius: 50%;
                    cursor: pointer;
                    transition: all 0.3s;
                    text-decoration: none;
                    display: inline-block;
                " onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'">
<<<<<<< HEAD
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
=======
                    <span class="icon-bell"></span>
                </a>
                <span class="notification-badge">3</span>
>>>>>>> c9ccaba (Initial commit)
            </div>

            <div class="user-profile" onclick="toggleUserDropdown()" id="userProfile">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['first_name'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars(getCurrentUserRole()); ?></div>
                </div>
<<<<<<< HEAD
                <i class="fas fa-chevron-down dropdown-arrow"></i>
=======
                <span class="icon-chevron-down dropdown-arrow"></span>
>>>>>>> c9ccaba (Initial commit)

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
<<<<<<< HEAD
                        <a href="users/view.php?id=<?php echo $currentUser['id']; ?>" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span class="icon-users" style="display: none;"></span>
                            My Profile
                        </a>
                        <a href="settings/index.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span class="icon-cog" style="display: none;"></span>
                            Account Settings
                        </a>
                        <a href="logs/user_activity.php" class="dropdown-item">
                            <i class="fas fa-history"></i>
                            <span class="icon-history" style="display: none;"></span>
                            Activity Log
                        </a>
                        <a href="#" class="dropdown-item" onclick="alert('Help documentation coming soon!')">
                            <i class="fas fa-question-circle"></i>
                            <span class="icon-question" style="display: none;"></span>
=======
                        <a href="users/view.php?id=<?php echo $currentUser['user_id']; ?>" class="dropdown-item">
                            <span class="icon-user"></span>
                            My Profile
                        </a>
                        <a href="settings/index.php" class="dropdown-item">
                            <span class="icon-cog"></span>
                            Account Settings
                        </a>
                        <a href="logs/user_activity.php" class="dropdown-item">
                            <span class="icon-history"></span>
                            Activity Log
                        </a>
                        <a href="#" class="dropdown-item" onclick="alert('Help documentation coming soon!')">
                            <span class="icon-question"></span>
>>>>>>> c9ccaba (Initial commit)
                            Help & Support
                        </a>
                        <div style="height: 1px; background: #e2e8f0; margin: 10px 0;"></div>
                        <a href="../auth/logout.php" class="dropdown-item logout">
<<<<<<< HEAD
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="icon-logout" style="display: none;"></span>
=======
                            <span class="icon-logout"></span>
>>>>>>> c9ccaba (Initial commit)
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
                        <a href="../admin/index.php" class="nav-link active">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="icon-dashboard" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-dashboard"></span>
>>>>>>> c9ccaba (Initial commit)
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Core Management -->
                <div class="nav-section">
                    <div class="nav-title">Core Management</div>
                    <div class="nav-item">
                        <a href="users/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-users"></i>
                                <span class="icon-users" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-users"></span>
>>>>>>> c9ccaba (Initial commit)
                            Users
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="businesses/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-building"></i>
                                <span class="icon-building" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-building"></span>
>>>>>>> c9ccaba (Initial commit)
                            Businesses
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="properties/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-home"></i>
                                <span class="icon-home" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-home"></span>
>>>>>>> c9ccaba (Initial commit)
                            Properties
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="zones/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-map-marked-alt"></i>
                                <span class="icon-map" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-map"></span>
>>>>>>> c9ccaba (Initial commit)
                            Zones & Areas
                        </a>
                    </div>
                </div>

                <!-- Billing & Payments -->
                <div class="nav-section">
                    <div class="nav-title">Billing & Payments</div>
                    <div class="nav-item">
                        <a href="billing/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-file-invoice"></i>
                                <span class="icon-invoice" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-invoice"></span>
>>>>>>> c9ccaba (Initial commit)
                            Billing
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="payments/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-credit-card"></i>
                                <span class="icon-credit" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-credit"></span>
>>>>>>> c9ccaba (Initial commit)
                            Payments
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="fee_structure/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-tags"></i>
                                <span class="icon-tags" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-tags"></span>
>>>>>>> c9ccaba (Initial commit)
                            Fee Structure
                        </a>
                    </div>
                </div>

                <!-- Reports & System -->
                <div class="nav-section">
                    <div class="nav-title">Reports & System</div>
                    <div class="nav-item">
                        <a href="reports/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-chart-bar"></i>
                                <span class="icon-chart" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-chart"></span>
>>>>>>> c9ccaba (Initial commit)
                            Reports
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="notifications/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-bell"></i>
                                <span class="icon-bell" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-bell"></span>
>>>>>>> c9ccaba (Initial commit)
                            Notifications
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="settings/index.php" class="nav-link">
<<<<<<< HEAD
                            <span class="nav-icon">
                                <i class="fas fa-cog"></i>
                                <span class="icon-cog" style="display: none;"></span>
                            </span>
=======
                            <span class="nav-icon icon-cog"></span>
>>>>>>> c9ccaba (Initial commit)
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

<<<<<<< HEAD
=======
        <!-- Sidebar Indicator for when sidebar is hidden -->
        <div class="sidebar-indicator" id="sidebarIndicator" onclick="showSidebar()" title="Show Sidebar">
            <span class="icon-menu"></span>
        </div>

>>>>>>> c9ccaba (Initial commit)
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Welcome Section -->
            <div class="welcome-card">
                <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($userDisplayName); ?>! 👋</h1>
                <p class="welcome-subtitle">Here's what's happening with your billing system today.</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-header">
                        <div class="stat-title">Revenue (<?php echo date('Y'); ?>)</div>
                        <div class="stat-icon">
<<<<<<< HEAD
                            <i class="fas fa-money-bill-wave"></i>
                            <span class="icon-money" style="display: none;"></span>
=======
                            <span class="icon-money"></span>
>>>>>>> c9ccaba (Initial commit)
                        </div>
                    </div>
                    <div class="stat-value">₵ <?php echo number_format($totalRevenue, 2); ?></div>
                    <div class="stat-subtitle">This Month: ₵ <?php echo number_format($monthlyRevenue, 2); ?></div>
                    <?php if ($previousYearRevenue > 0): ?>
                        <div class="revenue-change <?php echo $revenueChange >= 0 ? 'positive' : 'negative'; ?>">
                            <?php echo $revenueChange >= 0 ? '↗' : '↘'; ?> 
                            <?php echo abs(round($revenueChange, 1)); ?>% vs <?php echo date('Y') - 1; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <div class="stat-title">Total Businesses</div>
                        <div class="stat-icon">
<<<<<<< HEAD
                            <i class="fas fa-building"></i>
                            <span class="icon-building" style="display: none;"></span>
=======
                            <span class="icon-building"></span>
>>>>>>> c9ccaba (Initial commit)
                        </div>
                    </div>
                    <div class="stat-value"><?php echo number_format($businessCount); ?></div>
                </div>

                <div class="stat-card info">
                    <div class="stat-header">
                        <div class="stat-title">Total Properties</div>
                        <div class="stat-icon">
<<<<<<< HEAD
                            <i class="fas fa-home"></i>
                            <span class="icon-home" style="display: none;"></span>
=======
                            <span class="icon-home"></span>
>>>>>>> c9ccaba (Initial commit)
                        </div>
                    </div>
                    <div class="stat-value"><?php echo number_format($propertyCount); ?></div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-title">Total Users</div>
                        <div class="stat-icon">
<<<<<<< HEAD
                            <i class="fas fa-users"></i>
                            <span class="icon-users" style="display: none;"></span>
=======
                            <span class="icon-users"></span>
>>>>>>> c9ccaba (Initial commit)
                        </div>
                    </div>
                    <div class="stat-value"><?php echo number_format($userCount); ?></div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">⚡ Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="actions-grid">
                        <a href="users/add.php" class="action-btn purple">
<<<<<<< HEAD
                            <i class="fas fa-user-plus"></i>
                            <span class="icon-user-plus" style="display: none;"></span>
                            Add User
                        </a>
                        <a href="businesses/add.php" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span class="icon-plus" style="display: none;"></span>
                            Add Business
                        </a>
                        <a href="properties/add.php" class="action-btn success">
                            <i class="fas fa-plus"></i>
                            <span class="icon-plus" style="display: none;"></span>
                            Add Property
                        </a>
                        <a href="payments/record.php" class="action-btn info">
                            <i class="fas fa-money-bill"></i>
                            <span class="icon-money" style="display: none;"></span>
                            Record Payment
                        </a>
                        <a href="billing/generate.php" class="action-btn warning">
                            <i class="fas fa-file-invoice"></i>
                            <span class="icon-invoice" style="display: none;"></span>
                            Generate Bills
                        </a>
                        <a href="users/index.php" class="action-btn info">
                            <i class="fas fa-users"></i>
                            <span class="icon-users" style="display: none;"></span>
                            Manage Users
                        </a>
                        <a href="businesses/index.php" class="action-btn success">
                            <i class="fas fa-building"></i>
                            <span class="icon-building" style="display: none;"></span>
                            Manage Businesses
                        </a>
                        <a href="properties/index.php" class="action-btn purple">
                            <i class="fas fa-home"></i>
                            <span class="icon-home" style="display: none;"></span>
=======
                            <span class="icon-user-plus"></span>
                            Add User
                        </a>
                        <a href="businesses/add.php" class="action-btn">
                            <span class="icon-plus"></span>
                            Add Business
                        </a>
                        <a href="properties/add.php" class="action-btn success">
                            <span class="icon-plus"></span>
                            Add Property
                        </a>
                        <a href="payments/record.php" class="action-btn info">
                            <span class="icon-money"></span>
                            Record Payment
                        </a>
                        <a href="billing/generate.php" class="action-btn warning">
                            <span class="icon-invoice"></span>
                            Generate Bills
                        </a>
                        <a href="users/index.php" class="action-btn info">
                            <span class="icon-users"></span>
                            Manage Users
                        </a>
                        <a href="businesses/index.php" class="action-btn success">
                            <span class="icon-building"></span>
                            Manage Businesses
                        </a>
                        <a href="properties/index.php" class="action-btn purple">
                            <span class="icon-home"></span>
>>>>>>> c9ccaba (Initial commit)
                            Manage Properties
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Monthly Payments Chart -->
                <div class="chart-card">
                    <h6>💰 Monthly Payments - <?php echo date('Y'); ?></h6>
                    <div class="chart-container">
                        <canvas id="paymentsChart"></canvas>
                    </div>
                </div>

                <!-- Bills by Zone Pie Chart -->
                <div class="chart-card">
                    <h6>🏢 Bills by Zone - <?php echo date('Y'); ?></h6>
                    <div class="pie-chart-container">
                        <canvas id="zoneChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
<<<<<<< HEAD
        // Check if Font Awesome loaded, if not show emoji icons
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // Check if Font Awesome icons are working
                const testIcon = document.querySelector('.fas.fa-bars');
                if (!testIcon || getComputedStyle(testIcon, ':before').content === 'none') {
                    // Font Awesome didn't load, show emoji fallbacks
                    document.querySelectorAll('.fas, .far').forEach(function(icon) {
                        icon.style.display = 'none';
                    });
                    document.querySelectorAll('[class*="icon-"]').forEach(function(emoji) {
                        emoji.style.display = 'inline';
                    });
                }
            }, 100);

            // Initialize charts with a delay to ensure Chart.js is loaded
            setTimeout(function() {
                console.log('Initializing charts...');
                console.log('Chart.js available:', typeof Chart !== 'undefined');
                console.log('Monthly payments data:', monthlyPaymentsData);
                console.log('Bills by zone data:', billsByZoneLabels, billsByZoneData);
                initializeCharts();
            }, 300);
        });

        // Enhanced Chart.js Configuration from the original code
=======
        // Chart data
>>>>>>> c9ccaba (Initial commit)
        const monthlyPaymentsData = <?php echo json_encode(array_values($monthlyPayments)); ?>;
        const billsByZoneLabels = <?php echo json_encode(array_keys($billsByZone)); ?>;
        const billsByZoneData = <?php echo json_encode(array_values($billsByZone)); ?>;

<<<<<<< HEAD
        function initializeCharts() {
            // Check if Chart.js is available
            if (typeof Chart === 'undefined' || window.chartsUnavailable) {
                console.log('Chart.js not available, creating custom charts');
                createCustomCharts();
=======
        // Global variables
        let isMobile = window.innerWidth <= 768;

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing dashboard...');
            console.log('Chart.js available:', typeof Chart !== 'undefined');
            console.log('Monthly payments data:', monthlyPaymentsData);
            console.log('Bills by zone data:', billsByZoneLabels, billsByZoneData);
            
            // Initialize mobile detection
            checkMobile();
            
            // Restore sidebar state
            restoreSidebarState();
            
            setTimeout(function() {
                initializeCharts();
            }, 300);
        });

        // Check if mobile
        function checkMobile() {
            isMobile = window.innerWidth <= 768;
        }

        // FIXED: Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const indicator = document.getElementById('sidebarIndicator');
            const overlay = document.getElementById('mobileOverlay');
            
            if (isMobile) {
                // Mobile behavior
                if (sidebar.classList.contains('mobile-show')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                // Desktop behavior
                sidebar.classList.toggle('hidden');
                const isHidden = sidebar.classList.contains('hidden');
                
                // Show/hide indicator
                if (isHidden) {
                    setTimeout(() => {
                        indicator.style.display = 'flex';
                    }, 300);
                } else {
                    indicator.style.display = 'none';
                }
                
                // Save state
                localStorage.setItem('sidebarHidden', isHidden);
                
                console.log('Sidebar toggled, hidden:', isHidden);
            }
        }

        // Mobile sidebar functions
        function openMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.add('mobile-show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.remove('mobile-show');
            overlay.classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Show sidebar (from indicator)
        function showSidebar() {
            const sidebar = document.getElementById('sidebar');
            const indicator = document.getElementById('sidebarIndicator');
            
            sidebar.classList.remove('hidden');
            indicator.style.display = 'none';
            localStorage.setItem('sidebarHidden', false);
        }

        // Restore sidebar state
        function restoreSidebarState() {
            const sidebar = document.getElementById('sidebar');
            const indicator = document.getElementById('sidebarIndicator');
            const sidebarHidden = localStorage.getItem('sidebarHidden');
            
            if (!isMobile && sidebarHidden === 'true') {
                sidebar.classList.add('hidden');
                indicator.style.display = 'flex';
            }
        }

        function initializeCharts() {
            // Check if Chart.js is available
            if (typeof Chart === 'undefined') {
                console.log('Chart.js not available, showing fallback message');
                showChartFallbacks();
>>>>>>> c9ccaba (Initial commit)
                return;
            }

            try {
<<<<<<< HEAD
                // Try to use Chart.js first
                console.log('Attempting to create Chart.js charts...');
                
                // Enhanced Monthly Payments Line Chart (from first code)
=======
                console.log('Creating Chart.js charts...');
                
                // Monthly Payments Line Chart
>>>>>>> c9ccaba (Initial commit)
                const paymentsChart = new Chart(document.getElementById('paymentsChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Payments (₵)',
                            data: monthlyPaymentsData,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e2e8f0'
                                },
<<<<<<< HEAD
                                title: {
                                    display: true,
                                    text: 'Amount Paid (₵)'
=======
                                ticks: {
                                    callback: function(value) {
                                        return '₵ ' + value.toLocaleString();
                                    }
>>>>>>> c9ccaba (Initial commit)
                                }
                            },
                            x: {
                                grid: {
                                    display: false
<<<<<<< HEAD
                                },
                                title: {
                                    display: true,
                                    text: 'Month'
=======
>>>>>>> c9ccaba (Initial commit)
                                }
                            }
                        },
                        plugins: {
<<<<<<< HEAD
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y.toLocaleString('en-US', { style: 'currency', currency: 'GHS' });
                                        return label;
=======
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Payments: ₵ ' + context.parsed.y.toLocaleString();
>>>>>>> c9ccaba (Initial commit)
                                    }
                                }
                            }
                        }
                    }
                });

<<<<<<< HEAD
                // Enhanced Bills by Zone Pie Chart (from first code)
                const zoneChart = new Chart(document.getElementById('zoneChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: billsByZoneLabels.length > 0 ? billsByZoneLabels : ['No Data Available'],
                        datasets: [{
                            label: 'Bills (₵)',
                            data: billsByZoneData.length > 0 ? billsByZoneData : [1],
                            backgroundColor: [
=======
                // Bills by Zone Pie Chart
                const hasZoneData = billsByZoneData.length > 0 && billsByZoneData.some(val => val > 0);
                const zoneChart = new Chart(document.getElementById('zoneChart').getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: hasZoneData ? billsByZoneLabels : ['No Data Available'],
                        datasets: [{
                            label: 'Bills (₵)',
                            data: hasZoneData ? billsByZoneData : [1],
                            backgroundColor: hasZoneData ? [
>>>>>>> c9ccaba (Initial commit)
                                '#667eea',
                                '#48bb78',
                                '#4299e1',
                                '#ed8936',
                                '#9f7aea',
                                '#f56565'
<<<<<<< HEAD
                            ],
=======
                            ] : ['#e2e8f0'],
>>>>>>> c9ccaba (Initial commit)
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
<<<<<<< HEAD
                                display: true,
=======
                                display: hasZoneData,
>>>>>>> c9ccaba (Initial commit)
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
<<<<<<< HEAD
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.toLocaleString('en-US', { style: 'currency', currency: 'GHS' });
                                        return label;
=======
                                enabled: hasZoneData,
                                callbacks: {
                                    label: function(context) {
                                        if (!hasZoneData) return '';
                                        return context.label + ': ₵ ' + context.parsed.toLocaleString();
>>>>>>> c9ccaba (Initial commit)
                                    }
                                }
                            }
                        }
                    }
                });
                
                console.log('Chart.js charts created successfully');
            } catch (error) {
                console.error('Error creating Chart.js charts:', error);
<<<<<<< HEAD
                console.log('Falling back to custom charts');
                createCustomCharts();
            }
        }

        // Custom fallback charts using pure CSS/HTML
        function createCustomCharts() {
            createCustomLineChart();
            createCustomPieChart();
        }

        function createCustomLineChart() {
            const container = document.getElementById('paymentsChart').parentElement;
            const maxValue = Math.max(...monthlyPaymentsData);
            const hasData = monthlyPaymentsData.some(val => val > 0);
            
            if (!hasData) {
                // Create a sample chart to show the structure
                const sampleData = [1200, 1800, 2100, 1500, 2300, 2800, 1900, 2400, 2600, 1700, 2200, 2500];
                const maxSample = Math.max(...sampleData);
                
                let chartHTML = '<div class="custom-line-chart">';
                chartHTML += '<div class="chart-grid">';
                
                // Create SVG line chart
                chartHTML += '<svg class="line-chart-svg" viewBox="0 0 400 200">';
                
                // Grid lines
                for (let i = 0; i <= 4; i++) {
                    const y = (i * 40) + 20;
                    chartHTML += `<line x1="40" y1="${y}" x2="380" y2="${y}" stroke="#e2e8f0" stroke-width="1"/>`;
                }
                
                // Create path for line
                let pathData = 'M ';
                sampleData.forEach((value, index) => {
                    const x = 40 + (index * 28);
                    const y = 180 - ((value / maxSample) * 140);
                    pathData += `${x},${y} `;
                });
                
                // Area fill
                chartHTML += `<path d="${pathData}L 376,180 L 40,180 Z" fill="rgba(102, 126, 234, 0.1)" stroke="none"/>`;
                
                // Line
                chartHTML += `<path d="${pathData}" fill="none" stroke="#667eea" stroke-width="3"/>`;
                
                // Points
                sampleData.forEach((value, index) => {
                    const x = 40 + (index * 28);
                    const y = 180 - ((value / maxSample) * 140);
                    chartHTML += `<circle cx="${x}" cy="${y}" r="4" fill="#667eea" stroke="#fff" stroke-width="2"/>`;
                });
                
                // Labels
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                months.forEach((month, index) => {
                    const x = 40 + (index * 28);
                    chartHTML += `<text x="${x}" y="195" text-anchor="middle" font-size="10" fill="#4a5568">${month}</text>`;
                });
                
                chartHTML += '</svg>';
                chartHTML += '<div class="chart-overlay">Sample Data - No payments recorded yet</div>';
                chartHTML += '</div></div>';
                
                container.innerHTML = chartHTML;
                return;
            }

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            let chartHTML = '<div class="custom-line-chart">';
            chartHTML += '<div class="chart-grid">';
            
            // Create SVG line chart
            chartHTML += '<svg class="line-chart-svg" viewBox="0 0 400 200">';
            
            // Grid lines
            for (let i = 0; i <= 4; i++) {
                const y = (i * 40) + 20;
                chartHTML += `<line x1="40" y1="${y}" x2="380" y2="${y}" stroke="#e2e8f0" stroke-width="1"/>`;
            }
            
            // Create path for line
            let pathData = 'M ';
            monthlyPaymentsData.forEach((value, index) => {
                const x = 40 + (index * 28);
                const y = 180 - ((value / maxValue) * 140);
                pathData += `${x},${y} `;
            });
            
            // Area fill
            chartHTML += `<path d="${pathData}L 376,180 L 40,180 Z" fill="rgba(102, 126, 234, 0.1)" stroke="none"/>`;
            
            // Line
            chartHTML += `<path d="${pathData}" fill="none" stroke="#667eea" stroke-width="3"/>`;
            
            // Points
            monthlyPaymentsData.forEach((value, index) => {
                const x = 40 + (index * 28);
                const y = 180 - ((value / maxValue) * 140);
                chartHTML += `<circle cx="${x}" cy="${y}" r="4" fill="#667eea" stroke="#fff" stroke-width="2"/>`;
            });
            
            // Labels
            months.forEach((month, index) => {
                const x = 40 + (index * 28);
                chartHTML += `<text x="${x}" y="195" text-anchor="middle" font-size="10" fill="#4a5568">${month}</text>`;
            });
            
            chartHTML += '</svg>';
            chartHTML += '</div></div>';
            container.innerHTML = chartHTML;
        }

        function createCustomPieChart() {
            const container = document.getElementById('zoneChart').parentElement;
            const hasData = billsByZoneData.length > 0 && billsByZoneData.some(val => val > 0);
            
            if (!hasData) {
                container.innerHTML = '<div class="no-data-message">No bill data available by zone for this year</div>';
                return;
            }

            const total = billsByZoneData.reduce((sum, val) => sum + val, 0);
            const colors = ['#667eea', '#48bb78', '#4299e1', '#ed8936', '#9f7aea', '#f56565'];
            
            let chartHTML = '<div class="custom-pie-chart">';
            
            // Create SVG pie chart
            chartHTML += '<svg class="pie-chart-svg" viewBox="0 0 200 200">';
            
            let currentAngle = 0;
            const centerX = 100;
            const centerY = 100;
            const radius = 80;
            
            billsByZoneLabels.forEach((label, index) => {
                const value = billsByZoneData[index];
                const percentage = (value / total) * 100;
                const sliceAngle = (value / total) * 360;
                
                // Calculate slice path
                const startAngle = currentAngle;
                const endAngle = currentAngle + sliceAngle;
                
                const startAngleRad = (startAngle * Math.PI) / 180;
                const endAngleRad = (endAngle * Math.PI) / 180;
                
                const largeArcFlag = sliceAngle > 180 ? 1 : 0;
                
                const x1 = centerX + radius * Math.cos(startAngleRad);
                const y1 = centerY + radius * Math.sin(startAngleRad);
                const x2 = centerX + radius * Math.cos(endAngleRad);
                const y2 = centerY + radius * Math.sin(endAngleRad);
                
                const pathData = [
                    "M", centerX, centerY,
                    "L", x1, y1,
                    "A", radius, radius, 0, largeArcFlag, 1, x2, y2,
                    "Z"
                ].join(" ");
                
                chartHTML += `<path d="${pathData}" fill="${colors[index % colors.length]}" stroke="#fff" stroke-width="2" class="pie-slice" data-label="${label}" data-value="₵${value.toLocaleString()}" data-percentage="${percentage.toFixed(1)}%"/>`;
                
                currentAngle += sliceAngle;
            });
            
            chartHTML += '</svg>';
            
            // Legend
            chartHTML += '<div class="pie-legend">';
            billsByZoneLabels.forEach((label, index) => {
                const value = billsByZoneData[index];
                const percentage = ((value / total) * 100).toFixed(1);
                chartHTML += `
                    <div class="pie-legend-item">
                        <div class="pie-legend-color" style="background: ${colors[index % colors.length]};"></div>
                        <div class="pie-legend-text">
                            <div class="pie-legend-label">${label}</div>
                            <div class="pie-legend-value">₵${value.toLocaleString()} (${percentage}%)</div>
                        </div>
                    </div>
                `;
            });
            chartHTML += '</div>';
            
            chartHTML += '</div>';
            container.innerHTML = chartHTML;
            
            // Add hover effects
            setTimeout(() => {
                document.querySelectorAll('.pie-slice').forEach(slice => {
                    slice.addEventListener('mouseover', function() {
                        this.style.opacity = '0.8';
                        this.style.transform = 'scale(1.05)';
                        this.style.transformOrigin = 'center';
                    });
                    slice.addEventListener('mouseout', function() {
                        this.style.opacity = '1';
                        this.style.transform = 'scale(1)';
                    });
                });
            }, 100);
        }

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
            
            // Save state
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

=======
                showChartFallbacks();
            }
        }

        function showChartFallbacks() {
            // Show fallback messages for charts
            const paymentsContainer = document.getElementById('paymentsChart').parentElement;
            const zoneContainer = document.getElementById('zoneChart').parentElement;
            
            paymentsContainer.innerHTML = '<div class="no-data-message">📊 Chart will appear here when Chart.js is available</div>';
            zoneContainer.innerHTML = '<div class="no-data-message">🍰 Chart will appear here when Chart.js is available</div>';
        }

>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        // Handle mobile responsiveness
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('hidden');
            }
        });

        // Add smooth hover effects
=======
        // Handle window resize
        window.addEventListener('resize', function() {
            const wasMobile = isMobile;
            checkMobile();
            
            // If switching between mobile and desktop
            if (wasMobile !== isMobile) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                const indicator = document.getElementById('sidebarIndicator');
                
                // Reset all states
                sidebar.classList.remove('mobile-show', 'hidden');
                overlay.classList.remove('show');
                indicator.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Apply appropriate state
                if (!isMobile) {
                    restoreSidebarState();
                }
            }
        });

        // Add enhanced interactions
>>>>>>> c9ccaba (Initial commit)
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add pulse effect to notification badge
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                setInterval(() => {
                    badge.style.animation = 'none';
                    setTimeout(() => {
                        badge.style.animation = 'pulse 2s infinite';
                    }, 100);
                }, 5000);
            }
<<<<<<< HEAD
=======

            // Add click feedback to toggle button
            const toggleBtn = document.getElementById('toggleBtn');
            toggleBtn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
>>>>>>> c9ccaba (Initial commit)
        });
    </script>
</body>
</html>