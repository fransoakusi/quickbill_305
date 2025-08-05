<?php
/**
 * Business Management - View Business Profile
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
if (!hasPermission('businesses.view')) {
    setFlashMessage('error', 'Access denied. You do not have permission to view businesses.');
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

$pageTitle = 'Business Profile';
$currentUser = getCurrentUser();
$userDisplayName = getUserDisplayName($currentUser);

// Get business ID from URL
$businessId = intval($_GET['id'] ?? 0);

if (!$businessId) {
    setFlashMessage('error', 'Invalid business ID.');
    header('Location: index.php');
    exit();
}

try {
    $db = new Database();
    
    // Get business details with related data
    $businessQuery = "SELECT b.*, z.zone_name, sz.sub_zone_name, sz.sub_zone_code,
                             u.first_name, u.last_name, u.username,
                             bf.fee_amount as fee_structure_amount
                      FROM businesses b 
                      LEFT JOIN zones z ON b.zone_id = z.zone_id 
                      LEFT JOIN sub_zones sz ON b.sub_zone_id = sz.sub_zone_id
                      LEFT JOIN users u ON b.created_by = u.user_id
                      LEFT JOIN business_fee_structure bf ON b.business_type = bf.business_type 
                                                          AND b.category = bf.category 
                                                          AND bf.is_active = 1
                      WHERE b.business_id = ?";
    
    $business = $db->fetchRow($businessQuery, [$businessId]);
    
    if (!$business) {
        setFlashMessage('error', 'Business not found.');
        header('Location: index.php');
        exit();
    }
    
<<<<<<< HEAD
=======
    // Calculate remaining balance (outstanding amount after all payments)
    $totalPaymentsQuery = "SELECT COALESCE(SUM(p.amount_paid), 0) as total_paid
                          FROM payments p 
                          INNER JOIN bills b ON p.bill_id = b.bill_id 
                          WHERE b.bill_type = 'Business' AND b.reference_id = ? 
                          AND p.payment_status = 'Successful'";
    $totalPaymentsResult = $db->fetchRow($totalPaymentsQuery, [$businessId]);
    $totalPaid = $totalPaymentsResult['total_paid'] ?? 0;
    
    // Calculate remaining balance: amount payable minus total successful payments
    $remainingBalance = max(0, $business['amount_payable'] - $totalPaid);
    
>>>>>>> c9ccaba (Initial commit)
    // Get billing history
    $billsQuery = "SELECT * FROM bills 
                   WHERE bill_type = 'Business' AND reference_id = ? 
                   ORDER BY billing_year DESC, generated_at DESC";
    $bills = $db->fetchAll($billsQuery, [$businessId]);
    
    // Get payment history
    $paymentsQuery = "SELECT p.*, b.bill_number, b.billing_year 
                      FROM payments p 
                      INNER JOIN bills b ON p.bill_id = b.bill_id 
                      WHERE b.bill_type = 'Business' AND b.reference_id = ? 
                      ORDER BY p.payment_date DESC";
    $payments = $db->fetchAll($paymentsQuery, [$businessId]);
    
    // Get recent audit logs for this business
    $auditQuery = "SELECT al.*, u.first_name, u.last_name 
                   FROM audit_logs al 
                   LEFT JOIN users u ON al.user_id = u.user_id 
                   WHERE al.table_name = 'businesses' AND al.record_id = ? 
                   ORDER BY al.created_at DESC 
                   LIMIT 10";
    $auditLogs = $db->fetchAll($auditQuery, [$businessId]);
    
    // Calculate statistics
    $stats = [
        'total_bills' => count($bills),
        'total_payments' => count($payments),
        'total_paid' => array_sum(array_column($payments, 'amount_paid')),
<<<<<<< HEAD
=======
        'remaining_balance' => $remainingBalance,
>>>>>>> c9ccaba (Initial commit)
        'last_payment' => !empty($payments) ? $payments[0]['payment_date'] : null
    ];
    
} catch (Exception $e) {
    writeLog("Business view error: " . $e->getMessage(), 'ERROR');
    setFlashMessage('error', 'An error occurred while loading business details.');
    header('Location: index.php');
    exit();
}
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
<<<<<<< HEAD
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
=======
>>>>>>> c9ccaba (Initial commit)
    
    <!-- Bootstrap for backup -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
<<<<<<< HEAD
=======
    <!-- Google Maps styling will be handled via JavaScript -->
    
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
        .icon-edit::before { content: "✏️"; }
        .icon-print::before { content: "🖨️"; }
        .icon-money::before { content: "💰"; }
        .icon-phone::before { content: "📞"; }
        .icon-location::before { content: "📍"; }
<<<<<<< HEAD
=======
        .icon-balance::before { content: "⚖️"; }
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
        
        /* Business Header */
        .business-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .business-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .business-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .business-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 32px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .business-details h1 {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin: 0 0 8px 0;
        }
        
        .business-details .account-number {
            font-size: 16px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .business-details .business-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .meta-item {
            font-size: 14px;
            color: #64748b;
        }
        
        .meta-item strong {
            color: #2d3748;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-suspended {
            background: #fef3c7;
            color: #92400e;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
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
        
        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.3);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(237, 137, 54, 0.3);
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 153, 225, 0.3);
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
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-icon {
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
        
        /* Details List */
        .details-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
        }
        
        .detail-value.amount {
            font-size: 18px;
            font-weight: bold;
        }
        
        .detail-value.positive {
            color: #dc2626;
        }
        
        .detail-value.zero {
            color: #059669;
        }
        
<<<<<<< HEAD
=======
        .detail-value.balance {
            color: #7c2d12;
            font-size: 20px;
            font-weight: bold;
        }
        
        .detail-value.balance.zero {
            color: #059669;
        }
        
>>>>>>> c9ccaba (Initial commit)
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }
        
<<<<<<< HEAD
=======
        .stat-card.danger {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }
        
>>>>>>> c9ccaba (Initial commit)
        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }
        
<<<<<<< HEAD
        /* Map Section */
        .map-container {
            height: 300px;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #e2e8f0;
            color: #64748b;
            font-size: 16px;
            margin-top: 15px;
        }
        
=======
        /* Map Styles */
        .map-container {
            height: 350px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            margin-top: 15px;
            position: relative;
            background: #f8fafc;
        }
        
        .map-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 16px;
            z-index: 1000;
            flex-direction: column;
            gap: 10px;
        }
        
        .map-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px dashed #f87171;
        }
        
        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .map-control-btn {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
            color: #374151;
            font-size: 14px;
        }
        
        .map-control-btn:hover {
            background: #f3f4f6;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* No Location State */
        .no-location {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 200px;
            color: #64748b;
            font-size: 16px;
            text-align: center;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            margin-top: 15px;
        }
        
        .no-location i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* Balance Highlight */
        .balance-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .balance-highlight.paid {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
        }
        
        .balance-highlight h4 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #92400e;
        }
        
        .balance-highlight.paid h4 {
            color: #065f46;
        }
        
        .balance-amount {
            font-size: 32px;
            font-weight: bold;
            color: #92400e;
            margin: 10px 0;
        }
        
        .balance-highlight.paid .balance-amount {
            color: #065f46;
        }
        
