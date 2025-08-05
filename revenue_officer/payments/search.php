<<<<<<< HEAD
 <?php
/**
 * Search Accounts Page for QUICKBILL 305
 * Revenue Officer interface for quick account searches
=======
<?php
/**
 * Search Accounts Page for QUICKBILL 305
 * Revenue Officer interface for quick account searches
 * Updated with outstanding balance calculation and improved error handling
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
$userDisplayName = getUserDisplayName($currentUser);

// Initialize variables
$searchTerm = '';
$searchType = 'all';
$searchResults = [];
$error = '';
$searchPerformed = false;
<<<<<<< HEAD
=======
$debugMode = isset($_GET['debug']) && $_GET['debug'] == '1';
$debugInfo = '';
>>>>>>> c9ccaba (Initial commit)

// Database connection
try {
    $db = new Database();
<<<<<<< HEAD
} catch (Exception $e) {
    $error = 'Database connection failed. Please try again.';
=======
    if ($debugMode) {
        $debugInfo .= "Database connection successful. ";
    }
} catch (Exception $e) {
    $error = 'Database connection failed. Please try again.';
    if ($debugMode) {
        $debugInfo .= "Database connection error: " . $e->getMessage() . ". ";
    }
}

// Function to calculate remaining balance for an account
function calculateRemainingBalance($db, $accountType, $accountId, $amountPayable) {
    try {
        $totalPaymentsQuery = "SELECT COALESCE(SUM(p.amount_paid), 0) as total_paid
                              FROM payments p 
                              INNER JOIN bills b ON p.bill_id = b.bill_id 
                              WHERE b.bill_type = ? AND b.reference_id = ? 
                              AND p.payment_status = 'Successful'";
        $totalPaymentsResult = $db->fetchRow($totalPaymentsQuery, [ucfirst($accountType), $accountId]);
        $totalPaid = $totalPaymentsResult['total_paid'] ?? 0;
        
        return [
            'remaining_balance' => max(0, $amountPayable - $totalPaid),
            'total_paid' => $totalPaid,
            'amount_payable' => $amountPayable
        ];
    } catch (Exception $e) {
        return [
            'remaining_balance' => $amountPayable,
            'total_paid' => 0,
            'amount_payable' => $amountPayable
        ];
    }
>>>>>>> c9ccaba (Initial commit)
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
                
<<<<<<< HEAD
=======
                if ($debugMode) {
                    $debugInfo .= "Searching for '{$searchTerm}' in '{$searchType}' accounts. ";
                }
                
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        $searchResults = array_merge($searchResults, $businesses);
=======
                        // Calculate remaining balance for each business
                        foreach ($businesses as &$business) {
                            $balanceInfo = calculateRemainingBalance($db, 'business', $business['id'], $business['amount_payable']);
                            $business['remaining_balance'] = $balanceInfo['remaining_balance'];
                            $business['total_paid'] = $balanceInfo['total_paid'];
                        }
                        $searchResults = array_merge($searchResults, $businesses);
                        if ($debugMode) {
                            $debugInfo .= "Found " . count($businesses) . " businesses with balance calculations. ";
                        }
                    } else {
                        if ($debugMode) {
                            $debugInfo .= "No businesses found or query failed. ";
                        }
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        $searchResults = array_merge($searchResults, $properties);
=======
                        // Calculate remaining balance for each property
                        foreach ($properties as &$property) {
                            $balanceInfo = calculateRemainingBalance($db, 'property', $property['id'], $property['amount_payable']);
                            $property['remaining_balance'] = $balanceInfo['remaining_balance'];
                            $property['total_paid'] = $balanceInfo['total_paid'];
                        }
                        $searchResults = array_merge($searchResults, $properties);
                        if ($debugMode) {
                            $debugInfo .= "Found " . count($properties) . " properties with balance calculations. ";
                        }
                    } else {
                        if ($debugMode) {
                            $debugInfo .= "No properties found or query failed. ";
                        }
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                    }
                }
                
            } catch (Exception $e) {
                $error = 'Search failed. Please try again.';
=======
                        
                        if ($debugMode) {
                            $debugInfo .= "Added zone names to " . count($searchResults) . " results. ";
                        }
                    }
                }
                
                if ($debugMode) {
                    $debugInfo .= "Total results: " . count($searchResults) . ". ";
                }
                
            } catch (Exception $e) {
                $error = 'Search failed. Please try again.';
                if ($debugMode) {
                    $debugInfo .= "Search error: " . $e->getMessage() . ". ";
                }
                error_log("Search error: " . $e->getMessage());
>>>>>>> c9ccaba (Initial commit)
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
        
<<<<<<< HEAD
=======
        if ($debugMode) {
            $debugInfo .= "Fetching {$accountType} details for ID {$accountId}. ";
        }
        
>>>>>>> c9ccaba (Initial commit)
        try {
            if ($accountType === 'business') {
                $accountDetails = $db->fetchRow("
                    SELECT b.*, z.zone_name, sz.sub_zone_name,
                           (SELECT COUNT(*) FROM bills WHERE bill_type = 'Business' AND reference_id = b.business_id) as total_bills,
                           (SELECT COUNT(*) FROM payments p JOIN bills bl ON p.bill_id = bl.bill_id 
<<<<<<< HEAD
=======
                            WHERE bl.bill_type = 'Business' AND bl.reference_id = b.business_id AND p.payment_status = 'Successful') as successful_payments,
                           (SELECT COUNT(*) FROM payments p JOIN bills bl ON p.bill_id = bl.bill_id 
>>>>>>> c9ccaba (Initial commit)
                            WHERE bl.bill_type = 'Business' AND bl.reference_id = b.business_id) as total_payments
                    FROM businesses b
                    LEFT JOIN zones z ON b.zone_id = z.zone_id
                    LEFT JOIN sub_zones sz ON b.sub_zone_id = sz.sub_zone_id
<<<<<<< HEAD
                    WHERE b.business_id = ? AND b.status = 'Active'
                ", [$accountId]);
                $accountDetails['type'] = 'business';
            } else {
=======
                    WHERE b.business_id = ?
                ", [$accountId]);
                
                if ($accountDetails !== false && $accountDetails !== null && !empty($accountDetails)) {
                    $accountDetails['type'] = 'business';
                    // Calculate detailed balance information
                    $balanceInfo = calculateRemainingBalance($db, 'business', $accountId, $accountDetails['amount_payable']);
                    $accountDetails['remaining_balance'] = $balanceInfo['remaining_balance'];
                    $accountDetails['total_paid'] = $balanceInfo['total_paid'];
                    $accountDetails['payment_progress'] = $accountDetails['amount_payable'] > 0 ? 
                        ($balanceInfo['total_paid'] / $accountDetails['amount_payable']) * 100 : 100;
                    
                    if ($debugMode) {
                        $debugInfo .= "Business found: " . $accountDetails['business_name'] . 
                                     ". Remaining balance: " . $accountDetails['remaining_balance'] . ". ";
                    }
                } else {
                    $accountDetails = null;
                    if ($debugMode) {
                        $debugInfo .= "No business found with ID {$accountId}. ";
                    }
                }
                
            } elseif ($accountType === 'property') {
>>>>>>> c9ccaba (Initial commit)
                $accountDetails = $db->fetchRow("
                    SELECT p.*, z.zone_name,
                           (SELECT COUNT(*) FROM bills WHERE bill_type = 'Property' AND reference_id = p.property_id) as total_bills,
                           (SELECT COUNT(*) FROM payments py JOIN bills bl ON py.bill_id = bl.bill_id 
<<<<<<< HEAD
=======
                            WHERE bl.bill_type = 'Property' AND bl.reference_id = p.property_id AND py.payment_status = 'Successful') as successful_payments,
                           (SELECT COUNT(*) FROM payments py JOIN bills bl ON py.bill_id = bl.bill_id 
>>>>>>> c9ccaba (Initial commit)
                            WHERE bl.bill_type = 'Property' AND bl.reference_id = p.property_id) as total_payments
                    FROM properties p
                    LEFT JOIN zones z ON p.zone_id = z.zone_id
                    WHERE p.property_id = ?
                ", [$accountId]);
<<<<<<< HEAD
                $accountDetails['type'] = 'property';
            }
        } catch (Exception $e) {
            // Ignore errors for modal display
=======
                
                if ($accountDetails !== false && $accountDetails !== null && !empty($accountDetails)) {
                    $accountDetails['type'] = 'property';
                    // Calculate detailed balance information
                    $balanceInfo = calculateRemainingBalance($db, 'property', $accountId, $accountDetails['amount_payable']);
                    $accountDetails['remaining_balance'] = $balanceInfo['remaining_balance'];
                    $accountDetails['total_paid'] = $balanceInfo['total_paid'];
                    $accountDetails['payment_progress'] = $accountDetails['amount_payable'] > 0 ? 
                        ($balanceInfo['total_paid'] / $accountDetails['amount_payable']) * 100 : 100;
                    
                    if ($debugMode) {
                        $debugInfo .= "Property found: " . $accountDetails['owner_name'] . 
                                     ". Remaining balance: " . $accountDetails['remaining_balance'] . ". ";
                    }
                } else {
                    $accountDetails = null;
                    if ($debugMode) {
                        $debugInfo .= "No property found with ID {$accountId}. ";
                    }
                }
            } else {
                if ($debugMode) {
                    $debugInfo .= "Invalid account type: {$accountType}. ";
                }
            }
            
        } catch (Exception $e) {
            $accountDetails = null;
            if ($debugMode) {
                $debugInfo .= "Database error fetching account details: " . $e->getMessage() . ". ";
            }
            error_log("Account details fetch error: " . $e->getMessage());
        }
    } else {
        if ($debugMode) {
            $debugInfo .= "Invalid view parameter format. ";
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
        .icon-balance::before { content: "⚖️"; }
        .icon-check::before { content: "✅"; }
>>>>>>> c9ccaba (Initial commit)
        
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
        
<<<<<<< HEAD
=======
        /* Debug Info */
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-family: monospace;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-line;
            line-height: 1.4;
        }

        .debug-info strong {
            color: #cc7a00;
            font-weight: bold;
        }

        .debug-toggle {
            background: #ffc107;
            color: #212529;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .debug-toggle:hover {
            background: #e0a800;
            transform: translateY(-1px);
        }
        
