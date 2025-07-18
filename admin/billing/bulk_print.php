 <?php
/**
 * Billing Management - Bulk Print Bills
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
if (!hasPermission('billing.print')) {
    setFlashMessage('error', 'Access denied. You do not have permission to print bills.');
    header('Location: index.php');
    exit();
}

$pageTitle = 'Bulk Print Bills';
$currentUser = getCurrentUser();

// Initialize variables
$errors = [];
$selectedBillIds = [];
$bills = [];
$printStats = [];
$zones = [];
$businessTypes = [];

// Get filter options
$filterType = sanitizeInput($_GET['type'] ?? $_POST['filter_type'] ?? '');
$filterZone = intval($_GET['zone_id'] ?? $_POST['filter_zone'] ?? 0);
$filterBusinessType = sanitizeInput($_GET['business_type'] ?? $_POST['filter_business_type'] ?? '');
$filterStatus = sanitizeInput($_GET['status'] ?? $_POST['filter_status'] ?? '');
$filterYear = intval($_GET['year'] ?? $_POST['filter_year'] ?? 0);

try {
    $db = new Database();
    
    // Get zones for filter
    $zones = $db->fetchAll("SELECT * FROM zones ORDER BY zone_name");
    
    // Get business types for filter
    $businessTypes = $db->fetchAll("
        SELECT DISTINCT business_type 
        FROM business_fee_structure 
        WHERE is_active = 1 
        ORDER BY business_type
    ");
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = sanitizeInput($_POST['action']);
            
            if ($action === 'preview_selected' && isset($_POST['bill_ids'])) {
                // Handle bills selected from list page
                $selectedBillIds = array_map('intval', $_POST['bill_ids']);
                $selectedBillIds = array_filter($selectedBillIds, function($id) { return $id > 0; });
                
            } elseif ($action === 'preview_filtered') {
                // Handle bulk selection based on filters
                $whereConditions = [];
                $params = [];
                
                // Bill type filter
                if (!empty($filterType)) {
                    $whereConditions[] = "b.bill_type = ?";
                    $params[] = $filterType;
                }
                
                // Zone filter
                if ($filterZone > 0) {
                    $whereConditions[] = "(
                        (b.bill_type = 'Business' AND bs.zone_id = ?) OR
                        (b.bill_type = 'Property' AND pr.zone_id = ?)
                    )";
                    $params[] = $filterZone;
                    $params[] = $filterZone;
                }
                
                // Business type filter
                if (!empty($filterBusinessType)) {
                    $whereConditions[] = "b.bill_type = 'Business' AND bs.business_type = ?";
                    $params[] = $filterBusinessType;
                }
                
                // Status filter
                if (!empty($filterStatus)) {
                    $whereConditions[] = "b.status = ?";
                    $params[] = $filterStatus;
                }
                
                // Year filter
                if ($filterYear > 0) {
                    $whereConditions[] = "b.billing_year = ?";
                    $params[] = $filterYear;
                }
                
                $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);
                
                // Get bill IDs based on filters
                $billIdsQuery = "
                    SELECT b.bill_id
                    FROM bills b
                    LEFT JOIN businesses bs ON b.bill_type = 'Business' AND b.reference_id = bs.business_id
                    LEFT JOIN properties pr ON b.bill_type = 'Property' AND b.reference_id = pr.property_id
                    {$whereClause}
                    ORDER BY b.bill_number
                ";
                
                $billIdsResult = $db->fetchAll($billIdsQuery, $params);
                $selectedBillIds = array_column($billIdsResult, 'bill_id');
            }
        }
    }
    
    // Get bill details if we have selected IDs
    if (!empty($selectedBillIds)) {
        $placeholders = str_repeat('?,', count($selectedBillIds) - 1) . '?';
        
        $billsQuery = "
            SELECT b.*,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bs.business_name
                       WHEN b.bill_type = 'Property' THEN pr.owner_name
                   END as payer_name,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bs.account_number
                       WHEN b.bill_type = 'Property' THEN pr.property_number
                   END as account_number,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bs.owner_name
                       WHEN b.bill_type = 'Property' THEN pr.owner_name
                   END as owner_name,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bs.telephone
                       WHEN b.bill_type = 'Property' THEN pr.telephone
                   END as telephone,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bs.exact_location
                       WHEN b.bill_type = 'Property' THEN pr.location
                   END as location,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN z1.zone_name
                       WHEN b.bill_type = 'Property' THEN z2.zone_name
                   END as zone_name,
                   -- Business specific fields
                   bs.business_type,
                   bs.category,
                   -- Property specific fields
                   pr.structure,
                   pr.property_use,
                   pr.number_of_rooms
            FROM bills b
            LEFT JOIN businesses bs ON b.bill_type = 'Business' AND b.reference_id = bs.business_id
            LEFT JOIN properties pr ON b.bill_type = 'Property' AND b.reference_id = pr.property_id
            LEFT JOIN zones z1 ON bs.zone_id = z1.zone_id
            LEFT JOIN zones z2 ON pr.zone_id = z2.zone_id
            WHERE b.bill_id IN ({$placeholders})
            ORDER BY b.bill_type, b.bill_number
        ";
        
        $bills = $db->fetchAll($billsQuery, $selectedBillIds);
        
        // Calculate print statistics
        $printStats = [
            'total_bills' => count($bills),
            'business_bills' => count(array_filter($bills, function($bill) { return $bill['bill_type'] === 'Business'; })),
            'property_bills' => count(array_filter($bills, function($bill) { return $bill['bill_type'] === 'Property'; })),
            'total_amount' => array_sum(array_column($bills, 'amount_payable')),
            'pending_bills' => count(array_filter($bills, function($bill) { return $bill['status'] === 'Pending'; })),
            'paid_bills' => count(array_filter($bills, function($bill) { return $bill['status'] === 'Paid'; })),
            'overdue_bills' => count(array_filter($bills, function($bill) { return in_array($bill['status'], ['Overdue', 'Partially Paid']); }))
        ];
    }
    
} catch (Exception $e) {
    writeLog("Bulk print error: " . $e->getMessage(), 'ERROR');
    $errors[] = 'An error occurred while loading bills for printing.';
}

// Get flash messages
$flashMessages = getFlashMessages();
$flashMessage = !empty($flashMessages) ? $flashMessages[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Icons and CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            color: #64748b;
            margin-bottom: 20px;
        }
        
        .breadcrumb a {
            color: #10b981;
            text-decoration: none;
        }
        
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
        
        .errors {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .errors ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .errors li {
            color: #991b1b;
            margin-bottom: 5px;
        }
        
        .errors li:before {
            content: "❌ ";
            margin-right: 5px;
        }
        
        /* Selection Methods */
        .selection-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .selection-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
            border: 3px solid transparent;
        }
        
        .selection-card:hover {
            transform: translateY(-2px);
            border-color: #10b981;
        }
        
        .selection-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 36px;
        }
        
        .selection-title {
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .selection-description {
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        /* Filter Form */
        .filter-form {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
        }
        
        .form-input {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        /* Print Statistics */
        .stats-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        .stats-content {
            position: relative;
            z-index: 2;
        }
        
        .stats-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Bills Preview */
        .preview-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .preview-header {
            background: #f8fafc;
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .preview-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .preview-actions {
            display: flex;
            gap: 10px;
        }
        
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .preview-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .preview-table th {
            background: #f8fafc;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .preview-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 14px;
        }
        
        .preview-table tr:hover {
            background: #f8fafc;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partially-paid { background: #dbeafe; color: #1e40af; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        
        .type-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .type-business { background: #dbeafe; color: #1e40af; }
        .type-property { background: #d1fae5; color: #065f46; }
        
        .amount {
            font-weight: bold;
            font-family: monospace;
            color: #10b981;
        }
        
        .bill-number {
            font-family: monospace;
            font-weight: 600;
            color: #2d3748;
        }
        
        /* Buttons */
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #475569;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            color: #10b981;
            border: 2px solid #10b981;
        }
        
        .btn-outline:hover {
            background: #10b981;
            color: white;
        }
        
        .btn-success {
            background: #059669;
            color: white;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-lg {
            padding: 15px 30px;
            font-size: 16px;
        }
        
        /* Print Options */
        .print-options {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .option-group {
            margin-bottom: 20px;
        }
        
        .option-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .checkbox-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #2d3748;
            font-size: 24px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .selection-cards {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .preview-actions {
                flex-direction: column;
            }
        }
        
        /* Animations */
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Print styles */
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="breadcrumb">
                <a href="../index.php">Dashboard</a> / 
                <a href="index.php">Billing</a> / 
                Bulk Print Bills
            </div>
            <h1 class="page-title">
                <i class="fas fa-print"></i>
                Bulk Print Bills
            </h1>
            <p style="color: #64748b;">Print multiple bills at once with customizable options</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                <i class="fas fa-<?php echo $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <div><?php echo htmlspecialchars($flashMessage['message']); ?></div>
            </div>
        <?php endif; ?>

        <!-- Errors -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (empty($bills)): ?>
            <!-- Selection Methods -->
            <div class="selection-cards">
                <!-- Pre-selected Bills -->
                <div class="selection-card">
                    <div class="selection-icon">
                        <i class="fas fa-check-square"></i>
                    </div>
                    <div class="selection-title">Selected Bills</div>
                    <div class="selection-description">
                        Print bills that were pre-selected from the bills list page. Perfect for printing specific bills.
                    </div>
                    <div style="font-size: 14px; color: #64748b; margin-bottom: 20px;">
                        <?php if (isset($_POST['bill_ids']) && !empty($_POST['bill_ids'])): ?>
                            <i class="fas fa-info-circle"></i>
                            <?php echo count($_POST['bill_ids']); ?> bills selected
                        <?php else: ?>
                            <i class="fas fa-exclamation-triangle"></i>
                            No bills currently selected
                        <?php endif; ?>
                    </div>
                    <?php if (isset($_POST['bill_ids']) && !empty($_POST['bill_ids'])): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="preview_selected">
                            <?php foreach ($_POST['bill_ids'] as $billId): ?>
                                <input type="hidden" name="bill_ids[]" value="<?php echo intval($billId); ?>">
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-eye"></i>
                                Preview Selected Bills
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="list.php" class="btn btn-outline">
                            <i class="fas fa-list"></i>
                            Go to Bills List
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Filter-based Selection -->
                <div class="selection-card">
                    <div class="selection-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div class="selection-title">Filter-based Selection</div>
                    <div class="selection-description">
                        Select bills to print based on criteria like bill type, zone, status, or year. Great for bulk operations.
                    </div>
                    <button class="btn btn-primary" onclick="toggleFilterForm()">
                        <i class="fas fa-sliders-h"></i>
                        Set Print Filters
                    </button>
                </div>
            </div>

            <!-- Filter Form (Hidden by default) -->
            <div class="filter-form" id="filterForm" style="display: none;">
                <form method="POST">
                    <input type="hidden" name="action" value="preview_filtered">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Bill Type</label>
                            <select name="filter_type" class="form-input">
                                <option value="">All Types</option>
                                <option value="Business" <?php echo $filterType === 'Business' ? 'selected' : ''; ?>>Business Bills</option>
                                <option value="Property" <?php echo $filterType === 'Property' ? 'selected' : ''; ?>>Property Bills</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Zone</label>
                            <select name="filter_zone" class="form-input">
                                <option value="">All Zones</option>
                                <?php foreach ($zones as $zone): ?>
                                    <option value="<?php echo $zone['zone_id']; ?>" 
                                            <?php echo $filterZone == $zone['zone_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($zone['zone_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Business Type</label>
                            <select name="filter_business_type" class="form-input">
                                <option value="">All Business Types</option>
                                <?php foreach ($businessTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['business_type']); ?>" 
                                            <?php echo $filterBusinessType === $type['business_type'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['business_type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="filter_status" class="form-input">
                                <option value="">All Status</option>
                                <option value="Pending" <?php echo $filterStatus === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Paid" <?php echo $filterStatus === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="Partially Paid" <?php echo $filterStatus === 'Partially Paid' ? 'selected' : ''; ?>>Partially Paid</option>
                                <option value="Overdue" <?php echo $filterStatus === 'Overdue' ? 'selected' : ''; ?>>Overdue</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Billing Year</label>
                            <select name="filter_year" class="form-input">
                                <option value="">All Years</option>
                                <?php for ($year = date('Y'); $year >= 2020; $year--): ?>
                                    <option value="<?php echo $year; ?>" 
                                            <?php echo $filterYear == $year ? 'selected' : ''; ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="toggleFilterForm()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Find Bills to Print
                        </button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Print Statistics -->
            <div class="stats-card">
                <div class="stats-content">
                    <div class="stats-title">
                        <i class="fas fa-chart-bar"></i>
                        Print Summary
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($printStats['total_bills']); ?></div>
                            <div class="stat-label">Total Bills</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($printStats['business_bills']); ?></div>
                            <div class="stat-label">Business Bills</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($printStats['property_bills']); ?></div>
                            <div class="stat-label">Property Bills</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">GHS <?php echo number_format($printStats['total_amount'], 0); ?></div>
                            <div class="stat-label">Total Amount</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Options -->
            <div class="print-options">
                <h3 style="margin-bottom: 20px; color: #2d3748;">
                    <i class="fas fa-cog"></i>
                    Print Options
                </h3>
                
                <div class="option-group">
                    <div class="option-title">Layout Options</div>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="two_per_page" class="checkbox" checked>
                            <label for="two_per_page">Two bills per page</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="include_qr" class="checkbox" checked>
                            <label for="include_qr">Include QR codes</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="include_watermark" class="checkbox">
                            <label for="include_watermark">Add watermark</label>
                        </div>
                    </div>
                </div>

                <div class="option-group">
                    <div class="option-title">Output Format</div>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="radio" id="format_pdf" name="output_format" class="checkbox" value="pdf" checked>
                            <label for="format_pdf">PDF Download</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="radio" id="format_print" name="output_format" class="checkbox" value="print">
                            <label for="format_print">Direct Print</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bills Preview -->
            <div class="preview-card">
                <div class="preview-header">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Bills Preview (<?php echo count($bills); ?> bills)
                    </div>
                    <div class="preview-actions">
                        <button class="btn btn-secondary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left"></i>
                            Back
                        </button>
                        <button class="btn btn-success btn-lg" onclick="printBills()">
                            <i class="fas fa-print"></i>
                            Print All Bills
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllPreview" onchange="toggleAllPreview()" checked>
                                </th>
                                <th>Bill Number</th>
                                <th>Type</th>
                                <th>Payer</th>
                                <th>Account</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Zone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox print-checkbox" 
                                               value="<?php echo $bill['bill_id']; ?>" checked>
                                    </td>
                                    <td>
                                        <span class="bill-number"><?php echo htmlspecialchars($bill['bill_number']); ?></span>
                                    </td>
                                    <td>
                                        <span class="type-badge type-<?php echo strtolower($bill['bill_type']); ?>">
                                            <?php echo htmlspecialchars($bill['bill_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">
                                            <?php echo htmlspecialchars($bill['payer_name'] ?: 'Unknown'); ?>
                                        </div>
                                        <?php if ($bill['telephone']): ?>
                                            <div style="font-size: 12px; color: #64748b;">
                                                <?php echo htmlspecialchars($bill['telephone']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-family: monospace; font-weight: 600;">
                                            <?php echo htmlspecialchars($bill['account_number'] ?: 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="amount">GHS <?php echo number_format($bill['amount_payable'], 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $bill['status'])); ?>">
                                            <?php echo htmlspecialchars($bill['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($bill['zone_name'] ?: 'Unassigned'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function toggleAllPreview() {
            const selectAll = document.getElementById('selectAllPreview');
            const checkboxes = document.querySelectorAll('.print-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function printBills() {
            const selectedBills = Array.from(document.querySelectorAll('.print-checkbox:checked'))
                .map(checkbox => checkbox.value);
            
            if (selectedBills.length === 0) {
                alert('Please select at least one bill to print');
                return;
            }

            const twoPerPage = document.getElementById('two_per_page').checked;
            const includeQR = document.getElementById('include_qr').checked;
            const includeWatermark = document.getElementById('include_watermark').checked;
            const outputFormat = document.querySelector('input[name="output_format"]:checked').value;

            // Create print window
            const printWindow = window.open('', '_blank');
            
            // Build print content
            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Bulk Bills Print - <?php echo APP_NAME; ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                        .bill { 
                            width: ${twoPerPage ? '48%' : '100%'}; 
                            margin-bottom: 30px; 
                            padding: 20px; 
                            border: 2px solid #333; 
                            display: ${twoPerPage ? 'inline-block' : 'block'};
                            vertical-align: top;
                            margin-right: ${twoPerPage ? '2%' : '0'};
                            page-break-inside: avoid;
                        }
                        .bill-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                        .bill-title { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                        .bill-number { font-size: 18px; color: #666; }
                        .bill-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
                        .info-section { flex: 1; margin-right: 20px; }
                        .info-section h3 { margin-bottom: 10px; color: #333; border-bottom: 1px solid #ccc; }
                        .breakdown { margin: 20px 0; }
                        .breakdown table { width: 100%; border-collapse: collapse; }
                        .breakdown th, .breakdown td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                        .breakdown th { background: #f5f5f5; font-weight: bold; }
                        .total-row { font-weight: bold; background: #e8f5e8; }
                        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 72px; color: rgba(0,0,0,0.1); z-index: -1; }
                        .qr-code { text-align: center; margin-top: 20px; }
                        @media print { 
                            body { margin: 0; } 
                            .bill { page-break-after: ${twoPerPage ? 'auto' : 'always'}; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${includeWatermark ? '<div class="watermark">OFFICIAL</div>' : ''}
            `;

            // Add bills to print content
            <?php if (!empty($bills)): ?>
                const billsData = <?php echo json_encode($bills); ?>;
                
                selectedBills.forEach(billId => {
                    const bill = billsData.find(b => b.bill_id == billId);
                    if (!bill) return;

                    printContent += `
                        <div class="bill">
                            <div class="bill-header">
                                <div class="bill-title"><?php echo APP_NAME; ?></div>
                                <div class="bill-title">OFFICIAL BILL</div>
                                <div class="bill-number">Bill No: ${bill.bill_number}</div>
                                <div style="margin-top: 10px; font-size: 14px;">Year: ${bill.billing_year}</div>
                            </div>
                            
                            <div class="bill-info">
                                <div class="info-section">
                                    <h3>${bill.bill_type} Information</h3>
                                    <p><strong>Name:</strong> ${bill.payer_name || 'N/A'}</p>
                                    <p><strong>Account:</strong> ${bill.account_number || 'N/A'}</p>
                                    ${bill.telephone ? `<p><strong>Phone:</strong> ${bill.telephone}</p>` : ''}
                                    ${bill.zone_name ? `<p><strong>Zone:</strong> ${bill.zone_name}</p>` : ''}
                                    ${bill.bill_type === 'Business' ? `
                                        ${bill.business_type ? `<p><strong>Type:</strong> ${bill.business_type}</p>` : ''}
                                        ${bill.category ? `<p><strong>Category:</strong> ${bill.category}</p>` : ''}
                                    ` : `
                                        ${bill.structure ? `<p><strong>Structure:</strong> ${bill.structure}</p>` : ''}
                                        ${bill.property_use ? `<p><strong>Use:</strong> ${bill.property_use}</p>` : ''}
                                        ${bill.number_of_rooms ? `<p><strong>Rooms:</strong> ${bill.number_of_rooms}</p>` : ''}
                                    `}
                                </div>
                                
                                <div class="info-section">
                                    <h3>Bill Details</h3>
                                    <p><strong>Status:</strong> ${bill.status}</p>
                                    <p><strong>Generated:</strong> ${new Date(bill.generated_at).toLocaleDateString()}</p>
                                    ${bill.location ? `<p><strong>Location:</strong> ${bill.location}</p>` : ''}
                                </div>
                            </div>
                            
                            <div class="breakdown">
                                <table>
                                    <tr><th>Description</th><th>Amount (GHS)</th></tr>
                                    <tr><td>Old Bill</td><td>${parseFloat(bill.old_bill).toFixed(2)}</td></tr>
                                    <tr><td>Arrears</td><td>${parseFloat(bill.arrears).toFixed(2)}</td></tr>
                                    <tr><td>Current Bill</td><td>${parseFloat(bill.current_bill).toFixed(2)}</td></tr>
                                    <tr><td>Previous Payments</td><td>(${parseFloat(bill.previous_payments).toFixed(2)})</td></tr>
                                    <tr class="total-row"><td><strong>TOTAL AMOUNT PAYABLE</strong></td><td><strong>${parseFloat(bill.amount_payable).toFixed(2)}</strong></td></tr>
                                </table>
                            </div>
                            
                            ${includeQR ? `
                                <div class="qr-code">
                                    <div style="border: 1px solid #ccc; padding: 10px; display: inline-block;">
                                        <div style="width: 100px; height: 100px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                            QR CODE<br/>${bill.bill_number}
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                            
                            <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
                                <p>This is an official bill. Please pay on or before the due date.</p>
                                <p>For inquiries, contact the Municipal Assembly office.</p>
                            </div>
                        </div>
                    `;
                });
            <?php endif; ?>

            printContent += `
                    <div class="no-print" style="margin-top: 30px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; margin-right: 10px;">Print Bills</button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 5px;">Close</button>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.write(printContent);
            printWindow.document.close();

            // Auto-print if direct print is selected
            if (outputFormat === 'print') {
                setTimeout(() => {
                    printWindow.print();
                }, 1000);
            }

            // Log the print action
            <?php if (function_exists('logAuditActivity')): ?>
                // This would be implemented server-side
                console.log('Print action logged for bills:', selectedBills);
            <?php endif; ?>
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-show filter form if we have filter parameters
            <?php if (!empty($filterType) || !empty($filterZone) || !empty($filterBusinessType) || !empty($filterStatus) || !empty($filterYear)): ?>
                document.getElementById('filterForm').style.display = 'block';
            <?php endif; ?>
        });
    </script>
</body>
</html>
