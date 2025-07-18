 <?php
/**
 * Search Accounts Page for QUICKBILL 305
 * Revenue Officer interface for quick account searches
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

// Check if user is revenue officer or admin
$currentUser = getCurrentUser();
if (!isRevenueOfficer() && !isAdmin()) {
    setFlashMessage('error', 'Access denied. Revenue Officer privileges required.');
    header('Location: ../../auth/login.php');
    exit();
}

$userDisplayName = getUserDisplayName($currentUser);

// Initialize variables
$searchTerm = '';
$searchType = 'all';
$searchResults = [];
$error = '';
$searchPerformed = false;

// Database connection
try {
    $db = new Database();
} catch (Exception $e) {
    $error = 'Database connection failed. Please try again.';
}

// Handle search request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    if (!verifyCsrfToken()) {
        $error = 'Security validation failed. Please try again.';
    } else {
        $searchTerm = sanitizeInput($_POST['search_term'] ?? '');
        $searchType = sanitizeInput($_POST['search_type'] ?? 'all');
        
        if (empty($searchTerm)) {
            $error = 'Please enter a search term.';
        } else {
            $searchPerformed = true;
            try {
                $searchPattern = "%{$searchTerm}%";
                $searchResults = [];
                
                // Search businesses
                if ($searchType === 'all' || $searchType === 'business') {
                    $businessQuery = "
                        SELECT 'business' as type, business_id as id, account_number, business_name as name, 
                               owner_name, telephone, amount_payable, exact_location as location, status,
                               business_type, category, zone_id, sub_zone_id, created_at
                        FROM businesses 
                        WHERE (account_number LIKE ? OR business_name LIKE ? OR owner_name LIKE ? OR telephone LIKE ?)
                        AND status = 'Active'
                        ORDER BY business_name
                    ";
                    
                    $businesses = $db->fetchAll($businessQuery, [$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
                    if ($businesses !== false && is_array($businesses)) {
                        $searchResults = array_merge($searchResults, $businesses);
                    }
                }
                
                // Search properties
                if ($searchType === 'all' || $searchType === 'property') {
                    $propertyQuery = "
                        SELECT 'property' as type, property_id as id, property_number as account_number, 
                               owner_name as name, owner_name, telephone, amount_payable, location, 'Active' as status,
                               structure, property_use, number_of_rooms, zone_id, created_at
                        FROM properties 
                        WHERE (property_number LIKE ? OR owner_name LIKE ? OR telephone LIKE ?)
                        ORDER BY owner_name
                    ";
                    
                    $properties = $db->fetchAll($propertyQuery, [$searchPattern, $searchPattern, $searchPattern]);
                    if ($properties !== false && is_array($properties)) {
                        $searchResults = array_merge($searchResults, $properties);
                    }
                }
                
                // Get zone names for results
                if (!empty($searchResults)) {
                    $zoneIds = array_unique(array_column($searchResults, 'zone_id'));
                    $zoneIds = array_filter($zoneIds); // Remove null values
                    
                    if (!empty($zoneIds)) {
                        $zonePlaceholders = str_repeat('?,', count($zoneIds) - 1) . '?';
                        $zoneQuery = "SELECT zone_id, zone_name FROM zones WHERE zone_id IN ($zonePlaceholders)";
                        $zones = $db->fetchAll($zoneQuery, $zoneIds);
                        $zoneMap = [];
                        if ($zones !== false) {
                            foreach ($zones as $zone) {
                                $zoneMap[$zone['zone_id']] = $zone['zone_name'];
                            }
                        }
                        
                        // Add zone names to results
                        foreach ($searchResults as &$result) {
                            $result['zone_name'] = $zoneMap[$result['zone_id']] ?? 'Unknown';
                        }
                    }
                }
                
            } catch (Exception $e) {
                $error = 'Search failed. Please try again.';
            }
        }
    }
}

// Get account details for modal display
$accountDetails = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $viewData = explode(':', $_GET['view']);
    if (count($viewData) === 2) {
        $accountType = $viewData[0];
        $accountId = intval($viewData[1]);
        
        try {
            if ($accountType === 'business') {
                $accountDetails = $db->fetchRow("
                    SELECT b.*, z.zone_name, sz.sub_zone_name,
                           (SELECT COUNT(*) FROM bills WHERE bill_type = 'Business' AND reference_id = b.business_id) as total_bills,
                           (SELECT COUNT(*) FROM payments p JOIN bills bl ON p.bill_id = bl.bill_id 
                            WHERE bl.bill_type = 'Business' AND bl.reference_id = b.business_id) as total_payments
                    FROM businesses b
                    LEFT JOIN zones z ON b.zone_id = z.zone_id
                    LEFT JOIN sub_zones sz ON b.sub_zone_id = sz.sub_zone_id
                    WHERE b.business_id = ? AND b.status = 'Active'
                ", [$accountId]);
                $accountDetails['type'] = 'business';
            } else {
                $accountDetails = $db->fetchRow("
                    SELECT p.*, z.zone_name,
                           (SELECT COUNT(*) FROM bills WHERE bill_type = 'Property' AND reference_id = p.property_id) as total_bills,
                           (SELECT COUNT(*) FROM payments py JOIN bills bl ON py.bill_id = bl.bill_id 
                            WHERE bl.bill_type = 'Property' AND bl.reference_id = p.property_id) as total_payments
                    FROM properties p
                    LEFT JOIN zones z ON p.zone_id = z.zone_id
                    WHERE p.property_id = ?
                ", [$accountId]);
                $accountDetails['type'] = 'property';
            }
        } catch (Exception $e) {
            // Ignore errors for modal display
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Accounts - <?php echo APP_NAME; ?></title>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #2d3748;
        }
        
        /* Custom Icons */
        .icon-search::before { content: "🔍"; }
        .icon-money::before { content: "💰"; }
        .icon-building::before { content: "🏢"; }
        .icon-home::before { content: "🏠"; }
        .icon-phone::before { content: "📞"; }
        .icon-location::before { content: "📍"; }
        .icon-back::before { content: "←"; }
        .icon-eye::before { content: "👁️"; }
        .icon-filter::before { content: "🔧"; }
        .icon-info::before { content: "ℹ️"; }
        .icon-warning::before { content: "⚠️"; }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-title h1 {
            font-size: 28px;
            font-weight: 700;
        }
        
        .header-icon {
            font-size: 32px;
            opacity: 0.9;
        }
        
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }
        
        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .search-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .search-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .search-title h2 {
            color: #2d3748;
            font-size: 24px;
            font-weight: 600;
        }
        
        .search-stats {
            color: #718096;
            font-size: 14px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
            align-items: end;
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
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
        }
        
        .form-control select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            height: fit-content;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Search Results */
        .results-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .results-title {
            color: #2d3748;
            font-size: 20px;
            font-weight: 600;
        }
        
        .results-count {
            background: #e53e3e;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .results-table th {
            background: #f7fafc;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
        }
        
        .results-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        
        .results-table tr:hover {
            background: #f7fafc;
        }
        
        .account-type-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-business {
            background: #e6fffa;
            color: #38a169;
        }
        
        .badge-property {
            background: #ebf8ff;
            color: #4299e1;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-paid {
            background: #c6f6d5;
            color: #276749;
        }
        
        .status-pending {
            background: #fed7d7;
            color: #c53030;
        }
        
        .amount-highlight {
            color: #e53e3e;
            font-weight: 700;
            font-size: 16px;
        }
        
        .amount-paid {
            color: #38a169;
            font-weight: 600;
        }
        
        .action-btn {
            background: #4299e1;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .action-btn:hover {
            background: #3182ce;
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
        }
        
        .action-btn.payment {
            background: #38a169;
        }
        
        .action-btn.payment:hover {
            background: #2f855a;
        }
        
        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        
        .no-results-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-results h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2d3748;
        }
        
        .no-results p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Search Instructions */
        .search-instructions {
            background: #f7fafc;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 10px 10px 0;
        }
        
        .search-instructions h4 {
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-instructions ul {
            margin-left: 20px;
            color: #718096;
        }
        
        .search-instructions li {
            margin-bottom: 5px;
        }
        
        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: #fed7d7;
            border: 1px solid #fc8181;
            color: #c53030;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            padding: 25px 30px 20px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #718096;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .modal-close:hover {
            color: #e53e3e;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .account-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-label {
            font-weight: 600;
            color: #718096;
            font-size: 14px;
        }
        
        .info-value {
            font-size: 16px;
            color: #2d3748;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .search-form {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .results-table {
                font-size: 14px;
            }
            
            .results-table th,
            .results-table td {
                padding: 10px 8px;
            }
            
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .account-info-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-search"></i>
                    <span class="icon-search" style="display: none;"></span>
                </div>
                <h1>Search Accounts</h1>
            </div>
            <a href="../index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span class="icon-back" style="display: none;"></span>
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="main-container">
        <!-- Alert -->
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="icon-warning" style="display: none;"></span>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Search Instructions -->
        <div class="search-instructions fade-in">
            <h4>
                <i class="fas fa-info-circle"></i>
                <span class="icon-info" style="display: none;"></span>
                Search Instructions
            </h4>
            <ul>
                <li><strong>Account Number:</strong> Enter exact account number (e.g., BIZ000001, PROP000001)</li>
                <li><strong>Name:</strong> Search by business name or property owner name</li>
                <li><strong>Phone:</strong> Search by contact telephone number</li>
                <li><strong>Filter:</strong> Choose to search all accounts, businesses only, or properties only</li>
            </ul>
        </div>

        <!-- Search Section -->
        <div class="search-section fade-in">
            <div class="search-header">
                <div class="search-title">
                    <i class="fas fa-search" style="color: #e53e3e; font-size: 24px;"></i>
                    <span class="icon-search" style="display: none; font-size: 24px;"></span>
                    <h2>Search Accounts</h2>
                </div>
                <?php if ($searchPerformed): ?>
                    <div class="search-stats">
                        <?php echo count($searchResults); ?> result(s) found for "<?php echo htmlspecialchars($searchTerm); ?>"
                    </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" class="search-form">
                <?php echo csrfField(); ?>
                <input type="hidden" name="action" value="search">
                
                <div class="form-group">
                    <label for="search_term" class="form-label">Search Term</label>
                    <input type="text" 
                           class="form-control" 
                           id="search_term" 
                           name="search_term"
                           value="<?php echo htmlspecialchars($searchTerm); ?>"
                           placeholder="Enter account number, business/property name, or phone number"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="search_type" class="form-label">Filter</label>
                    <select class="form-control" id="search_type" name="search_type">
                        <option value="all" <?php echo $searchType === 'all' ? 'selected' : ''; ?>>All Accounts</option>
                        <option value="business" <?php echo $searchType === 'business' ? 'selected' : ''; ?>>Businesses Only</option>
                        <option value="property" <?php echo $searchType === 'property' ? 'selected' : ''; ?>>Properties Only</option>
                    </select>
                </div>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    <span class="icon-search" style="display: none;"></span>
                    Search
                </button>
            </form>
        </div>

        <!-- Search Results -->
        <?php if ($searchPerformed): ?>
            <div class="results-section fade-in">
                <?php if (!empty($searchResults)): ?>
                    <div class="results-header">
                        <h3 class="results-title">Search Results</h3>
                        <span class="results-count"><?php echo count($searchResults); ?> found</span>
                    </div>
                    
                    <div style="overflow-x: auto;">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Account Number</th>
                                    <th>Name/Owner</th>
                                    <th>Phone</th>
                                    <th>Zone</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result): ?>
                                    <tr>
                                        <td>
                                            <span class="account-type-badge <?php echo $result['type'] === 'business' ? 'badge-business' : 'badge-property'; ?>">
                                                <i class="fas <?php echo $result['type'] === 'business' ? 'fa-building' : 'fa-home'; ?>"></i>
                                                <span class="<?php echo $result['type'] === 'business' ? 'icon-building' : 'icon-home'; ?>" style="display: none;"></span>
                                                <?php echo ucfirst($result['type']); ?>
                                            </span>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($result['account_number']); ?></strong></td>
                                        <td>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($result['name']); ?></div>
                                            <div style="font-size: 12px; color: #718096;"><?php echo htmlspecialchars($result['owner_name']); ?></div>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone" style="color: #718096; margin-right: 5px;"></i>
                                            <span class="icon-phone" style="display: none;"></span>
                                            <?php echo htmlspecialchars($result['telephone'] ?: 'N/A'); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt" style="color: #718096; margin-right: 5px;"></i>
                                            <span class="icon-location" style="display: none;"></span>
                                            <?php echo htmlspecialchars($result['zone_name'] ?? 'Unknown'); ?>
                                        </td>
                                        <td>
                                            <?php if ($result['amount_payable'] > 0): ?>
                                                <span class="amount-highlight"><?php echo formatCurrency($result['amount_payable']); ?></span>
                                            <?php else: ?>
                                                <span class="amount-paid">Paid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $result['amount_payable'] > 0 ? 'status-pending' : 'status-paid'; ?>">
                                                <?php echo $result['amount_payable'] > 0 ? 'Pending' : 'Paid'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
                                                <a href="?view=<?php echo $result['type']; ?>:<?php echo $result['id']; ?>" 
                                                   class="action-btn" 
                                                   onclick="showAccountDetails(event, '<?php echo $result['type']; ?>', <?php echo $result['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="icon-eye" style="display: none;"></span>
                                                    View
                                                </a>
                                                <?php if ($result['amount_payable'] > 0): ?>
                                                    <a href="record.php?account=<?php echo $result['type']; ?>:<?php echo $result['id']; ?>" 
                                                       class="action-btn payment">
                                                        <i class="fas fa-cash-register"></i>
                                                        <span class="icon-money" style="display: none;"></span>
                                                        Pay
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                            <span class="icon-search" style="display: none;"></span>
                        </div>
                        <h3>No accounts found</h3>
                        <p>No accounts match your search criteria. Try using different search terms or check your spelling.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Account Details Modal -->
    <div class="modal" id="accountModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">
                    <i class="fas fa-building"></i>
                    <span class="icon-building" style="display: none;"></span>
                    Account Details
                </h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script>
        // Check if Font Awesome loaded, if not show emoji icons
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const testIcon = document.querySelector('.fas.fa-search');
                if (!testIcon || getComputedStyle(testIcon, ':before').content === 'none') {
                    document.querySelectorAll('.fas, .far').forEach(function(icon) {
                        icon.style.display = 'none';
                    });
                    document.querySelectorAll('[class*="icon-"]').forEach(function(emoji) {
                        emoji.style.display = 'inline';
                    });
                }
            }, 100);

            // Auto-focus search field
            const searchField = document.getElementById('search_term');
            if (searchField) {
                searchField.focus();
            }

            // Show modal if URL has view parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('view')) {
                const viewParam = urlParams.get('view');
                const [type, id] = viewParam.split(':');
                if (type && id) {
                    showAccountDetails(null, type, id);
                }
            }
        });

        // Show account details modal
        function showAccountDetails(event, type, id) {
            if (event) {
                event.preventDefault();
            }

            const modal = document.getElementById('accountModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');

            // Update title icon
            const titleIcon = modalTitle.querySelector('i');
            const titleEmojiIcon = modalTitle.querySelector('span');
            if (titleIcon) {
                titleIcon.className = type === 'business' ? 'fas fa-building' : 'fas fa-home';
            }
            if (titleEmojiIcon) {
                titleEmojiIcon.className = type === 'business' ? 'icon-building' : 'icon-home';
            }

            // Show loading state
            modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #718096;"></i><p style="margin-top: 15px; color: #718096;">Loading account details...</p></div>';
            
            modal.classList.add('show');

            // Fetch account details (in a real app, this would be an AJAX call)
            setTimeout(() => {
                <?php if ($accountDetails): ?>
                    const accountData = <?php echo json_encode($accountDetails); ?>;
                    displayAccountDetails(accountData);
                <?php else: ?>
                    modalBody.innerHTML = '<div style="text-align: center; padding: 40px; color: #e53e3e;"><i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i><p style="margin-top: 15px;">Failed to load account details.</p></div>';
                <?php endif; ?>
            }, 500);
        }

        // Display account details in modal
        function displayAccountDetails(data) {
            const modalBody = document.getElementById('modalBody');
            
            let detailsHtml = `
                <div class="account-info-grid">
                    <div class="info-item">
                        <span class="info-label">Account Number</span>
                        <span class="info-value">${data.account_number || data.property_number}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Owner Name</span>
                        <span class="info-value">${data.owner_name}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">${data.telephone || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Zone</span>
                        <span class="info-value">${data.zone_name || 'Unknown'}</span>
                    </div>
            `;

            if (data.type === 'business') {
                detailsHtml += `
                    <div class="info-item">
                        <span class="info-label">Business Name</span>
                        <span class="info-value">${data.business_name}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Business Type</span>
                        <span class="info-value">${data.business_type}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category</span>
                        <span class="info-value">${data.category}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sub Zone</span>
                        <span class="info-value">${data.sub_zone_name || 'N/A'}</span>
                    </div>
                `;
            } else {
                detailsHtml += `
                    <div class="info-item">
                        <span class="info-label">Structure</span>
                        <span class="info-value">${data.structure}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Property Use</span>
                        <span class="info-value">${data.property_use}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Number of Rooms</span>
                        <span class="info-value">${data.number_of_rooms}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ownership Type</span>
                        <span class="info-value">${data.ownership_type}</span>
                    </div>
                `;
            }

            detailsHtml += `
                    <div class="info-item">
                        <span class="info-label">Location</span>
                        <span class="info-value">${data.exact_location || data.location || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Bills</span>
                        <span class="info-value">${data.total_bills || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Payments</span>
                        <span class="info-value">${data.total_payments || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Amount Payable</span>
                        <span class="info-value amount-highlight">GH₵ ${parseFloat(data.amount_payable || 0).toFixed(2)}</span>
                    </div>
                </div>
            `;

            modalBody.innerHTML = detailsHtml;
        }

        // Close modal
        function closeModal() {
            const modal = document.getElementById('accountModal');
            modal.classList.remove('show');
            
            // Clear URL parameter
            const url = new URL(window.location);
            url.searchParams.delete('view');
            window.history.replaceState({}, document.title, url.toString());
        }

        // Close modal when clicking outside
        document.getElementById('accountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape to close modal
            if (e.key === 'Escape') {
                closeModal();
            }
            
            // Ctrl + F to focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search_term').focus();
            }
        });
    </script>
</body>
</html>