>>>>>>> c9ccaba (Initial commit)
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
        
<<<<<<< HEAD
=======
        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }
        
>>>>>>> c9ccaba (Initial commit)
        .amount-highlight {
            color: #e53e3e;
            font-weight: 700;
            font-size: 16px;
        }
        
        .amount-paid {
            color: #38a169;
            font-weight: 600;
        }
        
<<<<<<< HEAD
=======
        .balance-display {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .balance-main {
            font-weight: 700;
            font-size: 16px;
        }
        
        .balance-detail {
            font-size: 11px;
            opacity: 0.8;
        }
        
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
            max-width: 800px;
=======
            max-width: 900px;
>>>>>>> c9ccaba (Initial commit)
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
        
<<<<<<< HEAD
=======
        /* Balance Highlight for Modal */
        .balance-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .balance-highlight.paid {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
        }
        
        .balance-highlight::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg) translate(-50%, -50%); }
            50% { transform: rotate(180deg) translate(-50%, -50%); }
        }
        
        .balance-highlight h4 {
            margin: 0 0 15px 0;
            font-size: 20px;
            color: #92400e;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .balance-highlight.paid h4 {
            color: #065f46;
        }
        
        .balance-amount {
            font-size: 36px;
            font-weight: bold;
            color: #92400e;
            margin: 15px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .balance-highlight.paid .balance-amount {
            color: #065f46;
        }
        
        .balance-subtitle {
            font-size: 14px;
            opacity: 0.8;
            color: #92400e;
        }
        
        .balance-highlight.paid .balance-subtitle {
            color: #065f46;
        }
        
        /* Payment Progress Bar */
        .payment-progress {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #4299e1;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .progress-bar-container {
            background: #e2e8f0;
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 1s ease;
            border-radius: 6px;
        }
        
        .progress-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .progress-stat {
            text-align: center;
        }
        
        .progress-stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
        }
        
        .progress-stat-label {
            font-size: 12px;
            color: #718096;
            margin-top: 2px;
        }
        
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
            
            .progress-stats {
                grid-template-columns: 1fr;
            }
            
            .balance-amount {
                font-size: 28px;
            }
