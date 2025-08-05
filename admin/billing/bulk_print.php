<<<<<<< HEAD
 <?php
/**
 * Billing Management - Bulk Print Bills
=======
<?php
/**
 * Billing Management - Bulk Print Bills with Official Format
>>>>>>> c9ccaba (Initial commit)
 * QUICKBILL 305 - Admin Panel
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
<<<<<<< HEAD
=======
require_once '../../vendor/autoload.php';

// QR Code libraries
use chillerlan\QRCode\QRCode as ChillerlanQRCode;
use chillerlan\QRCode\QROptions;
>>>>>>> c9ccaba (Initial commit)

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
<<<<<<< HEAD
=======
// Check session expiration
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // Session expired (30 minutes)
    session_unset();
    session_destroy();
    setFlashMessage('error', 'Your session has expired. Please log in again.');
    header('Location: ../../index.php');
    exit();
}
>>>>>>> c9ccaba (Initial commit)

$pageTitle = 'Bulk Print Bills';
$currentUser = getCurrentUser();

// Initialize variables
$errors = [];
$selectedBillIds = [];
$bills = [];
$printStats = [];
$zones = [];
$businessTypes = [];
<<<<<<< HEAD
=======
$assemblyName = 'Anloga District Assembly'; // Default fallback
>>>>>>> c9ccaba (Initial commit)

// Get filter options
$filterType = sanitizeInput($_GET['type'] ?? $_POST['filter_type'] ?? '');
$filterZone = intval($_GET['zone_id'] ?? $_POST['filter_zone'] ?? 0);
$filterBusinessType = sanitizeInput($_GET['business_type'] ?? $_POST['filter_business_type'] ?? '');
$filterStatus = sanitizeInput($_GET['status'] ?? $_POST['filter_status'] ?? '');
$filterYear = intval($_GET['year'] ?? $_POST['filter_year'] ?? 0);

try {
    $db = new Database();
    
<<<<<<< HEAD
=======
    // Get assembly name from settings
    $assemblyNameSetting = $db->fetchRow("SELECT setting_value FROM system_settings WHERE setting_key = 'assembly_name'");
    if ($assemblyNameSetting && !empty($assemblyNameSetting['setting_value'])) {
        $assemblyName = $assemblyNameSetting['setting_value'];
    }
    
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
                   u.first_name as generated_by_name, 
                   u.last_name as generated_by_surname,
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                   -- Business specific fields
                   bs.business_type,
                   bs.category,
                   -- Property specific fields
                   pr.structure,
                   pr.property_use,
                   pr.number_of_rooms
            FROM bills b
=======
                   CASE 
                       WHEN b.bill_type = 'Business' THEN sz1.sub_zone_name
                       WHEN b.bill_type = 'Property' THEN sz2.sub_zone_name
                   END as sub_zone_name,
                   -- Business specific fields
                   bs.business_type,
                   bs.category,
                   bfs.fee_amount as annual_fee,
                   -- Property specific fields
                   pr.structure,
                   pr.property_use,
                   pr.number_of_rooms,
                   pr.gender,
                   pr.ownership_type,
                   pr.property_type,
                   pfs.fee_per_room
            FROM bills b
            LEFT JOIN users u ON b.generated_by = u.user_id
>>>>>>> c9ccaba (Initial commit)
            LEFT JOIN businesses bs ON b.bill_type = 'Business' AND b.reference_id = bs.business_id
            LEFT JOIN properties pr ON b.bill_type = 'Property' AND b.reference_id = pr.property_id
            LEFT JOIN zones z1 ON bs.zone_id = z1.zone_id
            LEFT JOIN zones z2 ON pr.zone_id = z2.zone_id
<<<<<<< HEAD
=======
            LEFT JOIN sub_zones sz1 ON bs.sub_zone_id = sz1.sub_zone_id
            LEFT JOIN sub_zones sz2 ON pr.sub_zone_id = sz2.sub_zone_id
            LEFT JOIN business_fee_structure bfs ON b.bill_type = 'Business' AND bs.business_type = bfs.business_type AND bs.category = bfs.category AND bfs.is_active = 1
            LEFT JOIN property_fee_structure pfs ON b.bill_type = 'Property' AND pr.structure = pfs.structure AND pr.property_use = pfs.property_use AND pfs.is_active = 1
>>>>>>> c9ccaba (Initial commit)
            WHERE b.bill_id IN ({$placeholders})
            ORDER BY b.bill_type, b.bill_number
        ";
        
        $bills = $db->fetchAll($billsQuery, $selectedBillIds);
        
<<<<<<< HEAD
=======
        // Generate QR codes for all bills
        $qr_dir = __DIR__ . '/../../assets/qr_codes/';
        if (!is_dir($qr_dir)) {
            mkdir($qr_dir, 0755, true);
        }
        
        foreach ($bills as &$bill) {
            // Create complete bill content for QR code
            $bill_type_full = $bill['bill_type'] === 'Business' ? 'Business License Bill' : 'Property Rate Bill';
            $bill_date = date('d/m/Y', strtotime($bill['generated_at']));
            
            $qr_data = $assemblyName . "\n";
            $qr_data .= str_repeat("=", 40) . "\n";
            $qr_data .= $bill_type_full . "\n";
            $qr_data .= "Bill Date: " . $bill_date . "\n";
            $qr_data .= "Zone: " . ($bill['zone_name'] ?? '') . "\n";
            $qr_data .= str_repeat("-", 40) . "\n\n";
            
            // Payer Information
            if ($bill['bill_type'] === 'Business') {
                $qr_data .= "BUSINESS INFORMATION:\n";
                $qr_data .= "Business Name: " . ($bill['payer_name'] ?? '') . "\n";
                $qr_data .= "Owner: " . ($bill['owner_name'] ?? '') . "\n";
                $qr_data .= "Telephone: " . ($bill['telephone'] ?? '') . "\n";
                $qr_data .= "Account#: " . ($bill['account_number'] ?? '') . "\n";
                $qr_data .= "Zone: " . ($bill['zone_name'] ?? '') . "\n";
                $qr_data .= "Business Type: " . ($bill['business_type'] ?? '') . "\n";
            } else {
                $qr_data .= "PROPERTY INFORMATION:\n";
                $qr_data .= "Owner Name: " . ($bill['owner_name'] ?? '') . "\n";
                $qr_data .= "Telephone: " . ($bill['telephone'] ?? '') . "\n";
                $qr_data .= "Property#: " . ($bill['account_number'] ?? '') . "\n";
                $qr_data .= "Zone: " . ($bill['zone_name'] ?? '') . "\n";
                $qr_data .= "Structure: " . ($bill['structure'] ?? '') . "\n";
            }
            
            $qr_data .= "\nBILL DETAILS:\n";
            $qr_data .= "Bill Number: " . ($bill['bill_number'] ?? '') . "\n";
            $qr_data .= "Bill Year: " . ($bill['billing_year'] ?? '') . "\n";
            $qr_data .= str_repeat("-", 40) . "\n";
            
            // Financial breakdown
            $qr_data .= "FINANCIAL BREAKDOWN:\n";
            $qr_data .= "Old Fee: GHS " . number_format($bill['old_bill'] ?? 0, 2) . "\n";
            $qr_data .= "Previous Payments: GHS " . number_format($bill['previous_payments'] ?? 0, 2) . "\n";
            $qr_data .= "Arrears: GHS " . number_format($bill['arrears'] ?? 0, 2) . "\n";
            $qr_data .= "Current Rate: GHS " . number_format($bill['current_bill'] ?? 0, 2) . "\n";
            $qr_data .= str_repeat("-", 40) . "\n";
            $qr_data .= "TOTAL AMOUNT DUE: GHS " . number_format($bill['amount_payable'] ?? 0, 2) . "\n";
            $qr_data .= str_repeat("=", 40) . "\n\n";
            
            $qr_data .= "PAYMENT INSTRUCTIONS:\n";
            $qr_data .= "Please present this bill when making payment.\n";
            $qr_data .= "Pay to DISTRICT FINANCE OFFICER or\n";
            $qr_data .= "authorized Revenue Collector.\n";
            $qr_data .= "Payment Due: 1st September 2025\n\n";
            $qr_data .= "For inquiries: 0249579191\n";
            $qr_data .= str_repeat("=", 40) . "\n";
            $qr_data .= $assemblyName;
            
            // Generate QR code if data is not too large
            $bill['qr_url'] = null;
            if (strlen($qr_data) <= 2500) {
                $qr_file = $qr_dir . 'bill_' . $bill['bill_id'] . '.png';
                try {
                    $options = new QROptions([
                        'outputType' => ChillerlanQRCode::OUTPUT_IMAGE_PNG,
                        'eccLevel' => ChillerlanQRCode::ECC_M,
                        'imageBase64' => false,
                        'scale' => 2, // Reduced from 3 to make the QR code smaller
                        'imageTransparent' => false,
                        'quietzoneSize' => 1, // Reduced from 2 for a smaller border
                    ]);

                    if (file_exists($qr_file)) {
                        unlink($qr_file);
                    }

                    $qrcode = new ChillerlanQRCode($options);
                    $qrcode->render($qr_data, $qr_file);

                    if (file_exists($qr_file) && filesize($qr_file) > 0) {
                        $bill['qr_url'] = '../../assets/qr_codes/bill_' . $bill['bill_id'] . '.png';
                    }
                } catch (Exception $e) {
                    writeLog("QR code generation failed for bill ID {$bill['bill_id']}: " . $e->getMessage(), 'ERROR');
                }
            }
        }
        unset($bill); // Break the reference
        
        // Count successfully generated QR codes
        $qrGeneratedCount = count(array_filter($bills, function($bill) {
            return !empty($bill['qr_url']);
        }));
        
        if ($qrGeneratedCount > 0) {
            writeLog("Successfully generated {$qrGeneratedCount} QR codes for bulk print", 'INFO');
        }
        
>>>>>>> c9ccaba (Initial commit)
        // Calculate print statistics
        $printStats = [
            'total_bills' => count($bills),
            'business_bills' => count(array_filter($bills, function($bill) { return $bill['bill_type'] === 'Business'; })),
            'property_bills' => count(array_filter($bills, function($bill) { return $bill['bill_type'] === 'Property'; })),
            'total_amount' => array_sum(array_column($bills, 'amount_payable')),
            'pending_bills' => count(array_filter($bills, function($bill) { return $bill['status'] === 'Pending'; })),
            'paid_bills' => count(array_filter($bills, function($bill) { return $bill['status'] === 'Paid'; })),
<<<<<<< HEAD
            'overdue_bills' => count(array_filter($bills, function($bill) { return in_array($bill['status'], ['Overdue', 'Partially Paid']); }))
=======
            'overdue_bills' => count(array_filter($bills, function($bill) { return in_array($bill['status'], ['Overdue', 'Partially Paid']); })),
            'qr_codes_generated' => $qrGeneratedCount
>>>>>>> c9ccaba (Initial commit)
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
        
<<<<<<< HEAD
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
        
=======
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
            <p style="color: #64748b;">Print multiple bills at once with customizable options</p>
=======
            <p style="color: #64748b;">Print multiple bills at once with official formatting</p>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
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
=======
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($printStats['qr_codes_generated']); ?></div>
                            <div class="stat-label">QR Codes Generated</div>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
                                <th>QR</th>
>>>>>>> c9ccaba (Initial commit)
                                <th>Status</th>
                                <th>Zone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td>
<<<<<<< HEAD
                                        <input type="checkbox" class="checkbox print-checkbox" 
=======
                                        <input type="checkbox" class="print-checkbox" 
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
                                        <?php if (!empty($bill['qr_url']) && file_exists(__DIR__ . '/../../assets/qr_codes/bill_' . $bill['bill_id'] . '.png')): ?>
                                            <span style="color: #10b981; font-weight: 600;">✓</span>
                                        <?php else: ?>
                                            <span style="color: #ef4444; font-weight: 600;">✗</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
            const twoPerPage = document.getElementById('two_per_page').checked;
            const includeQR = document.getElementById('include_qr').checked;
            const includeWatermark = document.getElementById('include_watermark').checked;
            const outputFormat = document.querySelector('input[name="output_format"]:checked').value;
=======
            // Fixed default settings (no checkboxes to read from)
            const twoPerPage = true;        // Two bills per page (vertically stacked)
            const includeQR = true;         // Include QR codes
            const includeWatermark = true;  // Add watermark
            const officialFormat = true;    // Official format
            const outputFormat = 'print';   // Direct print
>>>>>>> c9ccaba (Initial commit)

            // Create print window
            const printWindow = window.open('', '_blank');
            
<<<<<<< HEAD
            // Build print content
=======
            // Assembly name from PHP
            const assemblyName = <?php echo json_encode($assemblyName); ?>;
            
            // Build print content with official format and enhanced watermarks
>>>>>>> c9ccaba (Initial commit)
            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
<<<<<<< HEAD
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
=======
                    <title>Bulk Bills Print - ${assemblyName}</title>
                    <style>
                        * { 
                            margin: 0; 
                            padding: 0; 
                            box-sizing: border-box; 
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        body { 
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                            margin: 0; 
                            padding: ${twoPerPage ? '10px' : '20px'}; 
                            background: white;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        
                        .bill-container { 
                            width: ${twoPerPage ? '100%' : '100%'}; 
                            margin-bottom: ${twoPerPage ? '20px' : '30px'}; 
                            position: relative;
                            overflow: hidden;
                            min-height: ${twoPerPage ? '380px' : '600px'};
                            display: block;
                            vertical-align: top;
                            page-break-inside: avoid;
                            background: white;
                        }
                        
                        .bill-wrapper {
                            width: 100%;
                            padding: ${twoPerPage ? '12px' : '30px'};
                            position: relative;
                            overflow: hidden;
                            z-index: 1;
                            background-color: #fff;
                            box-sizing: border-box;
                        }
                        
                        /* Enhanced Watermark System - Using HTML elements for better print support */
                        .watermark {
                            position: absolute;
                            font-size: ${twoPerPage ? '40px' : '65px'};
                            font-weight: bold;
                            font-family: Arial, Helvetica, sans-serif;
                            color: rgba(0,0,0,0.12) !important;
                            z-index: 0;
                            pointer-events: none;
                            opacity: 1 !important;
                            transform: rotate(-45deg);
                            user-select: none;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        
                        .watermark-top-left {
                            top: 15%;
                            left: 15%;
                            transform: translate(-50%, -50%) rotate(-45deg);
                        }
                        
                        .watermark-center {
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%) rotate(-45deg);
                        }
                        
                        .watermark-bottom-right {
                            bottom: 15%;
                            right: 15%;
                            transform: translate(50%, 50%) rotate(-45deg);
                        }
                        
                        .watermark-top-right {
                            top: 20%;
                            right: 20%;
                            transform: translate(50%, -50%) rotate(-45deg);
                        }
                        
                        .watermark-bottom-left {
                            bottom: 20%;
                            left: 20%;
                            transform: translate(-50%, 50%) rotate(-45deg);
                        }
                        
                        .bill-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-bottom: 1px solid #000;
                            padding-bottom: ${twoPerPage ? '8px' : '15px'};
                            position: relative;
                            z-index: 2;
                            margin-bottom: ${twoPerPage ? '10px' : '20px'};
                            flex-wrap: wrap;
                            gap: ${twoPerPage ? '8px' : '15px'};
                        }
                        
                        .bill-logo {
                            width: ${twoPerPage ? '45px' : '80px'};
                            height: auto;
                            z-index: 2;
                            flex-shrink: 0;
                        }
                        
                        .bill-header-text {
                            text-align: center;
                            flex-grow: 1;
                            z-index: 2;
                            min-width: ${twoPerPage ? '150px' : '200px'};
                        }
                        
                        .bill-header-text h1 {
                            margin: 0;
                            font-size: ${twoPerPage ? '16px' : '28px'};
                            font-weight: bold;
                            color: #000;
                        }
                        
                        .bill-header-text h2 {
                            margin: ${twoPerPage ? '5px 0' : '8px 0'};
                            font-size: ${twoPerPage ? '12px' : '18px'};
                            font-weight: normal;
                            color: #333;
                        }
                        
                        .bill-header-text p {
                            margin: 0;
                            font-size: ${twoPerPage ? '10px' : '14px'};
                            color: #666;
                        }
                        
                        .bill-header-right {
                            text-align: right;
                            z-index: 2;
                            flex-shrink: 0;
                            min-width: ${twoPerPage ? '110px' : '150px'};
                        }
                        
                        .bill-header-right h2 {
                            margin: 0;
                            font-size: ${twoPerPage ? '12px' : '16px'};
                            font-weight: bold;
                            color: #000;
                        }
                        
                        .bill-header-right p {
                            margin: ${twoPerPage ? '3px 0' : '4px 0'};
                            font-size: ${twoPerPage ? '10px' : '14px'};
                            color: #333;
                        }
                        
                        .bill-content {
                            display: flex;
                            justify-content: space-between;
                            margin-top: ${twoPerPage ? '12px' : '20px'};
                            position: relative;
                            z-index: 2;
                            gap: ${twoPerPage ? '10px' : '20px'};
                            flex-wrap: wrap;
                            align-items: flex-start;
                        }
                        
                        .bill-left-section, .bill-right-section {
                            flex: 1;
                            min-width: ${twoPerPage ? '160px' : '300px'};
                            max-width: calc(50% - ${twoPerPage ? '5px' : '10px'});
                            box-sizing: border-box;
                        }
                        
                        .bill-info-box {
                            border: 1px solid #000;
                            padding: ${twoPerPage ? '8px' : '15px'};
                            margin-bottom: ${twoPerPage ? '10px' : '15px'};
                            position: relative;
                            z-index: 2;
                            background: white;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                            box-sizing: border-box;
                        }
                        
                        .bill-info-box p {
                            margin: ${twoPerPage ? '5px 0' : '8px 0'};
                            font-size: ${twoPerPage ? '10px' : '14px'};
                            color: #000;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                        }
                        
                        .bill-info-box p strong {
                            display: inline-block;
                            width: ${twoPerPage ? '75px' : '130px'};
                            font-weight: bold;
                            vertical-align: top;
                        }
                        
                        .bill-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: ${twoPerPage ? '8px' : '10px'};
                            position: relative;
                            z-index: 2;
                            table-layout: fixed;
                            overflow-x: auto;
                        }
                        
                        .bill-table, .bill-table th, .bill-table td {
                            border: 1px solid #000;
                            padding: ${twoPerPage ? '3px 2px' : '6px 4px'};
                            text-align: center;
                            font-size: ${twoPerPage ? '9px' : '12px'};
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                        }
                        
                        .bill-table th {
                            background-color: #f0f0f0;
                            font-weight: bold;
                            color: #000;
                            line-height: 1.2;
                        }
                        
                        .bill-table td {
                            color: #000;
                            line-height: 1.2;
                        }
                        
                        .bill-note {
                            font-size: ${twoPerPage ? '10px' : '14px'};
                            text-align: right;
                            margin-bottom: ${twoPerPage ? '8px' : '10px'};
                            position: relative;
                            z-index: 2;
                            color: #000;
                            font-weight: 500;
                        }
                        
                        .bill-qr-code {
                            text-align: center;
                            margin-top: ${twoPerPage ? '4px' : '5px'};
                            position: relative;
                            z-index: 2;
                            padding: ${twoPerPage ? '4px 0' : '5px 0'};
                        }
                        
                        .bill-qr-code img {
                            width: ${twoPerPage ? '70px' : '120px'};
                            height: ${twoPerPage ? '70px' : '120px'};
                            border: 1px solid #ddd;
                        }
                        
                        .qr-placeholder {
                            width: ${twoPerPage ? '70px' : '120px'};
                            height: ${twoPerPage ? '70px' : '120px'};
                            border: 1px solid #ddd;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto;
                            background: #f8f9fa;
                            color: #666;
                            font-size: ${twoPerPage ? '9px' : '12px'};
                            text-align: center;
                        }
                        
                        .bill-footer {
                            border-top: 1px solid #000;
                            padding-top: ${twoPerPage ? '10px' : '15px'};
                            text-align: center;
                            font-size: ${twoPerPage ? '9px' : '13px'};
                            position: relative;
                            z-index: 2;
                            color: #000;
                            margin-top: ${twoPerPage ? '10px' : '15px'};
                            clear: both;
                        }
                        
                        .bill-footer p {
                            margin: ${twoPerPage ? '5px 0' : '8px 0'};
                            line-height: ${twoPerPage ? '1.3' : '1.4'};
                        }
                        
                        @media print { 
                            body { margin: 0; padding: 5px; }
                            .no-print { display: none; } 
                            .bill-container { 
                                page-break-inside: avoid;
                                margin-bottom: ${twoPerPage ? '12px' : '30px'};
                            }
                            .bill-container:nth-child(2n) { 
                                page-break-after: ${twoPerPage ? 'always' : 'auto'}; 
                            }
                            .bill-content { gap: ${twoPerPage ? '8px' : '15px'}; }
                            .bill-left-section, .bill-right-section { max-width: 48%; }
                            
                            /* Enhanced watermark visibility for print */
                            .watermark {
                                color: rgba(0,0,0,0.15) !important;
                                opacity: 1 !important;
                                -webkit-print-color-adjust: exact !important;
                                print-color-adjust: exact !important;
                            }
>>>>>>> c9ccaba (Initial commit)
                        }
                    </style>
                </head>
                <body>
