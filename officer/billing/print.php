<?php
/**
 * Officer - Print Bills
 * Print bills individually or in bulk with PDF generation
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
if (!isOfficer() && !isAdmin()) {
    setFlashMessage('error', 'Access denied. Officer privileges required.');
    header('Location: ../../auth/login.php');
    exit();
}

$currentUser = getCurrentUser();

// Initialize variables
$search = $_GET['search'] ?? '';
$bill_type = $_GET['bill_type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$zone_filter = $_GET['zone'] ?? '';
$billing_year = $_GET['billing_year'] ?? date('Y');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Handle single bill print
if (isset($_GET['print_bill']) && !empty($_GET['bill_id'])) {
    $bill_id = intval($_GET['bill_id']);
    generatePDF($bill_id);
    exit();
}

// Handle bulk print
if (isset($_POST['bulk_print']) && !empty($_POST['selected_bills'])) {
    $bill_ids = array_map('intval', $_POST['selected_bills']);
    generateBulkPDF($bill_ids);
    exit();
}

try {
    $db = new Database();
    
    // Get filter options
    $zones = $db->fetchAll("SELECT zone_id, zone_name FROM zones ORDER BY zone_name");
    
    // Build query with filters
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(b.bill_number LIKE ? OR account_info.name LIKE ? OR account_info.account_number LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($bill_type)) {
        $whereConditions[] = "b.bill_type = ?";
        $params[] = $bill_type;
    }
    
    if (!empty($status_filter)) {
        $whereConditions[] = "b.status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($zone_filter)) {
        $whereConditions[] = "account_info.zone_id = ?";
        $params[] = $zone_filter;
    }
    
    if (!empty($billing_year)) {
        $whereConditions[] = "b.billing_year = ?";
        $params[] = $billing_year;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total 
                 FROM bills b 
                 LEFT JOIN (
                     SELECT business_id as id, business_name as name, account_number, zone_id, 'Business' as type FROM businesses
                     UNION ALL
                     SELECT property_id as id, owner_name as name, property_number as account_number, zone_id, 'Property' as type FROM properties
                 ) account_info ON account_info.id = b.reference_id AND account_info.type = b.bill_type
                 $whereClause";
    
    $totalResult = $db->fetchRow($countSql, $params);
    $totalRecords = $totalResult['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get bills with pagination
    $sql = "SELECT b.*, 
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bus.business_name
                       WHEN b.bill_type = 'Property' THEN prop.owner_name
                   END as account_name,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bus.account_number
                       WHEN b.bill_type = 'Property' THEN prop.property_number
                   END as account_number,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN bus.owner_name
                       WHEN b.bill_type = 'Property' THEN prop.telephone
                   END as contact_info,
                   CASE 
                       WHEN b.bill_type = 'Business' THEN z1.zone_name
                       WHEN b.bill_type = 'Property' THEN z2.zone_name
                   END as zone_name,
                   u.first_name, u.last_name
            FROM bills b 
            LEFT JOIN businesses bus ON b.bill_type = 'Business' AND b.reference_id = bus.business_id
            LEFT JOIN properties prop ON b.bill_type = 'Property' AND b.reference_id = prop.property_id
            LEFT JOIN zones z1 ON bus.zone_id = z1.zone_id
            LEFT JOIN zones z2 ON prop.zone_id = z2.zone_id
            LEFT JOIN users u ON b.generated_by = u.user_id
            $whereClause 
            ORDER BY b.generated_at DESC 
            LIMIT $limit OFFSET $offset";
    
    $bills = $db->fetchAll($sql, $params);
    
    // Get statistics
    $stats = $db->fetchRow("
        SELECT 
            COUNT(*) as total_bills,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_bills,
            SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paid_bills,
            SUM(CASE WHEN status = 'Partially Paid' THEN 1 ELSE 0 END) as partial_bills,
            SUM(amount_payable) as total_amount
        FROM bills
        WHERE billing_year = ?
    ", [$billing_year]);
    
} catch (Exception $e) {
    $bills = [];
    $totalRecords = 0;
    $totalPages = 0;
    $stats = [
        'total_bills' => 0,
        'pending_bills' => 0,
        'paid_bills' => 0,
        'partial_bills' => 0,
        'total_amount' => 0
    ];
    setFlashMessage('error', 'Error loading bills: ' . $e->getMessage());
}

// Function to generate single PDF
function generatePDF($bill_id) {
    global $db;
    
    try {
        // Get bill details with account information
        $sql = "SELECT b.*, 
                       CASE 
                           WHEN b.bill_type = 'Business' THEN bus.business_name
                           WHEN b.bill_type = 'Property' THEN prop.owner_name
                       END as account_name,
                       CASE 
                           WHEN b.bill_type = 'Business' THEN bus.account_number
                           WHEN b.bill_type = 'Property' THEN prop.property_number
                       END as account_number,
                       CASE 
                           WHEN b.bill_type = 'Business' THEN bus.owner_name
                           WHEN b.bill_type = 'Property' THEN CONCAT(prop.structure, ' - ', prop.property_use)
                       END as additional_info,
                       CASE 
                           WHEN b.bill_type = 'Business' THEN bus.exact_location
                           WHEN b.bill_type = 'Property' THEN prop.location
                       END as location,
                       CASE 
                           WHEN b.bill_type = 'Business' THEN bus.telephone
                           WHEN b.bill_type = 'Property' THEN prop.telephone
                       END as telephone,
                       CASE 
                           WHEN b.bill_type = 'Business' THEN z1.zone_name
                           WHEN b.bill_type = 'Property' THEN z2.zone_name
                       END as zone_name
                FROM bills b 
                LEFT JOIN businesses bus ON b.bill_type = 'Business' AND b.reference_id = bus.business_id
                LEFT JOIN properties prop ON b.bill_type = 'Property' AND b.reference_id = prop.property_id
                LEFT JOIN zones z1 ON bus.zone_id = z1.zone_id
                LEFT JOIN zones z2 ON prop.zone_id = z2.zone_id
                WHERE b.bill_id = ?";
        
        $bill = $db->fetchRow($sql, [$bill_id]);
        
        if (!$bill) {
            throw new Exception('Bill not found.');
        }
        
        // Generate HTML for PDF
        $html = generateBillHTML($bill);
        
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Bill_' . $bill['bill_number'] . '.pdf"');
        
        // For now, we'll output HTML since we don't have a PDF library
        // In production, you would use a library like TCPDF or DOMPDF
        echo $html;
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error generating PDF: ' . $e->getMessage());
        header('Location: print.php');
        exit();
    }
}

// Function to generate bulk PDF
function generateBulkPDF($bill_ids) {
    global $db;
    
    try {
        $html = '<html><head><title>Bulk Bills</title></head><body>';
        
        foreach ($bill_ids as $bill_id) {
            $sql = "SELECT b.*, 
                           CASE 
                               WHEN b.bill_type = 'Business' THEN bus.business_name
                               WHEN b.bill_type = 'Property' THEN prop.owner_name
                           END as account_name,
                           CASE 
                               WHEN b.bill_type = 'Business' THEN bus.account_number
                               WHEN b.bill_type = 'Property' THEN prop.property_number
                           END as account_number
                    FROM bills b 
                    LEFT JOIN businesses bus ON b.bill_type = 'Business' AND b.reference_id = bus.business_id
                    LEFT JOIN properties prop ON b.bill_type = 'Property' AND b.reference_id = prop.property_id
                    WHERE b.bill_id = ?";
            
            $bill = $db->fetchRow($sql, [$bill_id]);
            
            if ($bill) {
                $html .= generateBillHTML($bill, true);
                $html .= '<div style="page-break-after: always;"></div>';
            }
        }
        
        $html .= '</body></html>';
        
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Bulk_Bills_' . date('Y-m-d') . '.pdf"');
        
        echo $html;
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error generating bulk PDF: ' . $e->getMessage());
        header('Location: print.php');
        exit();
    }
}

// Function to generate bill HTML
function generateBillHTML($bill, $compact = false) {
    $assembly_name = getConfig('assembly_name', 'Municipal Assembly');
    
    $html = '
    <div style="font-family: Arial, sans-serif; ' . ($compact ? 'font-size: 12px;' : 'font-size: 14px;') . '">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #333;">' . htmlspecialchars($assembly_name) . '</h2>
            <h3 style="margin: 5px 0; color: #666;">REVENUE BILL</h3>
            <p style="margin: 0;">Billing Year: ' . htmlspecialchars($bill['billing_year']) . '</p>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div>
                <strong>Bill Number:</strong> ' . htmlspecialchars($bill['bill_number']) . '<br>
                <strong>Account Number:</strong> ' . htmlspecialchars($bill['account_number']) . '<br>
                <strong>Account Type:</strong> ' . htmlspecialchars($bill['bill_type']) . '<br>
                <strong>Generated Date:</strong> ' . formatDate($bill['generated_at']) . '
            </div>
            <div style="text-align: right;">
                <strong>Due Date:</strong> ' . ($bill['due_date'] ? formatDate($bill['due_date']) : 'N/A') . '<br>
                <strong>Status:</strong> ' . htmlspecialchars($bill['status']) . '
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h4 style="background: #f0f0f0; padding: 10px; margin: 0 0 10px 0;">Account Details</h4>
            <strong>Name:</strong> ' . htmlspecialchars($bill['account_name']) . '<br>
            ' . ($bill['additional_info'] ? '<strong>Additional Info:</strong> ' . htmlspecialchars($bill['additional_info']) . '<br>' : '') . '
            ' . ($bill['location'] ? '<strong>Location:</strong> ' . htmlspecialchars($bill['location']) . '<br>' : '') . '
            ' . ($bill['telephone'] ? '<strong>Telephone:</strong> ' . htmlspecialchars($bill['telephone']) . '<br>' : '') . '
            ' . ($bill['zone_name'] ? '<strong>Zone:</strong> ' . htmlspecialchars($bill['zone_name']) . '<br>' : '') . '
        </div>
        
        <div style="margin-bottom: 20px;">
            <h4 style="background: #f0f0f0; padding: 10px; margin: 0 0 10px 0;">Bill Breakdown</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Description</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>Amount (GHS)</strong></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Old Bill</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($bill['old_bill'], 2) . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Arrears</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($bill['arrears'], 2) . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Current Bill</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($bill['current_bill'], 2) . '</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Previous Payments</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">(' . number_format($bill['previous_payments'], 2) . ')</td>
                </tr>
                <tr style="background: #f9f9f9; font-weight: bold;">
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>TOTAL AMOUNT PAYABLE</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-size: 16px;"><strong>GHS ' . number_format($bill['amount_payable'], 2) . '</strong></td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666;">
            <p><strong>Payment Instructions:</strong></p>
            <p>• Pay at the Municipal Assembly office during working hours</p>
            <p>• Mobile Money payments accepted</p>
            <p>• Keep this bill for payment reference</p>
            <p>• Contact the Revenue Office for any inquiries</p>
        </div>
        
        ' . ($bill['qr_code'] ? '<div style="text-align: center; margin-top: 20px;"><img src="' . $bill['qr_code'] . '" alt="QR Code" style="width: 100px; height: 100px;"></div>' : '') . '
    </div>';
    
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bills - <?php echo APP_NAME; ?></title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <style>
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        
        .page-subtitle {
            color: #718096;
            font-size: 16px;
            margin-top: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
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
        
        .stat-card.danger {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .stat-title {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .stat-icon {
            font-size: 32px;
            opacity: 0.8;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .filters-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .btn-filter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            height: fit-content;
        }
        
        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-clear {
            background: #718096;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            height: fit-content;
        }
        
        .btn-clear:hover {
            background: #4a5568;
            color: white;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-bulk-print {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .btn-bulk-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-bulk-print:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin: 0;
        }
        
        .table th {
            background: #f7fafc;
            border: none;
            font-weight: 600;
            color: #2d3748;
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f7fafc;
        }
        
        .table tbody tr:hover {
            background: #f7fafc;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef5e7;
            color: #744210;
        }
        
        .status-paid {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status-partially-paid {
            background: #bee3f8;
            color: #2c5282;
        }
        
        .status-overdue {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .bill-type-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .bill-business {
            background: #e6fffa;
            color: #234e52;
        }
        
        .bill-property {
            background: #ebf8ff;
            color: #2c5282;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .btn-print {
            background: #667eea;
            color: white;
        }
        
        .btn-print:hover {
            background: #5a6fd8;
            color: white;
        }
        
        .btn-view {
            background: #4299e1;
            color: white;
        }
        
        .btn-view:hover {
            background: #3182ce;
            color: white;
        }
        
        .pagination-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .pagination {
            margin: 0;
            justify-content: center;
        }
        
        .page-link {
            border: none;
            padding: 10px 15px;
            margin: 0 2px;
            border-radius: 8px;
            color: #667eea;
            font-weight: 600;
        }
        
        .page-link:hover {
            background: #f0f3ff;
            color: #5a6fd8;
        }
        
        .page-item.active .page-link {
            background: #667eea;
            color: white;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        
        .no-data i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .bill-select {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .select-all {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .page-header,
            .filters-container,
            .table-container,
            .pagination-container {
                margin: 0 -15px 30px -15px;
                border-radius: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .table-actions {
                justify-content: center;
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <?php require_once '../header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php require_once '../sidebar.php'; ?>
            
            <div class="main-content">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item">Billing</li>
                        <li class="breadcrumb-item active">Print Bills</li>
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-print text-primary"></i>
                            Print Bills
                        </h1>
                        <p class="page-subtitle">Print individual bills or bulk print multiple bills as PDF</p>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (getFlashMessages()): ?>
                    <?php $flash = getFlashMessages(); ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <?php echo htmlspecialchars($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div class="stat-title">Total Bills (<?php echo $billing_year; ?>)</div>
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['total_bills']); ?></div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-title">Pending Bills</div>
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['pending_bills']); ?></div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-title">Paid Bills</div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['paid_bills']); ?></div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-header">
                            <div class="stat-title">Partial Payments</div>
                            <div class="stat-icon">
                                <i class="fas fa-adjust"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['partial_bills']); ?></div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-header">
                            <div class="stat-title">Total Amount</div>
                            <div class="stat-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo formatCurrency($stats['total_amount']); ?></div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-container">
                    <h5 class="mb-3">
                        <i class="fas fa-filter text-primary"></i>
                        Search & Filter Bills
                    </h5>
                    
                    <form method="GET" class="filter-form">
                        <div class="filter-row">
                            <div class="form-group">
                                <label class="form-label">Search</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Search bill number, account name...">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Bill Type</label>
                                <select class="form-control" name="bill_type">
                                    <option value="">All Types</option>
                                    <option value="Business" <?php echo $bill_type === 'Business' ? 'selected' : ''; ?>>Business</option>
                                    <option value="Property" <?php echo $bill_type === 'Property' ? 'selected' : ''; ?>>Property</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Paid" <?php echo $status_filter === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="Partially Paid" <?php echo $status_filter === 'Partially Paid' ? 'selected' : ''; ?>>Partially Paid</option>
                                    <option value="Overdue" <?php echo $status_filter === 'Overdue' ? 'selected' : ''; ?>>Overdue</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Zone</label>
                                <select class="form-control" name="zone">
                                    <option value="">All Zones</option>
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?php echo $zone['zone_id']; ?>" 
                                                <?php echo $zone_filter == $zone['zone_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($zone['zone_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Billing Year</label>
                                <select class="form-control" name="billing_year">
                                    <?php for ($year = date('Y') + 1; $year >= 2020; $year--): ?>
                                        <option value="<?php echo $year; ?>" 
                                                <?php echo $billing_year == $year ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn-filter">
                                    <i class="fas fa-search"></i>
                                    Filter
                                </button>
                            </div>
                            
                            <div class="form-group">
                                <a href="print.php" class="btn-clear">
                                    <i class="fas fa-times"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bills Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h5 class="table-title">
                            Bills List 
                            <?php if ($totalRecords > 0): ?>
                                <span class="text-muted">
                                    (<?php echo number_format($totalRecords); ?> 
                                    <?php echo $totalRecords === 1 ? 'bill' : 'bills'; ?>)
                                </span>
                            <?php endif; ?>
                        </h5>
                        <div class="table-actions">
                            <button type="button" class="btn-bulk-print" onclick="printSelected()" disabled id="bulkPrintBtn">
                                <i class="fas fa-print"></i>
                                Print Selected
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($bills)): ?>
                    <form method="POST" id="bulkForm">
                        <input type="hidden" name="bulk_print" value="1">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="select-all" onchange="toggleAll(this)">
                                        </th>
                                        <th>Bill Number</th>
                                        <th>Account Details</th>
                                        <th>Type</th>
                                        <th>Billing Year</th>
                                        <th>Amount Payable</th>
                                        <th>Status</th>
                                        <th>Generated By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bills as $bill): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                   class="bill-select" 
                                                   name="selected_bills[]" 
                                                   value="<?php echo $bill['bill_id']; ?>"
                                                   onchange="updateBulkButton()">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($bill['bill_number']); ?></strong>
                                            <br><small class="text-muted">
                                                <?php echo formatDateTime($bill['generated_at']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($bill['account_name']); ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($bill['account_number']); ?>
                                            </small>
                                            <?php if (!empty($bill['contact_info'])): ?>
                                                <br><small class="text-muted">
                                                    <?php echo htmlspecialchars($bill['contact_info']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="bill-type-badge bill-<?php echo strtolower($bill['bill_type']); ?>">
                                                <?php echo htmlspecialchars($bill['bill_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($bill['billing_year']); ?></td>
                                        <td>
                                            <strong class="<?php echo $bill['amount_payable'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo formatCurrency($bill['amount_payable']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $bill['status'])); ?>">
                                                <?php echo htmlspecialchars($bill['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(trim($bill['first_name'] . ' ' . $bill['last_name'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?print_bill=1&bill_id=<?php echo $bill['bill_id']; ?>" 
                                                   class="btn-action btn-print" 
                                                   title="Print Bill"
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn-action btn-view" 
                                                        title="Preview Bill"
                                                        onclick="previewBill(<?php echo $bill['bill_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-file-invoice"></i>
                        <h5>No bills found</h5>
                        <p>No bills match your current filter criteria.</p>
                        <a href="generate.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Generate Bills
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Bills pagination">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?> 
                            (<?php echo number_format($totalRecords); ?> total bills)
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle all checkboxes
        function toggleAll(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.bill-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkButton();
        }

        // Update bulk print button state
        function updateBulkButton() {
            const checkboxes = document.querySelectorAll('.bill-select:checked');
            const bulkBtn = document.getElementById('bulkPrintBtn');
            
            if (checkboxes.length > 0) {
                bulkBtn.disabled = false;
                bulkBtn.innerHTML = `<i class="fas fa-print"></i> Print Selected (${checkboxes.length})`;
            } else {
                bulkBtn.disabled = true;
                bulkBtn.innerHTML = '<i class="fas fa-print"></i> Print Selected';
            }
        }

        // Print selected bills
        function printSelected() {
            const checkboxes = document.querySelectorAll('.bill-select:checked');
            
            if (checkboxes.length === 0) {
                alert('Please select at least one bill to print.');
                return;
            }
            
            if (confirm(`Print ${checkboxes.length} selected bill(s) as PDF?`)) {
                document.getElementById('bulkForm').submit();
            }
        }

        // Preview bill (can be implemented with modal or new window)
        function previewBill(billId) {
            // For now, we'll open the print version in a new window
            window.open(`?print_bill=1&bill_id=${billId}`, '_blank');
        }

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

        // Add loading state to filter form
        document.querySelector('.filter-form')?.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.btn-filter');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtering...';
            submitBtn.disabled = true;
        });

        // Quick search functionality
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        this.form.submit();
                    }
                }, 1000);
            });
        }
    </script>
</body>
</html>