>>>>>>> c9ccaba (Initial commit)
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
<<<<<<< HEAD
=======
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
        <!-- Debug Information -->
        <?php if ($debugMode && !empty($debugInfo)): ?>
            <button class="debug-toggle" onclick="toggleDebugInfo()">🔧 Hide Debug Info</button>
            <div class="debug-info fade-in" id="debugInfo" style="display: block;">
                <strong>🐛 Debug Information:</strong><br>
                <?php echo $debugInfo; ?>
            </div>
        <?php endif; ?>

>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
                <li><strong>Balance:</strong> Outstanding balance shows remaining amount after all successful payments</li>
                <?php if (!$debugMode): ?>
                <li><strong>Debug:</strong> Add ?debug=1 to URL for detailed debugging information</li>
                <?php endif; ?>
                <?php if ($debugMode): ?>
                <li><strong>Quick Tests:</strong> 
                    <a href="?view=business:6&debug=1" style="color: #e53e3e;">Test Business ID 6</a> | 
                    <a href="?view=property:4&debug=1" style="color: #e53e3e;">Test Property ID 4</a>
                </li>
                <?php endif; ?>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                                    <th>Amount Due</th>
=======
                                    <th>Outstanding Balance</th>
>>>>>>> c9ccaba (Initial commit)
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $result): ?>
<<<<<<< HEAD
=======
                                    <?php 
                                    $remainingBalance = $result['remaining_balance'] ?? $result['amount_payable'];
                                    $totalPaid = $result['total_paid'] ?? 0;
                                    $amountPayable = $result['amount_payable'];
                                    
                                    // Determine status based on remaining balance
                                    if ($remainingBalance <= 0) {
                                        $status = 'paid';
                                        $statusText = 'Paid';
                                        $statusClass = 'status-paid';
                                    } elseif ($totalPaid > 0) {
                                        $status = 'partial';
                                        $statusText = 'Partial';
                                        $statusClass = 'status-partial';
                                    } else {
                                        $status = 'pending';
                                        $statusText = 'Pending';
                                        $statusClass = 'status-pending';
                                    }
                                    ?>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                                            <?php if ($result['amount_payable'] > 0): ?>
                                                <span class="amount-highlight"><?php echo formatCurrency($result['amount_payable']); ?></span>
                                            <?php else: ?>
                                                <span class="amount-paid">Paid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $result['amount_payable'] > 0 ? 'status-pending' : 'status-paid'; ?>">
                                                <?php echo $result['amount_payable'] > 0 ? 'Pending' : 'Paid'; ?>
