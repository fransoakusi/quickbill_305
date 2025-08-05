<<<<<<< HEAD
 
=======
<?php
/**
 * Public Portal - Search Bill for QUICKBILL 305
 * Allows users to search for their bills using account number
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Start session for public portal
session_start();

// Add missing authentication functions for public portal compatibility
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

// Public portal logging function (doesn't require authentication)
function logPublicActivity($action, $details = '') {
    try {
        $db = new Database();
        
        $sql = "INSERT INTO audit_logs (user_id, action, table_name, record_id, new_values, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $params = [
            null, // No user ID for public access
            'PUBLIC: ' . $action,
            'public_portal',
            null,
            json_encode($details),
            getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        $db->execute($sql, $params);
        
    } catch (Exception $e) {
        writeLog("Failed to log public activity: " . $e->getMessage(), 'ERROR');
    }
}

// Initialize variables
$searchResults = null;
$searchPerformed = false;
$accountNumber = '';
$accountType = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $errorMessage = 'Invalid security token. Please try again.';
    } else {
        $accountNumber = sanitizeInput($_POST['account_number'] ?? '');
        $accountType = sanitizeInput($_POST['account_type'] ?? '');
        
        // Validate required fields
        if (empty($accountNumber)) {
            $errorMessage = 'Please enter your account number.';
        } elseif (empty($accountType)) {
            $errorMessage = 'Please select your account type.';
        } else {
            $searchPerformed = true;
            
            try {
                $db = new Database();
                
                // Log search attempt
                logPublicActivity(
                    "Account search attempt", 
                    [
                        'account_number' => $accountNumber,
                        'account_type' => $accountType,
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                );
                
                // Search based on account type
                if ($accountType === 'Business') {
                    // Search for business account
                    $accountQuery = "
                        SELECT 
                            b.business_id as id,
                            b.account_number,
                            b.business_name as name,
                            b.owner_name,
                            b.telephone,
                            b.business_type as type,
                            b.category,
                            b.exact_location as location,
                            b.amount_payable,
                            b.status,
                            z.zone_name,
                            sz.sub_zone_name
                        FROM businesses b
                        LEFT JOIN zones z ON b.zone_id = z.zone_id
                        LEFT JOIN sub_zones sz ON b.sub_zone_id = sz.sub_zone_id
                        WHERE b.account_number = ? AND b.status = 'Active'
                    ";
                    
                    $account = $db->fetchRow($accountQuery, [$accountNumber]);
                    
                    if ($account) {
                        // Get current year bills for this business
                        $billsQuery = "
                            SELECT 
                                bill_id,
                                bill_number,
                                billing_year,
                                old_bill,
                                previous_payments,
                                arrears,
                                current_bill,
                                amount_payable,
                                status,
                                generated_at,
                                due_date
                            FROM bills 
                            WHERE bill_type = 'Business' 
                            AND reference_id = ? 
                            AND YEAR(generated_at) = YEAR(CURDATE())
                            ORDER BY generated_at DESC
                        ";
                        
                        $bills = $db->fetchAll($billsQuery, [$account['id']]);
                        
                        $searchResults = [
                            'account' => $account,
                            'bills' => $bills,
                            'account_type' => 'Business'
                        ];
                        
                        // Log successful search
                        logPublicActivity(
                            "Business account found", 
                            [
                                'business_name' => $account['name'],
                                'account_number' => $accountNumber,
                                'bills_count' => count($bills)
                            ]
                        );
                        
                    } else {
                        $errorMessage = 'Business account not found. Please check your account number and try again.';
                        
                        // Log failed search
                        logPublicActivity(
                            "Business account not found", 
                            [
                                'account_number' => $accountNumber,
                                'search_type' => 'Business'
                            ]
                        );
                    }
                    
                } elseif ($accountType === 'Property') {
                    // Search for property account
                    $accountQuery = "
                        SELECT 
                            p.property_id as id,
                            p.property_number as account_number,
                            p.owner_name as name,
                            p.owner_name,
                            p.telephone,
                            p.structure as type,
                            p.property_use as category,
                            p.location,
                            p.number_of_rooms,
                            p.amount_payable,
                            'Active' as status,
                            z.zone_name,
                            sz.sub_zone_name
                        FROM properties p
                        LEFT JOIN zones z ON p.zone_id = z.zone_id
                        LEFT JOIN sub_zones sz ON p.sub_zone_id = sz.sub_zone_id
                        WHERE p.property_number = ?
                    ";
                    
                    $account = $db->fetchRow($accountQuery, [$accountNumber]);
                    
                    if ($account) {
                        // Get current year bills for this property
                        $billsQuery = "
                            SELECT 
                                bill_id,
                                bill_number,
                                billing_year,
                                old_bill,
                                previous_payments,
                                arrears,
                                current_bill,
                                amount_payable,
                                status,
                                generated_at,
                                due_date
                            FROM bills 
                            WHERE bill_type = 'Property' 
                            AND reference_id = ? 
                            AND YEAR(generated_at) = YEAR(CURDATE())
                            ORDER BY generated_at DESC
                        ";
                        
                        $bills = $db->fetchAll($billsQuery, [$account['id']]);
                        
                        $searchResults = [
                            'account' => $account,
                            'bills' => $bills,
                            'account_type' => 'Property'
                        ];
                        
                        // Log successful search
                        logPublicActivity(
                            "Property account found", 
                            [
                                'owner_name' => $account['name'],
                                'account_number' => $accountNumber,
                                'bills_count' => count($bills)
                            ]
                        );
                        
                    } else {
                        $errorMessage = 'Property account not found. Please check your account number and try again.';
                        
                        // Log failed search
                        logPublicActivity(
                            "Property account not found", 
                            [
                                'account_number' => $accountNumber,
                                'search_type' => 'Property'
                            ]
                        );
                    }
                }
                
            } catch (Exception $e) {
                writeLog("Search error: " . $e->getMessage(), 'ERROR');
                $errorMessage = 'An error occurred while searching for your account. Please try again.';
                
                // Log error
                logPublicActivity(
                    "Search error occurred", 
                    [
                        'error_message' => $e->getMessage(),
                        'account_number' => $accountNumber,
                        'account_type' => $accountType
                    ]
                );
            }
        }
    }
}

include 'header.php';
?>

<div class="search-page">
    <div class="container">
        <?php if (!$searchPerformed): ?>
        <!-- Search Form Section -->
        <div class="search-section">
            <div class="search-header">
                <h1>🔍 Find Your Bill</h1>
                <p>Enter your account details to view and pay your bills online</p>
            </div>
            
            <div class="search-form-container">
                <form action="search_bill.php" method="POST" class="search-form" id="searchForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <?php if ($errorMessage): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($errorMessage); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="account_number">
                                <i class="fas fa-hashtag"></i>
                                Account Number
                            </label>
                            <input 
                                type="text" 
                                id="account_number" 
                                name="account_number" 
                                value="<?php echo htmlspecialchars($accountNumber); ?>"
                                placeholder="e.g., BIZ000001 or PROP000001"
                                required
                                autocomplete="off"
                                class="form-control"
                            >
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i>
                                Find your account number on your bill or SMS notification
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="account_type">
                                <i class="fas fa-list"></i>
                                Account Type
                            </label>
                            <select id="account_type" name="account_type" required class="form-control">
                                <option value="">Select Account Type</option>
                                <option value="Business" <?php echo $accountType === 'Business' ? 'selected' : ''; ?>>
                                    🏢 Business Permit
                                </option>
                                <option value="Property" <?php echo $accountType === 'Property' ? 'selected' : ''; ?>>
                                    🏠 Property Rates
                                </option>
                            </select>
                            <small class="form-help">
                                <i class="fas fa-info-circle"></i>
                                Choose the type of account you're looking for
                            </small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i>
                        Search My Bill
                    </button>
                </form>
                
                <div class="search-tips">
                    <h4>💡 Search Tips</h4>
                    <ul>
                        <li><strong>Account Number:</strong> Usually starts with BIZ (Business) or PROP (Property)</li>
                        <li><strong>Case Sensitive:</strong> Enter your account number exactly as shown on your bill</li>
                        <li><strong>Recent Bills:</strong> Only current year bills are shown for payment</li>
                        <li><strong>Need Help?</strong> Contact us if you can't find your account number</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Search Results Section -->
        <?php if ($searchResults): ?>
        <div class="results-section">
            <!-- Account Information -->
            <div class="account-info-card">
                <div class="account-header">
                    <div class="account-icon">
                        <?php echo $searchResults['account_type'] === 'Business' ? '🏢' : '🏠'; ?>
                    </div>
                    <div class="account-details">
                        <h2><?php echo htmlspecialchars($searchResults['account']['name']); ?></h2>
                        <p class="account-number">Account: <?php echo htmlspecialchars($searchResults['account']['account_number']); ?></p>
                        <div class="account-meta">
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($searchResults['account']['owner_name']); ?>
                            </span>
                            <?php if (!empty($searchResults['account']['telephone'])): ?>
                            <span class="meta-item">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($searchResults['account']['telephone']); ?>
                            </span>
                            <?php endif; ?>
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($searchResults['account']['zone_name'] ?? 'N/A'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="account-status">
                        <span class="status-badge <?php echo strtolower($searchResults['account']['status']); ?>">
                            <?php echo htmlspecialchars($searchResults['account']['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="account-summary">
                    <div class="summary-item">
                        <div class="summary-label">Total Outstanding</div>
                        <div class="summary-value outstanding">
                            ₵ <?php echo number_format($searchResults['account']['amount_payable'], 2); ?>
                        </div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label"><?php echo $searchResults['account_type']; ?> Type</div>
                        <div class="summary-value">
                            <?php echo htmlspecialchars($searchResults['account']['type']); ?>
                            <?php if ($searchResults['account_type'] === 'Business'): ?>
                                - <?php echo htmlspecialchars($searchResults['account']['category']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bills Section -->
            <div class="bills-section">
                <div class="bills-header">
                    <h3>📄 Available Bills (<?php echo date('Y'); ?>)</h3>
                    <p>Bills available for online payment</p>
                </div>
                
                <?php if (!empty($searchResults['bills'])): ?>
                <div class="bills-grid">
                    <?php foreach ($searchResults['bills'] as $bill): ?>
                    <div class="bill-card">
                        <div class="bill-header">
                            <div class="bill-number">
                                <i class="fas fa-file-invoice"></i>
                                <?php echo htmlspecialchars($bill['bill_number']); ?>
                            </div>
                            <div class="bill-status">
                                <span class="status-badge <?php echo strtolower($bill['status']); ?>">
                                    <?php echo htmlspecialchars($bill['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="bill-details">
                            <div class="bill-year">
                                <i class="fas fa-calendar"></i>
                                Billing Year: <?php echo $bill['billing_year']; ?>
                            </div>
                            
                            <div class="bill-amounts">
                                <?php if ($bill['old_bill'] > 0): ?>
                                <div class="amount-row">
                                    <span>Previous Bill:</span>
                                    <span>₵ <?php echo number_format($bill['old_bill'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($bill['previous_payments'] > 0): ?>
                                <div class="amount-row">
                                    <span>Previous Payments:</span>
                                    <span class="credit">-₵ <?php echo number_format($bill['previous_payments'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($bill['arrears'] > 0): ?>
                                <div class="amount-row">
                                    <span>Arrears:</span>
                                    <span class="arrears">₵ <?php echo number_format($bill['arrears'], 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="amount-row">
                                    <span>Current Bill:</span>
                                    <span>₵ <?php echo number_format($bill['current_bill'], 2); ?></span>
                                </div>
                                
                                <div class="amount-row total">
                                    <span><strong>Amount Payable:</strong></span>
                                    <span><strong>₵ <?php echo number_format($bill['amount_payable'], 2); ?></strong></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bill-actions">
                            <a href="view_bill.php?bill_id=<?php echo $bill['bill_id']; ?>" class="btn btn-outline">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                            
                            <?php if ($bill['amount_payable'] > 0): ?>
                            <a href="pay_bill.php?bill_id=<?php echo $bill['bill_id']; ?>" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i>
                                Pay Now
                            </a>
                            <?php else: ?>
                            <span class="btn btn-disabled">
                                <i class="fas fa-check"></i>
                                Paid
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="bill-meta">
                            <small>
                                <i class="fas fa-clock"></i>
                                Generated: <?php echo formatDate($bill['generated_at']); ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php else: ?>
                <div class="no-bills-message">
                    <div class="no-bills-icon">📋</div>
                    <h4>No Bills Found</h4>
                    <p>No bills have been generated for this account in <?php echo date('Y'); ?>.</p>
                    <small>Bills are typically generated on November 1st each year.</small>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="search_bill.php" class="btn btn-outline">
                    <i class="fas fa-search"></i>
                    Search Another Account
                </a>
                
                <a href="verify_payment.php" class="btn btn-secondary">
                    <i class="fas fa-check-circle"></i>
                    Verify Payment
                </a>
            </div>
        </div>
        
        <?php else: ?>
        <!-- No Results Found -->
        <div class="no-results-section">
            <div class="no-results-content">
                <div class="no-results-icon">🔍</div>
                <h2>Account Not Found</h2>
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
                
                <div class="suggestions">
                    <h4>💡 Suggestions:</h4>
                    <ul>
                        <li>Double-check your account number for typos</li>
                        <li>Make sure you selected the correct account type</li>
                        <li>Contact our office if you need help finding your account number</li>
                        <li>Account numbers are case-sensitive</li>
                    </ul>
                </div>
                
                <div class="retry-actions">
                    <a href="search_bill.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i>
                        Try Again
                    </a>
                    
                    <a href="#help" class="btn btn-outline" onclick="scrollToHelp()">
                        <i class="fas fa-question-circle"></i>
                        Get Help
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* Search Page Styles */
.search-page {
    min-height: 600px;
    padding: 40px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Search Section */
.search-section {
    max-width: 600px;
    margin: 0 auto;
}

.search-header {
    text-align: center;
    margin-bottom: 40px;
}

.search-header h1 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 10px;
}