>>>>>>> c9ccaba (Initial commit)
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .data-table th {
            background: #f8fafc;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        
        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 14px;
        }
        
        .data-table tr:hover {
            background: #f8fafc;
        }
        
        .table-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #2d3748;
        }
        
        /* Alert */
        .alert {
            background: #d1fae5;
            border: 1px solid #9ae6b4;
            border-radius: 10px;
            padding: 15px;
            color: #065f46;
            margin-bottom: 20px;
        }
        
<<<<<<< HEAD
        /* Map Modal */
        .map-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        .map-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            text-align: center;
            animation: modalSlideIn 0.4s ease;
        }
        
        .location-info {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .map-btn {
            background: #667eea;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .map-btn:hover {
            background: #5a6fd8;
            color: white;
            transform: translateY(-1px);
        }
        
        .close-btn {
            background: #64748b;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .close-btn:hover {
            background: #475569;
=======
        /* Google Maps popup customization */
        .gm-style-iw {
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
        
        .gm-style-iw-d {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }
        
        .business-popup {
            text-align: center;
            min-width: 200px;
        }
        
        .business-popup h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-size: 16px;
        }
        
        .business-popup p {
            margin: 5px 0;
            color: #64748b;
            font-size: 14px;
        }
        
        /* Custom marker animations */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .custom-business-marker {
            animation: bounce 2s ease-in-out infinite;
>>>>>>> c9ccaba (Initial commit)
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
            
            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .business-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .business-details .business-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: stretch;
            }
            
            .action-buttons .btn {
                flex: 1;
            }
            
            .details-list {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
<<<<<<< HEAD
=======
            
            .map-container {
                height: 250px;
            }
>>>>>>> c9ccaba (Initial commit)
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
<<<<<<< HEAD
        @keyframes modalSlideIn {
            from { transform: translateY(-30px) scale(0.9); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        
=======
>>>>>>> c9ccaba (Initial commit)
        .content-card {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .content-card:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .content-card:nth-child(3) {
            animation-delay: 0.2s;
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
<<<<<<< HEAD
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
=======
                    display: inline-block;
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        <a href="../settings/index.php" class="dropdown-item">
=======
                        <a href="../users/view.php?id=<?php echo $currentUser['user_id']; ?>" class="dropdown-item">
>>>>>>> c9ccaba (Initial commit)
                            <i class="fas fa-user"></i>
                            <span class="icon-user" style="display: none;"></span>
                            My Profile
                        </a>
                        <a href="../settings/index.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span class="icon-cog" style="display: none;"></span>
                            Account Settings
                        </a>
<<<<<<< HEAD
                        <a href="../logs/user_activity.php" class="dropdown-item">
=======
                        <a href="../logs/audit_logs.php" class="dropdown-item">
>>>>>>> c9ccaba (Initial commit)
                            <i class="fas fa-history"></i>
                            <span class="icon-chart" style="display: none;"></span>
                            Activity Log
                        </a>
<<<<<<< HEAD
                        <a href="../docs/user_manual.md" class="dropdown-item">
                            <i class="fas fa-question-circle"></i>
                            <span class="icon-bell" style="display: none;"></span>
                            Help & Support
                        </a>
=======
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        <a href="../fee_structure/index.php" class="nav-link">
=======
                        <a href="../fee_structure/business_fees.php" class="nav-link">
>>>>>>> c9ccaba (Initial commit)
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
                    <span class="breadcrumb-current"><?php echo htmlspecialchars($business['business_name']); ?></span>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php 
            $flashMessages = getFlashMessages();
            if (!empty($flashMessages)): 
            ?>
                <?php foreach ($flashMessages as $message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?>">
                        <?php echo htmlspecialchars($message['message']); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Business Header -->
            <div class="business-header">
                <div class="header-content">
                    <div class="business-info">
                        <div class="business-avatar">
<<<<<<< HEAD
                            <?php echo strtoupper(substr($business['business_name'], 0, 1)); ?>
=======
                            <i class="fas fa-building"></i>
                            <span class="icon-building" style="display: none;"></span>
>>>>>>> c9ccaba (Initial commit)
                        </div>
                        <div class="business-details">
                            <h1><?php echo htmlspecialchars($business['business_name']); ?></h1>
                            <div class="account-number"><?php echo htmlspecialchars($business['account_number']); ?></div>
                            <div class="business-meta">
                                <div class="meta-item">
                                    <strong>Owner:</strong> <?php echo htmlspecialchars($business['owner_name']); ?>
                                </div>
                                <?php if ($business['telephone']): ?>
                                    <div class="meta-item">
                                        <strong>Phone:</strong> <?php echo htmlspecialchars($business['telephone']); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="meta-item">
                                    <strong>Zone:</strong> <?php echo htmlspecialchars($business['zone_name'] ?? 'Not assigned'); ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo strtolower($business['status']); ?>">
                                <?php echo htmlspecialchars($business['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="edit.php?id=<?php echo $business['business_id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                            <span class="icon-edit" style="display: none;"></span>
                            Edit Business
                        </a>
                        
<<<<<<< HEAD
                        <a href="../billing/generate.php?type=business&id=<?php echo $business['business_id']; ?>" class="btn btn-success">
=======
                        <a href="../billing/generate.php?business_id=<?php echo $business['business_id']; ?>" class="btn btn-success">
>>>>>>> c9ccaba (Initial commit)
                            <i class="fas fa-file-invoice"></i>
                            <span class="icon-invoice" style="display: none;"></span>
                            Generate Bill
                        </a>
                        
<<<<<<< HEAD
                        <a href="../payments/record.php?search=<?php echo urlencode($business['account_number']); ?>" class="btn btn-warning">
=======
                        <a href="../payments/record.php?account=<?php echo urlencode($business['account_number']); ?>" class="btn btn-warning">
>>>>>>> c9ccaba (Initial commit)
                            <i class="fas fa-credit-card"></i>
                            <span class="icon-credit" style="display: none;"></span>
                            Record Payment
                        </a>
                        
                        <?php if ($business['latitude'] && $business['longitude']): ?>
                            <button class="btn btn-info" onclick="showOnMap(<?php echo $business['latitude']; ?>, <?php echo $business['longitude']; ?>, '<?php echo htmlspecialchars($business['business_name']); ?>')">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="icon-location" style="display: none;"></span>
                                View on Map
                            </button>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_bills']); ?></div>
                    <div class="stat-label">Total Bills</div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_payments']); ?></div>
                    <div class="stat-label">Total Payments</div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-value">₵ <?php echo number_format($stats['total_paid'], 2); ?></div>
                    <div class="stat-label">Amount Paid</div>
                </div>

<<<<<<< HEAD
=======
                <div class="stat-card <?php echo $remainingBalance > 0 ? 'danger' : 'success'; ?>">
                    <div class="stat-icon">
                        <i class="fas fa-balance-scale"></i>
                        <span class="icon-balance" style="display: none;"></span>
                    </div>
                    <div class="stat-value">₵ <?php echo number_format($remainingBalance, 2); ?></div>
                    <div class="stat-label">Remaining Balance</div>
                </div>

>>>>>>> c9ccaba (Initial commit)
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">
                        <?php echo $stats['last_payment'] ? date('M d, Y', strtotime($stats['last_payment'])) : 'No payments'; ?>
                    </div>
                    <div class="stat-label">Last Payment</div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Left Column - Business Details -->
                <div>
                    <!-- Business Information -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                Business Information
                            </h3>
                        </div>
                        
                        <div class="details-list">
                            <div class="detail-item">
                                <div class="detail-label">Business Type</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['business_type']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Category</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['category']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Owner Name</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['owner_name']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Telephone</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['telephone'] ?: 'Not provided'); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Zone</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['zone_name'] ?: 'Not assigned'); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Sub-Zone</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['sub_zone_name'] ?: 'Not assigned'); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Batch</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['batch'] ?: 'Not assigned'); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Created By</div>
                                <div class="detail-value"><?php echo htmlspecialchars(($business['first_name'] ?? '') . ' ' . ($business['last_name'] ?? '')); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                Location Information
                            </h3>
                        </div>
                        
                        <div class="details-list">
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <div class="detail-label">Exact Location</div>
                                <div class="detail-value"><?php echo htmlspecialchars($business['exact_location'] ?: 'Not provided'); ?></div>
                            </div>
                            
                            <?php if ($business['latitude'] && $business['longitude']): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Latitude</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($business['latitude']); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Longitude</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($business['longitude']); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
<<<<<<< HEAD
                        <?php if ($business['latitude'] && $business['longitude']): ?>
                            <div class="map-container">
                                <i class="fas fa-map-marked-alt" style="font-size: 32px; margin-right: 10px;"></i>
                                <span>Click "View on Map" to see location</span>
=======
                        <!-- Map Container -->
                        <?php if ($business['latitude'] && $business['longitude']): ?>
                            <div class="map-container" id="businessMap">
                                <div class="map-loading" id="mapLoading">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px;"></i>
                                    <span>Loading interactive map...</span>
                                </div>
                                <div class="map-controls" style="display: none;" id="mapControls">
                                    <button class="map-control-btn" onclick="centerMap()" title="Center on Business">
                                        <i class="fas fa-crosshairs"></i>
                                    </button>
                                    <button class="map-control-btn" onclick="toggleMapType()" title="Toggle Map Type">
                                        <i class="fas fa-layer-group"></i>
                                    </button>
                                    <button class="map-control-btn" onclick="fullscreenMap()" title="Fullscreen">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>No Location Data</h3>
                                <p>GPS coordinates not available for this business</p>
>>>>>>> c9ccaba (Initial commit)
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Billing History -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                Billing History
                            </h3>
                        </div>
                        
                        <?php if (empty($bills)): ?>
                            <div class="empty-state">
                                <i class="fas fa-file-invoice"></i>
                                <h3>No Bills Generated</h3>
                                <p>No bills have been generated for this business yet.</p>
<<<<<<< HEAD
                                <a href="../billing/generate.php?type=business&id=<?php echo $business['business_id']; ?>" class="btn btn-primary" style="margin-top: 15px;">
=======
                                <a href="../billing/generate.php?business_id=<?php echo $business['business_id']; ?>" class="btn btn-primary" style="margin-top: 15px;">
>>>>>>> c9ccaba (Initial commit)
                                    <i class="fas fa-plus"></i> Generate First Bill
                                </a>
                            </div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Bill Number</th>
                                        <th>Year</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bills as $bill): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($bill['bill_number']); ?></td>
                                            <td><?php echo htmlspecialchars($bill['billing_year']); ?></td>
                                            <td>₵ <?php echo number_format($bill['amount_payable'], 2); ?></td>
                                            <td>
                                                <span class="table-badge <?php 
                                                    echo $bill['status'] === 'Paid' ? 'badge-success' : 
                                                        ($bill['status'] === 'Partially Paid' ? 'badge-warning' : 
                                                        ($bill['status'] === 'Overdue' ? 'badge-danger' : 'badge-info')); 
                                                ?>">
                                                    <?php echo htmlspecialchars($bill['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($bill['generated_at'])); ?></td>
                                            <td>
<<<<<<< HEAD
                                                <a href="../billing/view.php?id=<?php echo $bill['bill_id']; ?>" class="btn btn-sm btn-primary">
=======
                                                <a href="../billing/bill_preview.php?id=<?php echo $bill['bill_id']; ?>" class="btn btn-sm btn-primary">
>>>>>>> c9ccaba (Initial commit)
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column - Billing Summary & Actions -->
                <div>
                    <!-- Billing Summary -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                Billing Summary
                            </h3>
                        </div>
                        
                        <div class="details-list" style="grid-template-columns: 1fr;">
                            <div class="detail-item">
                                <div class="detail-label">Old Bill</div>
                                <div class="detail-value amount">₵ <?php echo number_format($business['old_bill'], 2); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Previous Payments</div>
                                <div class="detail-value amount">₵ <?php echo number_format($business['previous_payments'], 2); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Arrears</div>
                                <div class="detail-value amount <?php echo $business['arrears'] > 0 ? 'positive' : 'zero'; ?>">
                                    ₵ <?php echo number_format($business['arrears'], 2); ?>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Current Bill</div>
                                <div class="detail-value amount">₵ <?php echo number_format($business['current_bill'], 2); ?></div>
                            </div>
                            
                            <div style="border-top: 2px solid #e2e8f0; margin: 15px 0; padding-top: 15px;">
                                <div class="detail-item">
                                    <div class="detail-label">Amount Payable</div>
                                    <div class="detail-value amount <?php echo $business['amount_payable'] > 0 ? 'positive' : 'zero'; ?>" style="font-size: 24px;">
                                        ₵ <?php echo number_format($business['amount_payable'], 2); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
<<<<<<< HEAD
=======
                        <!-- Remaining Balance Highlight -->
                        <div class="balance-highlight <?php echo $remainingBalance <= 0 ? 'paid' : ''; ?>">
                            <h4>
                                <i class="fas fa-balance-scale"></i>
                                <span class="icon-balance" style="display: none;"></span>
                                <?php echo $remainingBalance <= 0 ? 'Account Fully Paid' : 'Outstanding Balance'; ?>
                            </h4>
                            <div class="balance-amount">
                                ₵ <?php echo number_format($remainingBalance, 2); ?>
                            </div>
                            <?php if ($remainingBalance > 0): ?>
                                <p style="margin: 10px 0 0 0; font-size: 14px; color: #92400e;">
                                    This amount needs to be paid
                                </p>
                            <?php else: ?>
                                <p style="margin: 10px 0 0 0; font-size: 14px; color: #065f46;">
                                    ✅ All bills have been settled
                                </p>
                            <?php endif; ?>
                        </div>
                        
>>>>>>> c9ccaba (Initial commit)
                        <?php if ($business['fee_structure_amount']): ?>
                            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-top: 15px;">
                                <div class="detail-label">Annual Fee Structure</div>
                                <div class="detail-value">
                                    ₵ <?php echo number_format($business['fee_structure_amount'], 2); ?> 
                                    per year
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment History -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                Recent Payments
                            </h3>
                        </div>
                        
                        <?php if (empty($payments)): ?>
                            <div class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <h3>No Payments Recorded</h3>
                                <p>No payments have been recorded for this business.</p>
<<<<<<< HEAD
                                <a href="../payments/record.php?search=<?php echo urlencode($business['account_number']); ?>" class="btn btn-warning" style="margin-top: 15px;">
=======
                                <a href="../payments/record.php?account=<?php echo urlencode($business['account_number']); ?>" class="btn btn-warning" style="margin-top: 15px;">
>>>>>>> c9ccaba (Initial commit)
                                    <i class="fas fa-plus"></i> Record Payment
                                </a>
                            </div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($payments, 0, 5) as $payment): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                                            <td>₵ <?php echo number_format($payment['amount_paid'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                            <td>
                                                <span class="table-badge <?php 
                                                    echo $payment['payment_status'] === 'Successful' ? 'badge-success' : 
                                                        ($payment['payment_status'] === 'Pending' ? 'badge-warning' : 'badge-danger'); 
                                                ?>">
                                                    <?php echo htmlspecialchars($payment['payment_status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <?php if (count($payments) > 5): ?>
                                <div style="text-align: center; margin-top: 15px;">
<<<<<<< HEAD
                                    <a href="../payments/index.php?search=<?php echo urlencode($business['account_number']); ?>" class="btn btn-secondary">
=======
                                    <a href="../payments/index.php?account=<?php echo urlencode($business['account_number']); ?>" class="btn btn-secondary">
>>>>>>> c9ccaba (Initial commit)
                                        View All Payments (<?php echo count($payments); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- System Information -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <div class="card-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                System Information
                            </h3>
                        </div>
                        
                        <div class="details-list" style="grid-template-columns: 1fr;">
                            <div class="detail-item">
                                <div class="detail-label">Created Date</div>
                                <div class="detail-value"><?php echo date('M d, Y g:i A', strtotime($business['created_at'])); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Last Updated</div>
                                <div class="detail-value"><?php echo date('M d, Y g:i A', strtotime($business['updated_at'])); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Created By</div>
                                <div class="detail-value"><?php echo htmlspecialchars(trim(($business['first_name'] ?? '') . ' ' . ($business['last_name'] ?? '')) ?: 'System'); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Business ID</div>
                                <div class="detail-value">#<?php echo $business['business_id']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <script>
=======
    <!-- Load Google Maps JavaScript API -->
    <!-- IMPORTANT: Replace YOUR_API_KEY with your actual Google Maps API key -->
    <!-- Get your API key from: https://developers.google.com/maps/documentation/javascript/get-api-key -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg1CWNtJ8BHeclYP7VfltZZLIcY3TVHaI&callback=initMap&libraries=geometry"></script>

    <script>
        // Global map variables
        let businessMap = null;
        let businessMarker = null;
        let currentMapType = 'roadmap';
        
        // Business coordinates from PHP
        const businessLat = <?php echo $business['latitude'] ? $business['latitude'] : 'null'; ?>;
        const businessLng = <?php echo $business['longitude'] ? $business['longitude'] : 'null'; ?>;
        const businessName = <?php echo json_encode($business['business_name']); ?>;
        const businessAccount = <?php echo json_encode($business['account_number']); ?>;
        const remainingBalance = <?php echo $remainingBalance; ?>;
        
        // Google Maps callback function
        window.initMap = function() {
            if (businessLat && businessLng) {
                initializeBusinessMap();
            }
        };
        
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
        });
=======
            
            // Initialize map if coordinates are available and Google Maps is loaded
            if (businessLat && businessLng && window.google && window.google.maps) {
                initializeBusinessMap();
            }
            
            // Display balance notification
            displayBalanceNotification();
        });
        
        function initializeBusinessMap() {
            try {
                console.log('Initializing Google Map for business coordinates:', businessLat, businessLng);
                
                // Hide loading indicator after a brief delay
                setTimeout(() => {
                    const loadingElement = document.getElementById('mapLoading');
                    if (loadingElement) {
                        loadingElement.style.display = 'none';
                    }
                    
                    const controlsElement = document.getElementById('mapControls');
                    if (controlsElement) {
                        controlsElement.style.display = 'flex';
                    }
                }, 1500);
                
                // Initialize the Google Map
                const mapOptions = {
                    center: { lat: businessLat, lng: businessLng },
                    zoom: 16,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    zoomControl: true,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.BOTTOM_RIGHT
                    },
                    streetViewControl: true,
                    streetViewControlOptions: {
                        position: google.maps.ControlPosition.BOTTOM_RIGHT
                    },
                    fullscreenControl: false, // We'll add our own
                    mapTypeControl: false // We'll add our own
                };
                
                businessMap = new google.maps.Map(document.getElementById('businessMap'), mapOptions);
                
                // Create custom business marker
                businessMarker = new google.maps.Marker({
                    position: { lat: businessLat, lng: businessLng },
                    map: businessMap,
                    title: `${businessName}`,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 20,
                        fillColor: '#667eea',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 3
                    },
                    animation: google.maps.Animation.BOUNCE
                });
                
                // Stop bouncing after 3 seconds
                setTimeout(() => {
                    businessMarker.setAnimation(null);
                }, 3000);
                
                // Create info window content
                const infoWindowContent = `
                    <div class="business-popup" style="min-width: 200px; text-align: center;">
                        <h4 style="margin: 0 0 10px 0; color: #2d3748; font-size: 16px;">🏢 ${businessName}</h4>
                        <p style="margin: 5px 0; color: #64748b; font-size: 14px;"><strong>Account:</strong> ${businessAccount}</p>
                        <p style="margin: 5px 0; color: #64748b; font-size: 14px;"><strong>Type:</strong> <?php echo htmlspecialchars($business['business_type']); ?></p>
                        <p style="margin: 5px 0; color: #64748b; font-size: 14px;"><strong>Category:</strong> <?php echo htmlspecialchars($business['category']); ?></p>
                        <p style="margin: 5px 0; font-size: 14px; ${remainingBalance > 0 ? 'color: #dc2626; font-weight: bold;' : 'color: #059669; font-weight: bold;'}">
                            <strong>Balance:</strong> ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                        </p>
                        <div style="background: #f8fafc; padding: 8px; border-radius: 6px; margin-top: 10px; font-family: monospace; font-size: 12px;">
                            <strong>Coordinates:</strong><br>
                            Lat: ${businessLat}<br>
                            Lng: ${businessLng}
                        </div>
                        <div style="margin-top: 10px;">
                            <a href="https://www.google.com/maps?q=${businessLat},${businessLng}" 
                               target="_blank" 
                               style="
                                   background: #667eea; 
                                   color: white; 
                                   padding: 6px 12px; 
                                   border-radius: 6px; 
                                   text-decoration: none; 
                                   font-size: 12px;
                                   display: inline-block;
                                   margin-top: 8px;
                               ">
                                🔗 Open in Google Maps
                            </a>
                        </div>
                    </div>
                `;
                
                const infoWindow = new google.maps.InfoWindow({
                    content: infoWindowContent
                });
                
                // Open info window by default
                infoWindow.open(businessMap, businessMarker);
                
                // Add click listener to marker
                businessMarker.addListener('click', () => {
                    infoWindow.open(businessMap, businessMarker);
                });
                
                // Add circle around business
                new google.maps.Circle({
                    strokeColor: '#667eea',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#667eea',
                    fillOpacity: 0.1,
                    map: businessMap,
                    center: { lat: businessLat, lng: businessLng },
                    radius: 50
                });
                
                // Add map click listener
                businessMap.addListener('click', (e) => {
                    const clickedLat = e.latLng.lat();
                    const clickedLng = e.latLng.lng();
                    
                    // Calculate distance using Google Maps geometry library
                    const distance = google.maps.geometry.spherical.computeDistanceBetween(
                        new google.maps.LatLng(businessLat, businessLng),
                        new google.maps.LatLng(clickedLat, clickedLng)
                    );
                    
                    const clickInfoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="text-align: center;">
                                <p><strong>📍 Clicked Location</strong></p>
                                <p>Lat: ${clickedLat.toFixed(6)}<br>
                                   Lng: ${clickedLng.toFixed(6)}</p>
                                <p style="margin-top: 10px; font-size: 12px; color: #666;">
                                    Distance from business: ${Math.round(distance)}m
                                </p>
                            </div>
                        `,
                        position: e.latLng
                    });
                    
                    clickInfoWindow.open(businessMap);
                });
                
                console.log('✅ Google Map initialized successfully');
                
            } catch (error) {
                console.error('❌ Error initializing map:', error);
                showMapError('Failed to load map. Please check your internet connection.');
            }
        }
        
        function displayBalanceNotification() {
            // Show balance status notification on page load
            if (remainingBalance > 0) {
                setTimeout(() => {
                    showNotification(`⚠️ Outstanding balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`, 'warning');
                }, 2000);
            } else {
                setTimeout(() => {
                    showNotification('✅ Account fully paid - No outstanding balance', 'success');
                }, 2000);
            }
        }
        
        function centerMap() {
            if (businessMap && businessLat && businessLng) {
                businessMap.setCenter({ lat: businessLat, lng: businessLng });
                businessMap.setZoom(16);
                if (businessMarker) {
                    // Bounce the marker briefly
                    businessMarker.setAnimation(google.maps.Animation.BOUNCE);
                    setTimeout(() => {
                        businessMarker.setAnimation(null);
                    }, 2000);
                }
            }
        }
        
        function toggleMapType() {
            if (!businessMap) return;
            
            if (currentMapType === 'roadmap') {
                businessMap.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                currentMapType = 'satellite';
            } else {
                businessMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                currentMapType = 'roadmap';
            }
        }
        
        function fullscreenMap() {
            if (!businessLat || !businessLng) return;
            
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.9); z-index: 10000;
                display: flex; align-items: center; justify-content: center;
                animation: fadeIn 0.3s ease; cursor: pointer;
            `;
            
            const mapContainer = document.createElement('div');
            mapContainer.style.cssText = `
                width: 95%; height: 90%; background: white;
                border-radius: 15px; overflow: hidden; position: relative;
                box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            `;
            
            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '❌ Close';
            closeBtn.style.cssText = `
                position: absolute; top: 20px; right: 20px; z-index: 1001;
                background: rgba(0,0,0,0.7); color: white; border: none;
                padding: 12px 20px; border-radius: 8px; cursor: pointer;
                font-weight: 600; backdrop-filter: blur(10px);
            `;
            
            const fullMapDiv = document.createElement('div');
            fullMapDiv.style.cssText = 'width: 100%; height: 100%;';
            fullMapDiv.id = 'fullscreenMap';
            
            mapContainer.appendChild(closeBtn);
            mapContainer.appendChild(fullMapDiv);
            modal.appendChild(mapContainer);
            document.body.appendChild(modal);
            
            // Initialize fullscreen map
            setTimeout(() => {
                const fullscreenMap = new google.maps.Map(fullMapDiv, {
                    center: { lat: businessLat, lng: businessLng },
                    zoom: 18,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                
                const fullscreenMarker = new google.maps.Marker({
                    position: { lat: businessLat, lng: businessLng },
                    map: fullscreenMap,
                    title: `${businessName}`,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 25,
                        fillColor: '#667eea',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 4
                    }
                });
                
                const fullscreenInfoWindow = new google.maps.InfoWindow({
                    content: `<h4>${businessName}</h4><p>Remaining Balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>`
                });
                
                fullscreenInfoWindow.open(fullscreenMap, fullscreenMarker);
            }, 100);
            
            closeBtn.onclick = () => modal.remove();
            modal.onclick = (e) => {
                if (e.target === modal) modal.remove();
            };
        }
        
        function showMapError(message) {
            const mapContainer = document.getElementById('businessMap');
            if (mapContainer) {
                mapContainer.innerHTML = `
                    <div class="map-loading map-error">
                        <i class="fas fa-exclamation-triangle" style="font-size: 32px; margin-bottom: 10px;"></i>
                        <span>⚠️</span>
                        <br>
                        ${message}
                        <br><br>
                        <button onclick="initializeBusinessMap()" style="
                            background: #667eea; 
                            color: white; 
                            border: none; 
                            padding: 8px 16px; 
                            border-radius: 6px; 
                            cursor: pointer;
                        ">
                            🔄 Try Again
                        </button>
                    </div>
                `;
            }
        }
>>>>>>> c9ccaba (Initial commit)

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

        function showOnMap(lat, lng, name) {
            // Create enhanced map modal
            const mapModal = document.createElement('div');
<<<<<<< HEAD
            mapModal.className = 'map-modal';
            
            const mapContent = document.createElement('div');
            mapContent.className = 'map-content';
            
            mapContent.innerHTML = `
                <h3 style="margin: 0 0 20px 0; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-map-marker-alt" style="color: #667eea;"></i>
                    Business Location
                </h3>
                <div class="location-info">
=======
            mapModal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 10000;
                display: flex; align-items: center; justify-content: center;
                animation: fadeIn 0.3s ease; cursor: pointer;
            `;
            
            const mapContent = document.createElement('div');
            mapContent.style.cssText = `
                background: white; padding: 30px; border-radius: 15px; max-width: 500px; width: 90%;
                box-shadow: 0 25px 80px rgba(0,0,0,0.4); text-align: center;
                animation: modalSlideIn 0.4s ease; cursor: default;
            `;
            
            mapContent.innerHTML = `
                <h3 style="margin: 0 0 20px 0; color: #2d3748; display: flex; align-items: center; gap: 10px; justify-content: center;">
                    <i class="fas fa-map-marker-alt" style="color: #667eea;"></i>
                    📍 Business Location
                </h3>
                <div style="background: #f8fafc; padding: 20px; border-radius: 10px; margin: 20px 0;">
>>>>>>> c9ccaba (Initial commit)
                    <h4 style="margin: 0 0 15px 0; color: #667eea; font-size: 18px;">${name}</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left;">
                        <div>
                            <strong style="color: #2d3748;">Latitude:</strong>
                            <div style="color: #64748b; font-family: monospace; background: #f1f5f9; padding: 5px 8px; border-radius: 4px; margin-top: 3px;">${lat}</div>
                        </div>
                        <div>
                            <strong style="color: #2d3748;">Longitude:</strong>
                            <div style="color: #64748b; font-family: monospace; background: #f1f5f9; padding: 5px 8px; border-radius: 4px; margin-top: 3px;">${lng}</div>
                        </div>
                    </div>
<<<<<<< HEAD
                </div>
                <div class="modal-actions">
                    <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="map-btn">
                        <i class="fas fa-external-link-alt"></i> Open in Google Maps
                    </a>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="map-btn">
                        <i class="fas fa-route"></i> Get Directions
                    </a>
                    <button onclick="this.closest('.map-modal').remove()" class="close-btn">
                        <i class="fas fa-times"></i> Close
=======
                    <div style="margin-top: 15px; padding: 10px; border-radius: 8px; ${remainingBalance > 0 ? 'background: #fef3c7; color: #92400e;' : 'background: #d1fae5; color: #065f46;'}">
                        <strong>Remaining Balance:</strong> ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                    </div>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" style="
                        background: #667eea; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                        text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-flex;
                        align-items: center; gap: 8px; font-size: 14px;">
                        <i class="fas fa-external-link-alt"></i> 🔗 Open in Google Maps
                    </a>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" style="
                        background: #667eea; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                        text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-flex;
                        align-items: center; gap: 8px; font-size: 14px;">
                        <i class="fas fa-route"></i> 🗺️ Get Directions
                    </a>
                    <button onclick="this.closest('.map-modal').remove()" style="
                        background: #64748b; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                        cursor: pointer; font-weight: 600; transition: all 0.3s; display: inline-flex;
                        align-items: center; gap: 8px; font-size: 14px;">
                        <i class="fas fa-times"></i> ❌ Close
>>>>>>> c9ccaba (Initial commit)
                    </button>
                </div>
            `;
            
<<<<<<< HEAD
=======
            mapModal.className = 'map-modal';
>>>>>>> c9ccaba (Initial commit)
            mapModal.appendChild(mapContent);
            document.body.appendChild(mapModal);
            
            // Close modal when clicking backdrop
            mapModal.addEventListener('click', function(e) {
                if (e.target === mapModal) {
                    mapModal.remove();
                }
            });
        }

        // Mobile responsiveness
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('hidden');
            }
<<<<<<< HEAD
        });

=======
            
            // Resize map if it exists (Google Maps)
            if (businessMap) {
                setTimeout(() => {
                    google.maps.event.trigger(businessMap, 'resize');
                    if (businessLat && businessLng) {
                        businessMap.setCenter({ lat: businessLat, lng: businessLng });
                    }
                }, 300);
            }
        });

        // Print functionality
        function printBusinessDetails() {
            const printWindow = window.open('', '_blank');
            const businessData = {
                name: <?php echo json_encode($business['business_name']); ?>,
                account: <?php echo json_encode($business['account_number']); ?>,
                type: <?php echo json_encode($business['business_type']); ?>,
                category: <?php echo json_encode($business['category']); ?>,
                owner: <?php echo json_encode($business['owner_name']); ?>,
                location: <?php echo json_encode($business['exact_location']); ?>,
                amountPayable: <?php echo json_encode($business['amount_payable']); ?>,
                remainingBalance: remainingBalance
            };
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>Business Details - ${businessData.name}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; border-bottom: 2px solid #667eea; padding-bottom: 20px; margin-bottom: 30px; }
                        .detail-row { display: flex; margin-bottom: 10px; }
                        .label { font-weight: bold; width: 150px; }
                        .value { flex: 1; }
                        .amount { font-size: 24px; color: #dc2626; font-weight: bold; }
                        .balance { font-size: 28px; color: ${businessData.remainingBalance > 0 ? '#dc2626' : '#059669'}; font-weight: bold; }
                        .balance-section { border: 2px solid ${businessData.remainingBalance > 0 ? '#fbbf24' : '#10b981'}; 
                                         background: ${businessData.remainingBalance > 0 ? '#fef3c7' : '#d1fae5'}; 
                                         padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>🏢 Business Details</h1>
                        <h2>${businessData.name}</h2>
                        <p>Account Number: ${businessData.account}</p>
                    </div>
                    <div class="details">
                        <div class="detail-row">
                            <div class="label">Business Name:</div>
                            <div class="value">${businessData.name}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Account Number:</div>
                            <div class="value">${businessData.account}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Business Type:</div>
                            <div class="value">${businessData.type}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Category:</div>
                            <div class="value">${businessData.category}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Owner Name:</div>
                            <div class="value">${businessData.owner}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Location:</div>
                            <div class="value">${businessData.location || 'Not provided'}</div>
                        </div>
                        <div class="detail-row" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
                            <div class="label">Amount Payable:</div>
                            <div class="value amount">₵ ${parseFloat(businessData.amountPayable).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                        </div>
                    </div>
                    <div class="balance-section">
                        <h3>${businessData.remainingBalance > 0 ? '⚠️ Outstanding Balance' : '✅ Account Status'}</h3>
                        <div class="balance">₵ ${businessData.remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                        <p>${businessData.remainingBalance > 0 ? 'This amount needs to be paid' : 'Account fully paid'}</p>
                    </div>
                    <div style="margin-top: 50px; text-align: center; color: #64748b; font-size: 14px;">
                        Generated on ${new Date().toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        // Export functionality
        function exportBusinessData() {
            const businessData = {
                account_number: <?php echo json_encode($business['account_number']); ?>,
                business_name: <?php echo json_encode($business['business_name']); ?>,
                owner_name: <?php echo json_encode($business['owner_name']); ?>,
                business_type: <?php echo json_encode($business['business_type']); ?>,
                category: <?php echo json_encode($business['category']); ?>,
                telephone: <?php echo json_encode($business['telephone']); ?>,
                exact_location: <?php echo json_encode($business['exact_location']); ?>,
                latitude: <?php echo json_encode($business['latitude']); ?>,
                longitude: <?php echo json_encode($business['longitude']); ?>,
                old_bill: <?php echo json_encode($business['old_bill']); ?>,
                previous_payments: <?php echo json_encode($business['previous_payments']); ?>,
                arrears: <?php echo json_encode($business['arrears']); ?>,
                current_bill: <?php echo json_encode($business['current_bill']); ?>,
                amount_payable: <?php echo json_encode($business['amount_payable']); ?>,
                remaining_balance: remainingBalance,
                status: <?php echo json_encode($business['status']); ?>,
                zone_name: <?php echo json_encode($business['zone_name']); ?>,
                sub_zone_name: <?php echo json_encode($business['sub_zone_name']); ?>,
                created_at: <?php echo json_encode($business['created_at']); ?>,
                updated_at: <?php echo json_encode($business['updated_at']); ?>
            };
            
            // Convert to CSV
            const csvContent = Object.keys(businessData).map(key => 
                `"${key}","${businessData[key] || ''}"`
            ).join('\n');
            
            const csvHeader = '"Field","Value"\n';
            const fullCsv = csvHeader + csvContent;
            
            // Create and download file
            const blob = new Blob([fullCsv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `business_${businessData.account_number}_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Search functionality
        function searchInPage() {
            const searchTerm = prompt('🔍 Search in business details:');
            if (searchTerm && searchTerm.trim()) {
                const content = document.body.innerText.toLowerCase();
                if (content.includes(searchTerm.toLowerCase())) {
                    // Highlight search term (basic implementation)
                    if (window.find) {
                        window.find(searchTerm, false, false, true);
                    } else if (document.body.createTextRange) {
                        const textRange = document.body.createTextRange();
                        textRange.findText(searchTerm);
                        textRange.select();
                    }
                    
                    showNotification('✅ Search term found!', 'success');
                } else {
                    showNotification('❌ Search term not found.', 'error');
                }
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed; top: 100px; right: 20px; z-index: 10001;
                background: ${type === 'success' ? '#d1fae5' : type === 'error' ? '#fee2e2' : type === 'warning' ? '#fef3c7' : '#dbeafe'};
                color: ${type === 'success' ? '#065f46' : type === 'error' ? '#991b1b' : type === 'warning' ? '#92400e' : '#1e40af'};
                border: 1px solid ${type === 'success' ? '#9ae6b4' : type === 'error' ? '#f87171' : type === 'warning' ? '#fbbf24' : '#93c5fd'};
                border-radius: 10px; padding: 15px 20px; max-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1); font-weight: 500;
                animation: slideInRight 0.3s ease, slideOutRight 0.3s ease 2.7s forwards;
            `;
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Add animations if not already present
            if (!document.getElementById('notificationAnimations')) {
                const style = document.createElement('style');
                style.id = 'notificationAnimations';
                style.textContent = `
                    @keyframes slideInRight { 
                        from { transform: translateX(100%); opacity: 0; } 
                        to { transform: translateX(0); opacity: 1; } 
                    }
                    @keyframes slideOutRight { 
                        from { transform: translateX(0); opacity: 1; } 
                        to { transform: translateX(100%); opacity: 0; } 
                    }
                `;
                document.head.appendChild(style);
            }
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printBusinessDetails();
            }
            
            // Ctrl/Cmd + E for export
            if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
                e.preventDefault();
                exportBusinessData();
            }
            
            // Ctrl/Cmd + F for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInPage();
            }
            
            // Ctrl/Cmd + B for balance info
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                showBalanceDetails();
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.map-modal, .user-dropdown.show');
                modals.forEach(modal => {
                    if (modal.classList && modal.classList.contains('map-modal')) {
                        modal.remove();
                    } else if (modal.classList && modal.classList.contains('show')) {
                        modal.classList.remove('show');
                    }
                });
            }
        });

        // Show detailed balance information
        function showBalanceDetails() {
            const balanceModal = document.createElement('div');
            balanceModal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 10000;
                display: flex; align-items: center; justify-content: center;
                animation: fadeIn 0.3s ease; cursor: pointer;
            `;
            
            const balanceContent = document.createElement('div');
            balanceContent.style.cssText = `
                background: white; padding: 30px; border-radius: 15px; max-width: 600px; width: 90%;
                box-shadow: 0 25px 80px rgba(0,0,0,0.4); text-align: center;
                animation: modalSlideIn 0.4s ease; cursor: default;
            `;
            
            const amountPayable = <?php echo $business['amount_payable']; ?>;
            const totalPaid = <?php echo $stats['total_paid']; ?>;
            const paymentProgress = amountPayable > 0 ? (totalPaid / amountPayable) * 100 : 100;
            
            balanceContent.innerHTML = `
                <h3 style="margin: 0 0 20px 0; color: #2d3748; display: flex; align-items: center; gap: 10px; justify-content: center;">
                    <i class="fas fa-balance-scale" style="color: #667eea;"></i>
                    ⚖️ Balance Analysis
                </h3>
                <div style="background: #f8fafc; padding: 25px; border-radius: 12px; margin: 20px 0;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div style="text-align: left;">
                            <strong style="color: #2d3748;">Total Amount Payable:</strong>
                            <div style="font-size: 24px; font-weight: bold; color: #667eea; margin-top: 5px;">
                                ₵ ${amountPayable.toLocaleString('en-US', {minimumFractionDigits: 2})}
                            </div>
                        </div>
                        <div style="text-align: left;">
                            <strong style="color: #2d3748;">Total Paid:</strong>
                            <div style="font-size: 24px; font-weight: bold; color: #059669; margin-top: 5px;">
                                ₵ ${totalPaid.toLocaleString('en-US', {minimumFractionDigits: 2})}
                            </div>
                        </div>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <strong style="color: #2d3748;">Payment Progress:</strong>
                        <div style="background: #e2e8f0; height: 12px; border-radius: 6px; margin: 10px 0; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #10b981, #059669); height: 100%; width: ${Math.min(paymentProgress, 100)}%; transition: width 1s ease;"></div>
                        </div>
                        <div style="font-size: 14px; color: #64748b;">${paymentProgress.toFixed(1)}% Completed</div>
                    </div>
                    <div style="border: 3px solid ${remainingBalance > 0 ? '#f59e0b' : '#10b981'}; 
                                background: ${remainingBalance > 0 ? '#fef3c7' : '#d1fae5'}; 
                                padding: 20px; border-radius: 12px; margin-top: 20px;">
                        <h4 style="margin: 0 0 10px 0; color: ${remainingBalance > 0 ? '#92400e' : '#065f46'};">
                            ${remainingBalance > 0 ? '⚠️ Outstanding Balance' : '✅ Account Status'}
                        </h4>
                        <div style="font-size: 36px; font-weight: bold; color: ${remainingBalance > 0 ? '#92400e' : '#065f46'}; margin: 15px 0;">
                            ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                        </div>
                        <p style="margin: 10px 0 0 0; color: ${remainingBalance > 0 ? '#92400e' : '#065f46'};">
                            ${remainingBalance > 0 ? 'This amount needs to be paid to clear the account' : 'All bills have been fully settled'}
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 25px;">
                    ${remainingBalance > 0 ? `
                        <a href="../payments/record.php?account=${encodeURIComponent(<?php echo json_encode($business['account_number']); ?>)}" style="
                            background: #10b981; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                            text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-flex;
                            align-items: center; gap: 8px; font-size: 14px;">
                            <i class="fas fa-credit-card"></i> 💳 Record Payment
                        </a>
                        <a href="../billing/generate.php?business_id=<?php echo $business['business_id']; ?>" style="
                            background: #667eea; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                            text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-flex;
                            align-items: center; gap: 8px; font-size: 14px;">
                            <i class="fas fa-file-invoice"></i> 📄 Generate New Bill
                        </a>
                    ` : `
                        <a href="../billing/generate.php?business_id=<?php echo $business['business_id']; ?>" style="
                            background: #667eea; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                            text-decoration: none; font-weight: 600; transition: all 0.3s; display: inline-flex;
                            align-items: center; gap: 8px; font-size: 14px;">
                            <i class="fas fa-file-invoice"></i> 📄 Generate New Bill
                        </a>
                    `}
                    <button onclick="printBusinessDetails()" style="
                        background: #64748b; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                        cursor: pointer; font-weight: 600; transition: all 0.3s; display: inline-flex;
                        align-items: center; gap: 8px; font-size: 14px;">
                        <i class="fas fa-print"></i> 🖨️ Print Details
                    </button>
                    <button onclick="this.closest('.balance-modal').remove()" style="
                        background: #94a3b8; color: white; padding: 12px 20px; border: none; border-radius: 8px;
                        cursor: pointer; font-weight: 600; transition: all 0.3s; display: inline-flex;
                        align-items: center; gap: 8px; font-size: 14px;">
                        <i class="fas fa-times"></i> ❌ Close
                    </button>
                </div>
            `;
            
            balanceModal.className = 'balance-modal';
            balanceModal.appendChild(balanceContent);
            document.body.appendChild(balanceModal);
            
            // Close modal when clicking backdrop
            balanceModal.addEventListener('click', function(e) {
                if (e.target === balanceModal) {
                    balanceModal.remove();
                }
            });
        }

        // Add tooltips for action buttons
        function addTooltips() {
            const tooltips = {
                'Edit Business': 'Modify business details and information',
                'Generate Bill': 'Create a new bill for this business',
                'Record Payment': 'Add a payment record for this business',
                'View on Map': 'Show business location on interactive map',
                'Back to List': 'Return to businesses listing page'
            };
            
            document.querySelectorAll('.btn').forEach(btn => {
                const text = btn.textContent.trim();
                if (tooltips[text]) {
                    btn.title = tooltips[text];
                }
            });
        }

        // Initialize tooltips when DOM is ready
        document.addEventListener('DOMContentLoaded', addTooltips);

        // Add right-click context menu for additional actions
        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.business-header')) {
                e.preventDefault();
                showContextMenu(e.pageX, e.pageY);
            }
        });

        function showContextMenu(x, y) {
            // Remove existing context menu
            const existingMenu = document.getElementById('contextMenu');
            if (existingMenu) {
                existingMenu.remove();
            }
            
            const contextMenu = document.createElement('div');
            contextMenu.id = 'contextMenu';
            contextMenu.style.cssText = `
                position: absolute; top: ${y}px; left: ${x}px; z-index: 10002;
                background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border: 1px solid #e2e8f0; min-width: 180px; overflow: hidden;
                animation: fadeIn 0.2s ease;
            `;
            
            const menuItems = [
                { text: '🖨️ Print Details', action: printBusinessDetails },
                { text: '📥 Export Data', action: exportBusinessData },
                { text: '⚖️ Balance Details', action: showBalanceDetails },
                { text: '🔍 Search', action: searchInPage },
                { text: '🗂️ Copy Account Number', action: () => {
                    navigator.clipboard.writeText(<?php echo json_encode($business['account_number']); ?>);
                    showNotification('📋 Account number copied!', 'success');
                }},
                { text: '📧 Share Location', action: () => {
                    if (businessLat && businessLng) {
                        const shareText = `📍 ${businessName} Location: https://www.google.com/maps?q=${businessLat},${businessLng}\n💰 Remaining Balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        if (navigator.share) {
                            navigator.share({ title: 'Business Location & Balance', text: shareText });
                        } else {
                            navigator.clipboard.writeText(shareText);
                            showNotification('📋 Location & balance info copied!', 'success');
                        }
                    } else {
                        showNotification('❌ No location coordinates available', 'error');
                    }
                }}
            ];
            
            menuItems.forEach((item, index) => {
                const menuItem = document.createElement('div');
                menuItem.style.cssText = `
                    padding: 12px 16px; cursor: pointer; font-size: 14px;
                    border-bottom: ${index < menuItems.length - 1 ? '1px solid #f1f5f9' : 'none'};
                    transition: background 0.2s; color: #2d3748;
                `;
                menuItem.textContent = item.text;
                
                menuItem.addEventListener('mouseenter', function() {
                    this.style.background = '#f8fafc';
                });
                
                menuItem.addEventListener('mouseleave', function() {
                    this.style.background = 'white';
                });
                
                menuItem.addEventListener('click', function() {
                    item.action();
                    contextMenu.remove();
                });
                
                contextMenu.appendChild(menuItem);
            });
            
            document.body.appendChild(contextMenu);
            
            // Close context menu when clicking elsewhere
            setTimeout(() => {
                document.addEventListener('click', function closeContextMenu() {
                    contextMenu.remove();
                    document.removeEventListener('click', closeContextMenu);
                });
            }, 100);
        }

>>>>>>> c9ccaba (Initial commit)
        // Session timeout check
        let lastActivity = <?php echo $_SESSION['LAST_ACTIVITY']; ?>;
        const SESSION_TIMEOUT = 1800; // 30 minutes in seconds

        function checkSessionTimeout() {
            const currentTime = Math.floor(Date.now() / 1000);
            if (currentTime - lastActivity > SESSION_TIMEOUT) {
<<<<<<< HEAD
                window.location.href = '../../index.php';
=======
                showNotification('🔒 Session expired. Redirecting to login...', 'error');
                setTimeout(() => {
                    window.location.href = '../../index.php';
                }, 2000);
>>>>>>> c9ccaba (Initial commit)
            }
        }

        // Check session every minute
        setInterval(checkSessionTimeout, 60000);
<<<<<<< HEAD
=======

        // Update last activity on user interaction
        document.addEventListener('click', () => {
            lastActivity = Math.floor(Date.now() / 1000);
        });

        // Performance monitoring for Google Maps loading
        if (businessLat && businessLng) {
            console.log('🗺️ Google Maps initialization started for coordinates:', businessLat, businessLng);
            
            // Monitor map load time
            const mapStartTime = performance.now();
            window.addEventListener('load', function() {
                const mapLoadTime = performance.now() - mapStartTime;
                console.log(`⏱️ Total page load time: ${Math.round(mapLoadTime)}ms`);
                
                if (mapLoadTime > 3000) {
                    console.warn('⚠️ Google Maps loading took longer than expected. Consider optimizing.');
                }
            });
        }

        // Balance monitoring and alerts
        function checkBalanceAlerts() {
            const alerts = [];
            
            if (remainingBalance > 0) {
                if (remainingBalance > 1000) {
                    alerts.push({
                        type: 'danger',
                        message: `High outstanding balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`
                    });
                } else if (remainingBalance > 500) {
                    alerts.push({
                        type: 'warning', 
                        message: `Moderate outstanding balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`
                    });
                } else {
                    alerts.push({
                        type: 'info',
                        message: `Low outstanding balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`
                    });
                }
            }
            
            return alerts;
        }

        // Display balance alerts on page load
        setTimeout(() => {
            const alerts = checkBalanceAlerts();
            alerts.forEach((alert, index) => {
                setTimeout(() => {
                    showNotification(alert.message, alert.type);
                }, index * 1000);
            });
        }, 3000);

        console.log('✅ Business profile page initialized successfully');
        console.log(`💰 Remaining Balance: ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`);
>>>>>>> c9ccaba (Initial commit)
    </script>
</body>
</html>