=======
                                            <div class="balance-display">
                                                <?php if ($remainingBalance > 0): ?>
                                                    <span class="balance-main amount-highlight">
                                                        <?php echo formatCurrency($remainingBalance); ?>
                                                    </span>
                                                    <?php if ($totalPaid > 0): ?>
                                                        <span class="balance-detail" style="color: #38a169;">
                                                            Paid: <?php echo formatCurrency($totalPaid); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="balance-main amount-paid">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span class="icon-check" style="display: none;"></span>
                                                        Fully Paid
                                                    </span>
                                                    <span class="balance-detail" style="color: #38a169;">
                                                        Total: <?php echo formatCurrency($totalPaid); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
>>>>>>> c9ccaba (Initial commit)
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 5px;">
<<<<<<< HEAD
                                                <a href="?view=<?php echo $result['type']; ?>:<?php echo $result['id']; ?>" 
=======
                                                <a href="?view=<?php echo $result['type']; ?>:<?php echo $result['id']; ?><?php echo $debugMode ? '&debug=1' : ''; ?>" 
>>>>>>> c9ccaba (Initial commit)
                                                   class="action-btn" 
                                                   onclick="showAccountDetails(event, '<?php echo $result['type']; ?>', <?php echo $result['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="icon-eye" style="display: none;"></span>
                                                    View
                                                </a>
<<<<<<< HEAD
                                                <?php if ($result['amount_payable'] > 0): ?>
=======
                                                <?php if ($remainingBalance > 0): ?>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
=======
                        <?php if ($debugMode): ?>
                            <p style="margin-top: 15px; font-size: 14px; color: #718096;">
                                Debug information is shown above to help troubleshoot the issue.
                            </p>
                        <?php endif; ?>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                    showAccountDetails(null, type, id);
=======
                    showAccountDetails(null, type, parseInt(id));