<<<<<<< HEAD
                    ${includeWatermark ? '<div class="watermark">OFFICIAL</div>' : ''}
=======
>>>>>>> c9ccaba (Initial commit)
            `;

            // Add bills to print content
            <?php if (!empty($bills)): ?>
                const billsData = <?php echo json_encode($bills); ?>;
                
<<<<<<< HEAD
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
=======
                // Debug: Log bills data to see QR URLs
                console.log('Bills data with QR URLs:', billsData.map(b => ({
                    bill_id: b.bill_id,
                    bill_number: b.bill_number,
                    qr_url: b.qr_url,
                    qr_available: !!b.qr_url
                })));
                
                selectedBills.forEach((billId, index) => {
                    const bill = billsData.find(b => b.bill_id == billId);
                    if (!bill) return;

                    const billDate = new Date(bill.generated_at).toLocaleDateString();
                    const billTypeTitle = bill.bill_type === 'Business' ? 'Business License Bill' : 'Property Rate Bill';

                    printContent += `
                        <div class="bill-container">
                            <div class="bill-wrapper">
                                ${includeWatermark ? `
                                    <div class="watermark watermark-top-left">AnDA</div>
                                    <div class="watermark watermark-center">AnDA</div>
                                    <div class="watermark watermark-bottom-right">AnDA</div>
                                    <div class="watermark watermark-top-right">AnDA</div>
                                    <div class="watermark watermark-bottom-left">AnDA</div>
                                ` : ''}
                                
                                <!-- Bill Header -->
                                <div class="bill-header">
                                    <img src="../../assets/images/download.png" alt="${assemblyName} Logo" class="bill-logo">
                                    <div class="bill-header-text">
                                        <h1>${assemblyName}</h1>
                                        <h2>${billTypeTitle}</h2>
                                        <p>Bill Date: ${billDate} / Zone: ${bill.zone_name || ''}</p>
                                    </div>
                                    <div class="bill-header-right">
                                        <h2>${assemblyName}</h2>
                                        <p>${billTypeTitle}</p>
                                        <p>Bill Date: ${billDate}</p>
                                    </div>
                                </div>

                                <!-- Bill Content -->
                                <div class="bill-content">
                                    <div class="bill-left-section">
                                        <div class="bill-info-box">
                                            ${bill.bill_type === 'Business' ? `
                                                <p><strong>Business Name:</strong> ${bill.payer_name || ''}</p>
                                                <p><strong>Owner / Tel:</strong> ${bill.owner_name || ''} / ${bill.telephone || ''}</p>
                                                <p><strong>Acct#:</strong> ${bill.account_number || ''}</p>
                                                <p><strong>Location:</strong> ${bill.location || ''}</p>
                                                <p><strong>Bus.Type:</strong> ${bill.business_type || ''}</p>
                                            ` : `
                                                <p><strong>Owner Name:</strong> ${bill.owner_name || ''}</p>
                                                <p><strong>Owner / Tel:</strong> ${bill.owner_name || ''} / ${bill.telephone || ''}</p>
                                                <p><strong>Property#:</strong> ${bill.account_number || ''}</p>
                                                <p><strong>Location:</strong> ${bill.location || ''}</p>
                                                <p><strong>Structure:</strong> ${bill.structure || ''}</p>
                                            `}
                                        </div>
                                        <div class="bill-qr-code">
                                            ${includeQR ? `
                                                ${bill.qr_url ? `
                                                    <img src="${bill.qr_url}" alt="QR Code for Bill ${bill.bill_number}" 
                                                         style="width: ${twoPerPage ? '100px' : '150px'}; height: ${twoPerPage ? '100px' : '150px'}; border: 1px solid #ddd;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div class="qr-placeholder" style="display: none;">QR Code<br>Load Error</div>
                                                ` : `
                                                    <div class="qr-placeholder">QR Code<br>Not Available</div>
                                                `}
                                            ` : ''}
                                        </div>
                                    </div>

                                    <div class="bill-right-section">
                                        <div class="bill-info-box">
                                            <p><strong>Owner:</strong> ${bill.owner_name || bill.payer_name || ''}</p>
                                            <p><strong>${bill.bill_type === 'Business' ? 'Acct#' : 'Property#'}:</strong> ${bill.account_number || ''}</p>
                                            <p><strong>Bill Year:</strong> ${bill.billing_year}</p>
                                            <p><strong>Total Amount Due:</strong> GHS ${parseFloat(bill.amount_payable).toFixed(2)}</p>
                                        </div>
                                        <div style="overflow-x: auto;">
                                            <table class="bill-table">
                                                <thead>
                                                    <tr>
                                                        <th>Old Fee</th>
                                                        <th>Previous Payments</th>
                                                        <th>Arrears</th>
                                                        <th>Current Rate</th>
                                                        <th>Total Amount Due</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>GHS ${parseFloat(bill.old_bill).toFixed(2)}</td>
                                                        <td>GHS ${parseFloat(bill.previous_payments).toFixed(2)}</td>
                                                        <td>GHS ${parseFloat(bill.arrears).toFixed(2)}</td>
                                                        <td>GHS ${parseFloat(bill.current_bill).toFixed(2)}</td>
                                                        <td>GHS ${parseFloat(bill.amount_payable).toFixed(2)}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p class="bill-note">Please present this bill when making a payment</p>
                                    </div>
                                </div>

                                <!-- Bill Footer -->
                                <div class="bill-footer">
                                    <p>KINDLY pay the amount involved to the DISTRICT FINANCE OFFICER or to any Revenue Collector appointed by the Assembly ON OR BEFORE 1st of September 2025.</p>
                                    <p>For inquiries, please call: 0545041428</p>
                                </div>
                            </div>
                        </div>
		            `;
>>>>>>> c9ccaba (Initial commit)
                });
            <?php endif; ?>

            printContent += `
<<<<<<< HEAD
                    <div class="no-print" style="margin-top: 30px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; margin-right: 10px;">Print Bills</button>
                        <button onclick="window.close()" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 5px;">Close</button>
=======
                    <div class="no-print" style="margin-top: 30px; text-align: center; page-break-before: always;">
                        <button onclick="window.print()" style="padding: 15px 30px; background: #10b981; color: white; border: none; border-radius: 8px; margin-right: 15px; font-size: 16px; cursor: pointer;">Print Bills</button>
                        <button onclick="window.close()" style="padding: 15px 30px; background: #64748b; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">Close</button>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
            <?php if (function_exists('logAuditActivity')): ?>
                // This would be implemented server-side
                console.log('Print action logged for bills:', selectedBills);
            <?php endif; ?>
=======
            console.log('Bills printed:', selectedBills.length);
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> c9ccaba (Initial commit)