.search-header p {
    color: #718096;
    font-size: 1.1rem;
}

.search-form-container {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.form-row {
    display: grid;
    gap: 20px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2d3748;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-help {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    font-size: 0.85rem;
    color: #718096;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: #fed7d7;
    color: #c53030;
    border: 1px solid #feb2b2;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

.btn-outline {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #4a5568;
    color: white;
}

.btn-secondary:hover {
    background: #2d3748;
    color: white;
    text-decoration: none;
}

.btn-disabled {
    background: #e2e8f0;
    color: #a0aec0;
    cursor: not-allowed;
}

.btn-lg {
    width: 100%;
    padding: 15px 24px;
    font-size: 1.1rem;
}

.search-tips {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 10px;
    padding: 20px;
    margin-top: 30px;
}

.search-tips h4 {
    color: #0369a1;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.search-tips ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.search-tips li {
    margin-bottom: 8px;
    color: #0369a1;
    font-size: 0.9rem;
}

/* Results Section */
.results-section {
    animation: slideUp 0.6s ease;
}

.account-info-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-left: 5px solid #667eea;
}

.account-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 25px;
}

.account-icon {
    font-size: 3rem;
    background: #f0f9ff;
    padding: 15px;
    border-radius: 15px;
    border: 2px solid #bae6fd;
}

.account-details {
    flex: 1;
}

.account-details h2 {
    color: #2d3748;
    margin-bottom: 5px;
    font-size: 1.5rem;
}

.account-number {
    color: #667eea;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.account-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #4a5568;
    font-size: 0.9rem;
}

.meta-item i {
    color: #667eea;
    width: 16px;
}

.account-status {
    flex-shrink: 0;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.active {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.paid {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.pending {
    background: #fed7d7;
    color: #c53030;
}

.status-badge.partially {
    background: #feebc8;
    color: #c05621;
}

.account-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.summary-item {
    text-align: center;
    padding: 15px;
    background: #f7fafc;
    border-radius: 10px;
}

.summary-label {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.summary-value {
    font-weight: bold;
    color: #2d3748;
    font-size: 1.1rem;
}

.summary-value.outstanding {
    color: #d69e2e;
    font-size: 1.3rem;
}

/* Bills Section */
.bills-section {
    margin-bottom: 30px;
}

.bills-header {
    margin-bottom: 25px;
}

.bills-header h3 {
    color: #2d3748;
    margin-bottom: 5px;
    font-size: 1.5rem;
}

.bills-header p {
    color: #718096;
}

.bills-grid {
    display: grid;
    gap: 20px;
}

.bill-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.3s;
}

.bill-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.bill-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.bill-number {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #2d3748;
}

.bill-number i {
    color: #667eea;
}

.bill-details {
    margin-bottom: 20px;
}

.bill-year {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4a5568;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.bill-year i {
    color: #667eea;
}

.bill-amounts {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
}

.amount-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.amount-row:last-child {
    margin-bottom: 0;
}

.amount-row.total {
    padding-top: 10px;
    border-top: 2px solid #e2e8f0;
    font-size: 1rem;
}

.amount-row .credit {
    color: #38a169;
}

.amount-row .arrears {
    color: #e53e3e;
}

.bill-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.bill-actions .btn {
    flex: 1;
    justify-content: center;
}

.bill-meta {
    text-align: center;
    padding-top: 15px;
    border-top: 1px solid #e2e8f0;
}

.bill-meta small {
    color: #718096;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.no-bills-message {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.no-bills-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.no-bills-message h4 {
    color: #2d3748;
    margin-bottom: 10px;
}

.no-bills-message p {
    color: #4a5568;
    margin-bottom: 10px;
}

.no-bills-message small {
    color: #718096;
}

/* No Results Section */
.no-results-section {
    text-align: center;
    padding: 60px 20px;
    animation: slideUp 0.6s ease;
}

.no-results-content {
    max-width: 500px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.no-results-content h2 {
    color: #2d3748;
    margin-bottom: 15px;
}

.no-results-content p {
    color: #4a5568;
    margin-bottom: 25px;
}

.suggestions {
    text-align: left;
    margin-bottom: 30px;
    padding: 20px;
    background: #f0f9ff;
    border-radius: 10px;
    border: 1px solid #bae6fd;
}

.suggestions h4 {
    color: #0369a1;
    margin-bottom: 15px;
}

.suggestions ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.suggestions li {
    margin-bottom: 8px;
    color: #0369a1;
    font-size: 0.9rem;
    padding-left: 15px;
    position: relative;
}

.suggestions li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: #0369a1;
}

.retry-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-form-container {
        padding: 25px;
    }
    
    .account-header {
        flex-direction: column;
        text-align: center;
    }
    
    .account-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .account-summary {
        grid-template-columns: 1fr;
    }
    
    .bill-actions {
        flex-direction: column;
    }
    
    .action-buttons,
    .retry-actions {
        flex-direction: column;
    }
    
    .search-header h1 {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const accountInput = document.getElementById('account_number');
    const typeSelect = document.getElementById('account_type');
    
    // Auto-detect account type based on account number
    if (accountInput && typeSelect) {
        accountInput.addEventListener('input', function() {
            const value = this.value.toUpperCase();
            
            if (value.startsWith('BIZ')) {
                typeSelect.value = 'Business';
            } else if (value.startsWith('PROP')) {
                typeSelect.value = 'Property';
            }
        });
    }
    
    // Form submission with loading state
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            submitBtn.disabled = true;
            
            // Show loading overlay
            showLoading('Searching for your account...');
            
            // Re-enable button after 15 seconds (in case of issues)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                hideLoading();
            }, 15000);
        });
    }
    
    // Add hover effects to bill cards
    const billCards = document.querySelectorAll('.bill-card');
    billCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#667eea';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e2e8f0';
        });
    });
    
    // Auto-focus on account number input
    if (accountInput && !accountInput.value) {
        accountInput.focus();
    }
});

function scrollToHelp() {
    // Implement scroll to help section functionality
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

// Loading overlay functions (if not already defined)
function showLoading(message) {
    // Implement loading overlay
    console.log('Loading:', message);
}

function hideLoading() {
    // Hide loading overlay
    console.log('Loading hidden');
}
</script>

<?php include 'footer.php'; ?>
>>>>>>> c9ccaba (Initial commit)