>>>>>>> c9ccaba (Initial commit)
                }
            }
        });

        // Show account details modal
        function showAccountDetails(event, type, id) {
<<<<<<< HEAD
            if (event) {
                event.preventDefault();
            }

=======
            // Don't prevent default - let the page reload with the view parameter
            // This way PHP can fetch the account details properly
            if (event) {
                // Add debug parameter if it exists in current URL
                const currentUrl = new URL(window.location);
                const debugParam = currentUrl.searchParams.get('debug');
                
                let targetUrl = `?view=${type}:${id}`;
                if (debugParam) {
                    targetUrl += `&debug=${debugParam}`;
                }
                
                // Navigate to the URL with view parameter
                window.location.href = targetUrl;
                return;
            }

            // This code runs when the page loads with a view parameter
>>>>>>> c9ccaba (Initial commit)
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

<<<<<<< HEAD
            // Fetch account details (in a real app, this would be an AJAX call)
            setTimeout(() => {
                <?php if ($accountDetails): ?>
                    const accountData = <?php echo json_encode($accountDetails); ?>;
                    displayAccountDetails(accountData);
                <?php else: ?>
                    modalBody.innerHTML = '<div style="text-align: center; padding: 40px; color: #e53e3e;"><i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i><p style="margin-top: 15px;">Failed to load account details.</p></div>';
                <?php endif; ?>
            }, 500);
=======
            // Check if we have account details from PHP
            <?php if ($accountDetails && !empty($accountDetails)): ?>
                // Account details found
                setTimeout(() => {
                    const accountData = <?php echo json_encode($accountDetails); ?>;
                    displayAccountDetails(accountData);
                }, 300);
            <?php else: ?>
                // No account details or error occurred
                setTimeout(() => {
                    modalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #e53e3e;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i>
                            <p style="margin-top: 15px; font-weight: 600;">Account not found</p>
                            <p style="margin-top: 10px; color: #718096; font-size: 14px;">
                                The ${type} account with ID ${id} could not be found or may have been deleted.
                                <br><br>
                                <strong>Debug Information:</strong><br>
                                <?php if ($debugMode): ?>
                                    Check the debug information above for detailed analysis.
                                <?php else: ?>
                                    • Add ?debug=1 to the URL for detailed debugging<br>
                                    • Verify the account number is correct<br>
                                    • Check if the account is active<br>
                                    • Contact administrator if the problem persists
                                <?php endif; ?>
                            </p>
                            <button onclick="closeModal()" style="margin-top: 20px; padding: 10px 20px; background: #e53e3e; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                Close
                            </button>
                        </div>
                    `;
                }, 300);
            <?php endif; ?>
>>>>>>> c9ccaba (Initial commit)
        }

        // Display account details in modal
        function displayAccountDetails(data) {
            const modalBody = document.getElementById('modalBody');
            
<<<<<<< HEAD
            let detailsHtml = `
                <div class="account-info-grid">
                    <div class="info-item">
                        <span class="info-label">Account Number</span>
                        <span class="info-value">${data.account_number || data.property_number}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Owner Name</span>
                        <span class="info-value">${data.owner_name}</span>
=======
            // Validate data
            if (!data || typeof data !== 'object') {
                modalBody.innerHTML = '<div style="text-align: center; padding: 40px; color: #e53e3e;"><i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i><p style="margin-top: 15px;">Invalid account data received.</p></div>';
                return;
            }
            
            const remainingBalance = data.remaining_balance || 0;
            const totalPaid = data.total_paid || 0;
            const amountPayable = data.amount_payable || 0;
            const paymentProgress = data.payment_progress || 0;
            
            let detailsHtml = `
                <!-- Outstanding Balance Highlight -->
                <div class="balance-highlight ${remainingBalance <= 0 ? 'paid' : ''} ${remainingBalance > 0 ? 'pulse' : ''}">
                    <h4>
                        <i class="fas fa-balance-scale"></i>
                        <span class="icon-balance" style="display: none;"></span>
                        ${remainingBalance <= 0 ? 'Account Fully Paid' : 'Outstanding Balance'}
                    </h4>
                    <div class="balance-amount">
                        ₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}
                    </div>
                    <div class="balance-subtitle">
                        ${remainingBalance > 0 ? 'This amount needs to be paid to clear the account' : '✅ All bills have been settled'}
                    </div>
                </div>
                
                <!-- Payment Progress -->
                <div class="payment-progress">
                    <div class="progress-header">
                        <h4 style="margin: 0; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-chart-line"></i>
                            <span class="icon-info" style="display: none;"></span>
                            Payment Summary
                        </h4>
                        <span style="font-weight: 600; color: #4299e1;">${paymentProgress.toFixed(1)}% Complete</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: ${Math.min(paymentProgress, 100)}%;"></div>
                    </div>
                    <div class="progress-stats">
                        <div class="progress-stat">
                            <div class="progress-stat-value">₵ ${amountPayable.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            <div class="progress-stat-label">Total Payable</div>
                        </div>
                        <div class="progress-stat">
                            <div class="progress-stat-value">₵ ${totalPaid.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            <div class="progress-stat-label">Total Paid</div>
                        </div>
                        <div class="progress-stat">
                            <div class="progress-stat-value">₵ ${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            <div class="progress-stat-label">Remaining</div>
                        </div>
                        <div class="progress-stat">
                            <div class="progress-stat-value">${data.successful_payments || 0}</div>
                            <div class="progress-stat-label">Payments Made</div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Information -->
                <div class="account-info-grid">
                    <div class="info-item">
                        <span class="info-label">Account Number</span>
                        <span class="info-value">${data.account_number || data.property_number || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Owner Name</span>
                        <span class="info-value">${data.owner_name || 'N/A'}</span>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        <span class="info-value">${data.business_name}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Business Type</span>
                        <span class="info-value">${data.business_type}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category</span>
                        <span class="info-value">${data.category}</span>
=======
                        <span class="info-value">${data.business_name || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Business Type</span>
                        <span class="info-value">${data.business_type || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category</span>
                        <span class="info-value">${data.category || 'N/A'}</span>
>>>>>>> c9ccaba (Initial commit)
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sub Zone</span>
                        <span class="info-value">${data.sub_zone_name || 'N/A'}</span>
                    </div>
<<<<<<< HEAD
=======
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">${data.status || 'N/A'}</span>
                    </div>
>>>>>>> c9ccaba (Initial commit)
                `;
            } else {
                detailsHtml += `
                    <div class="info-item">
                        <span class="info-label">Structure</span>
<<<<<<< HEAD
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
=======
                        <span class="info-value">${data.structure || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Property Use</span>
                        <span class="info-value">${data.property_use || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Number of Rooms</span>
                        <span class="info-value">${data.number_of_rooms || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ownership Type</span>
                        <span class="info-value">${data.ownership_type || 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Property Type</span>
                        <span class="info-value">${data.property_type || 'N/A'}</span>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
                        <span class="info-label">Total Payments</span>
                        <span class="info-value">${data.total_payments || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Amount Payable</span>
                        <span class="info-value amount-highlight">GH₵ ${parseFloat(data.amount_payable || 0).toFixed(2)}</span>
                    </div>
=======
                        <span class="info-label">Total Transactions</span>
                        <span class="info-value">${data.total_payments || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Successful Payments</span>
                        <span class="info-value">${data.successful_payments || 0}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value">${data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Updated</span>
                        <span class="info-value">${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
                    ${remainingBalance > 0 ? `
                        <a href="record.php?account=${data.type}:${data.business_id || data.property_id}" 
                           style="background: #38a169; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;"
                           onmouseover="this.style.background='#2f855a'; this.style.transform='translateY(-2px)'"
                           onmouseout="this.style.background='#38a169'; this.style.transform='translateY(0)'">
                            <i class="fas fa-cash-register"></i>
                            <span class="icon-money" style="display: none;"></span>
                            Record Payment
                        </a>
                    ` : `
                        <div style="background: #c6f6d5; color: #276749; padding: 12px 25px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-check-circle"></i>
                            <span class="icon-check" style="display: none;"></span>
                            Account Fully Paid
                        </div>
                    `}
                    <button onclick="closeModal()" style="background: #94a3b8; color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.background='#64748b'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='#94a3b8'; this.style.transform='translateY(0)'">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
>>>>>>> c9ccaba (Initial commit)
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
<<<<<<< HEAD
    </script>
</body>
</html>
=======

        // Debug info toggle functionality
        function toggleDebugInfo() {
            const debugDiv = document.getElementById('debugInfo');
            const toggleBtn = document.querySelector('.debug-toggle');
            
            if (debugDiv && toggleBtn) {
                if (debugDiv.style.display === 'none') {
                    debugDiv.style.display = 'block';
                    toggleBtn.textContent = '🔧 Hide Debug Info';
                } else {
                    debugDiv.style.display = 'none';
                    toggleBtn.textContent = '🔧 Show Debug Info';
                }
            }
        }

        // Auto-hide debug info after 10 seconds unless user interacts with it
        setTimeout(() => {
            const debugDiv = document.getElementById('debugInfo');
            const toggleBtn = document.querySelector('.debug-toggle');
            if (debugDiv && toggleBtn && !debugDiv.matches(':hover')) {
                debugDiv.style.display = 'none';
                toggleBtn.textContent = '🔧 Show Debug Info';
            }
        }, 10000);

        console.log('✅ Search accounts page initialized successfully with outstanding balance calculations');
    </script>
</body>
</html>
>>>>>>> c9ccaba (Initial commit)
