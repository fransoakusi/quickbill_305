<<<<<<< HEAD
 
=======
 <?php
/**
 * Public Portal - View Bill Details for QUICKBILL 305
 * Displays detailed bill information and QR code
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Start session for public portal
session_start();

// Get bill ID from URL
$billId = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;

if (!$billId) {
    setFlashMessage('error', 'Invalid bill ID provided.');
    header('Location: search_bill.php');
    exit();
}

// Initialize variables
$billData = null;
$accountData = null;
$paymentHistory = [];
$assemblyName = getSystemSetting('assembly_name', 'Municipal Assembly');

try {
    $db = new Database();
    
    // Get bill details
    $billQuery = "
        SELECT 
            bill_id,
            bill_number,
            bill_type,
            reference_id,
            billing_year,
            old_bill,
            previous_payments,
            arrears,
            current_bill,
            amount_payable,
            qr_code,
            status,
            generated_at,
            due_date
        FROM bills 
        WHERE bill_id = ?
    ";
    
    $billData = $db->fetchRow($billQuery, [$billId]);
    
    if (!$billData) {
        setFlashMessage('error', 'Bill not found.');
        header('Location: search_bill.php');
        exit();
    }
    
    // Get account details based on bill type
    if ($billData['bill_type'] === 'Business') {
        $accountQuery = "
            SELECT 
                business_id as id,
                account_number,
                business_name as name,
                owner_name,
                business_type as type,
                category,
                telephone,
                exact_location as location,
                status,
                z.zone_name,
                sz.sub_zone_name
            FROM businesses b
            LEFT JOIN zones z ON b.zone_id = z.zone_id
            LEFT JOIN sub_zones sz ON b.sub_zone_id = sz.sub_zone_id
            WHERE b.business_id = ?
        ";
        
        $accountData = $db->fetchRow($accountQuery, [$billData['reference_id']]);
        
    } elseif ($billData['bill_type'] === 'Property') {
        $accountQuery = "
            SELECT 
                property_id as id,
                property_number as account_number,
                owner_name as name,
                owner_name,
                structure as type,
                property_use as category,
                telephone,
                location,
                number_of_rooms,
                z.zone_name,
                sz.sub_zone_name
            FROM properties p
            LEFT JOIN zones z ON p.zone_id = z.zone_id
            LEFT JOIN sub_zones sz ON p.sub_zone_id = sz.sub_zone_id
            WHERE p.property_id = ?
        ";
        
        $accountData = $db->fetchRow($accountQuery, [$billData['reference_id']]);
    }
    
    if (!$accountData) {
        setFlashMessage('error', 'Account information not found.');
        header('Location: search_bill.php');
        exit();
    }
    
    // Get payment history for this bill
    $paymentQuery = "
        SELECT 
            payment_id,
            payment_reference,
            amount_paid,
            payment_method,
            payment_channel,
            payment_status,
            payment_date,
            notes
        FROM payments 
        WHERE bill_id = ? 
        ORDER BY payment_date DESC
    ";
    
    $paymentHistory = $db->fetchAll($paymentQuery, [$billId]);
    
} catch (Exception $e) {
    writeLog("View bill error: " . $e->getMessage(), 'ERROR');
    setFlashMessage('error', 'An error occurred while loading bill details.');
    header('Location: search_bill.php');
    exit();
}

include 'header.php';
?>

<div class="bill-details-page">
    <div class="container">
        <!-- Bill Header -->
        <div class="bill-header-section">
            <div class="bill-header-content">
                <div class="bill-title">
                    <h1>📄 Bill Details</h1>
                    <p>Complete information for your <?php echo strtolower($billData['bill_type']); ?> bill</p>
                </div>
                
                <div class="bill-actions">
                    <button onclick="window.print()" class="btn btn-outline">
                        <i class="fas fa-print"></i>
                        Print Bill
                    </button>
                    
                    <?php if ($billData['amount_payable'] > 0): ?>
                    <a href="pay_bill.php?bill_id=<?php echo $billData['bill_id']; ?>" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i>
                        Pay Now
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bill Information Card -->
        <div class="bill-info-card">
            <!-- Assembly Header -->
            <div class="assembly-header">
                <div class="assembly-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="assembly-info">
                    <h2><?php echo htmlspecialchars($assemblyName); ?></h2>
                    <p class="assembly-subtitle">Official Bill Statement</p>
                    <p class="assembly-contact">Ghana | Tel: +233 123 456 789</p>
                </div>
                <div class="bill-qr">
                    <?php if (!empty($billData['qr_code'])): ?>
                        <img src="data:image/png;base64,<?php echo $billData['qr_code']; ?>" alt="Bill QR Code" class="qr-code">
                    <?php else: ?>
                        <div class="qr-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <small>QR Code</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bill and Account Details -->
            <div class="bill-account-section">
                <div class="bill-details-grid">
                    <!-- Bill Information -->
                    <div class="detail-section">
                        <h3 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Bill Information
                        </h3>
                        <div class="detail-rows">
                            <div class="detail-row">
                                <span class="detail-label">Bill Number:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($billData['bill_number']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Bill Type:</span>
                                <span class="detail-value">
                                    <?php echo $billData['bill_type'] === 'Business' ? '🏢 Business Permit' : '🏠 Property Rates'; ?>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Billing Year:</span>
                                <span class="detail-value"><?php echo $billData['billing_year']; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Generated Date:</span>
                                <span class="detail-value"><?php echo formatDate($billData['generated_at']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge <?php echo strtolower($billData['status']); ?>">
                                        <?php echo htmlspecialchars($billData['status']); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="detail-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Account Information
                        </h3>
                        <div class="detail-rows">
                            <div class="detail-row">
                                <span class="detail-label">Account Number:</span>
                                <span class="detail-value account-number"><?php echo htmlspecialchars($accountData['account_number']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><?php echo $billData['bill_type'] === 'Business' ? 'Business Name:' : 'Owner Name:'; ?></span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Owner Name:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['owner_name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><?php echo $billData['bill_type'] === 'Business' ? 'Business Type:' : 'Structure:'; ?></span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['type']); ?></span>
                            </div>
                            <?php if (!empty($accountData['category'])): ?>
                            <div class="detail-row">
                                <span class="detail-label"><?php echo $billData['bill_type'] === 'Business' ? 'Category:' : 'Property Use:'; ?></span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['category']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($accountData['telephone'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Telephone:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['telephone']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-row">
                                <span class="detail-label">Location:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['location'] ?? 'N/A'); ?></span>
                            </div>
                            <?php if (!empty($accountData['zone_name'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Zone:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($accountData['zone_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (isset($accountData['number_of_rooms'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Number of Rooms:</span>
                                <span class="detail-value"><?php echo $accountData['number_of_rooms']; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bill Amount Breakdown -->
            <div class="amount-breakdown-section">
                <h3 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Amount Breakdown
                </h3>
                
                <div class="amount-table">
                    <div class="amount-table-header">
                        <span>Description</span>
                        <span>Amount (₵)</span>
                    </div>
                    
                    <?php if ($billData['old_bill'] > 0): ?>
                    <div class="amount-row">
                        <span class="amount-description">
                            <i class="fas fa-history"></i>
                            Previous Bill Balance
                        </span>
                        <span class="amount-value"><?php echo number_format($billData['old_bill'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($billData['arrears'] > 0): ?>
                    <div class="amount-row arrears">
                        <span class="amount-description">
                            <i class="fas fa-exclamation-triangle"></i>
                            Arrears/Penalties
                        </span>
                        <span class="amount-value"><?php echo number_format($billData['arrears'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="amount-row">
                        <span class="amount-description">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Current Bill (<?php echo $billData['billing_year']; ?>)
                        </span>
                        <span class="amount-value"><?php echo number_format($billData['current_bill'], 2); ?></span>
                    </div>
                    
                    <?php if ($billData['previous_payments'] > 0): ?>
                    <div class="amount-row credit">
                        <span class="amount-description">
                            <i class="fas fa-check-circle"></i>
                            Previous Payments
                        </span>
                        <span class="amount-value">-<?php echo number_format($billData['previous_payments'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="amount-row total">
                        <span class="amount-description">
                            <i class="fas fa-money-bill-wave"></i>
                            <strong>Total Amount Payable</strong>
                        </span>
                        <span class="amount-value">
                            <strong>₵ <?php echo number_format($billData['amount_payable'], 2); ?></strong>
                        </span>
                    </div>
                </div>
                
                <?php if ($billData['amount_payable'] > 0): ?>
                <div class="payment-call-to-action">
                    <div class="payment-cta-content">
                        <h4>💳 Ready to Pay?</h4>
                        <p>Pay securely online using mobile money or card payments</p>
                        <a href="pay_bill.php?bill_id=<?php echo $billData['bill_id']; ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card"></i>
                            Pay ₵ <?php echo number_format($billData['amount_payable'], 2); ?> Now
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="payment-status-paid">
                    <div class="paid-icon">✅</div>
                    <h4>Bill Fully Paid</h4>
                    <p>This bill has been paid in full. Thank you!</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Payment History -->
            <?php if (!empty($paymentHistory)): ?>
            <div class="payment-history-section">
                <h3 class="section-title">
                    <i class="fas fa-history"></i>
                    Payment History
                </h3>
                
                <div class="payment-history-table">
                    <?php foreach ($paymentHistory as $payment): ?>
                    <div class="payment-row">
                        <div class="payment-info">
                            <div class="payment-reference">
                                <i class="fas fa-receipt"></i>
                                <?php echo htmlspecialchars($payment['payment_reference']); ?>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-<?php echo $payment['payment_method'] === 'Mobile Money' ? 'mobile-alt' : 'credit-card'; ?>"></i>
                                <?php echo htmlspecialchars($payment['payment_method']); ?>
                                <?php if (!empty($payment['payment_channel'])): ?>
                                    - <?php echo htmlspecialchars($payment['payment_channel']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="payment-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo formatDateTime($payment['payment_date']); ?>
                            </div>
                            <?php if (!empty($payment['notes'])): ?>
                            <div class="payment-notes">
                                <i class="fas fa-sticky-note"></i>
                                <?php echo htmlspecialchars($payment['notes']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="payment-amount">
                            <span class="amount">₵ <?php echo number_format($payment['amount_paid'], 2); ?></span>
                            <span class="status-badge <?php echo strtolower($payment['payment_status']); ?>">
                                <?php echo htmlspecialchars($payment['payment_status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Bill Footer -->
            <div class="bill-footer">
                <div class="footer-note">
                    <p><strong>Note:</strong> This is an official bill statement from <?php echo htmlspecialchars($assemblyName); ?>. 
                    For inquiries, please contact our office during business hours.</p>
                    <p><strong>Payment Methods:</strong> Mobile Money (MTN, Telecel, AirtelTigo), Debit/Credit Cards, Bank Transfer</p>
                </div>
                
                <div class="footer-contact">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+233 123 456 789</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@<?php echo strtolower(str_replace(' ', '', $assemblyName)); ?>.gov.gh</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Actions -->
        <div class="navigation-actions">
            <a href="search_bill.php" class="btn btn-outline">
                <i class="fas fa-search"></i>
                Search Another Bill
            </a>
            
            <a href="verify_payment.php" class="btn btn-secondary">
                <i class="fas fa-check-circle"></i>
                Verify Payment
            </a>
            
            <?php if ($billData['amount_payable'] > 0): ?>
            <a href="pay_bill.php?bill_id=<?php echo $billData['bill_id']; ?>" class="btn btn-primary">
                <i class="fas fa-credit-card"></i>
                Pay This Bill
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Bill Details Page Styles */
.bill-details-page {
    padding: 40px 0;
    min-height: 600px;
}

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Bill Header */
.bill-header-section {
    margin-bottom: 30px;
}

.bill-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.bill-title h1 {
    color: #2d3748;
    margin-bottom: 5px;
    font-size: 2rem;
}

.bill-title p {
    color: #718096;
    margin: 0;
}

.bill-actions {
    display: flex;
    gap: 15px;
}

/* Bill Information Card */
.bill-info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

/* Assembly Header */
.assembly-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.assembly-logo {
    font-size: 3rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.assembly-info {
    flex: 1;
}

.assembly-info h2 {
    margin: 0 0 8px 0;
    font-size: 1.8rem;
    font-weight: bold;
}

.assembly-subtitle {
    margin: 0 0 5px 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.assembly-contact {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9rem;
}

.bill-qr {
    flex-shrink: 0;
}

.qr-code {
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 8px;
    padding: 8px;
}

.qr-placeholder {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.qr-placeholder i {
    font-size: 2rem;
    margin-bottom: 5px;
}

.qr-placeholder small {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Bill and Account Section */
.bill-account-section {
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.bill-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.detail-section {
    background: #f7fafc;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #2d3748;
    margin-bottom: 20px;
    font-size: 1.2rem;
    font-weight: 600;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.section-title i {
    color: #667eea;
}

.detail-rows {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e2e8f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #4a5568;
    font-weight: 500;
    flex: 0 0 40%;
}

.detail-value {
    color: #2d3748;
    font-weight: 600;
    text-align: right;
    flex: 1;
}

.detail-value.account-number {
    color: #667eea;
    font-family: monospace;
    font-size: 1.1rem;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

.status-badge.successful {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.failed {
    background: #fed7d7;
    color: #c53030;
}

/* Amount Breakdown */
.amount-breakdown-section {
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.amount-table {
    background: #f7fafc;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    margin-bottom: 25px;
}

.amount-table-header {
    background: #667eea;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    font-weight: 600;
}

.amount-row {
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
}

.amount-row:last-child {
    border-bottom: none;
}

.amount-row.total {
    background: #edf2f7;
    font-weight: bold;
    font-size: 1.1rem;
}

.amount-row.credit {
    background: #f0fff4;
}

.amount-row.credit .amount-value {
    color: #38a169;
}

.amount-row.arrears {
    background: #fff5f5;
}

.amount-row.arrears .amount-value {
    color: #e53e3e;
}

.amount-description {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2d3748;
}

.amount-description i {
    color: #667eea;
    width: 16px;
}

.amount-value {
    font-weight: 600;
    color: #2d3748;
    font-family: monospace;
}

/* Payment Call to Action */
.payment-call-to-action {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
}

.payment-cta-content h4 {
    margin-bottom: 8px;
    font-size: 1.3rem;
}

.payment-cta-content p {
    margin-bottom: 20px;
    opacity: 0.9;
}

.payment-status-paid {
    background: #f0fff4;
    border: 2px solid #9ae6b4;
    color: #22543d;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
}

.paid-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.payment-status-paid h4 {
    margin-bottom: 8px;
    color: #22543d;
}

.payment-status-paid p {
    margin: 0;
    color: #2f855a;
}

/* Payment History */
.payment-history-section {
    padding: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.payment-history-table {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f7fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.payment-info {
    flex: 1;
}

.payment-reference {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-method,
.payment-date,
.payment-notes {
    font-size: 0.9rem;
    color: #4a5568;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.payment-method i,
.payment-date i,
.payment-notes i {
    color: #667eea;
    width: 14px;
}

.payment-amount {
    text-align: right;
}

.payment-amount .amount {
    display: block;
    font-weight: bold;
    color: #2d3748;
    font-size: 1.1rem;
    margin-bottom: 5px;
}

/* Bill Footer */
.bill-footer {
    padding: 30px;
    background: #f7fafc;
}

.footer-note {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.footer-note p {
    color: #4a5568;
    margin-bottom: 8px;
    font-size: 0.9rem;
    line-height: 1.5;
}

.footer-contact {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4a5568;
    font-size: 0.9rem;
}

.contact-item i {
    color: #667eea;
    width: 16px;
}

/* Navigation Actions */
.navigation-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Button Styles */
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

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
}

/* Print Styles */
@media print {
    .bill-header-section,
    .navigation-actions,
    .payment-call-to-action {
        display: none !important;
    }
    
    .bill-info-card {
        box-shadow: none;
        border: 1px solid #000;
    }
    
    .assembly-header {
        background: #f8f9fa !important;
        color: #000 !important;
        border-bottom: 2px solid #000;
    }
    
    .section-title {
        color: #000 !important;
    }
    
    .amount-table-header {
        background: #f8f9fa !important;
        color: #000 !important;
    }
    
    body {
        background: white !important;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .bill-header-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .assembly-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .bill-details-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .detail-value {
        text-align: left;
    }
    
    .amount-table-header,
    .amount-row {
        flex-direction: column;
        gap: 5px;
        text-align: center;
    }
    
    .payment-row {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .footer-contact {
        flex-direction: column;
        gap: 15px;
    }
    
    .navigation-actions {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .bill-info-card,
    .detail-section {
        padding: 20px;
    }
    
    .assembly-header {
        padding: 20px;
    }
    
    .bill-title h1 {
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add copy functionality for account number
    const accountNumber = document.querySelector('.account-number');
    if (accountNumber) {
        accountNumber.style.cursor = 'pointer';
        accountNumber.title = 'Click to copy account number';
        
        accountNumber.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent).then(function() {
                // Show temporary feedback
                const originalText = accountNumber.textContent;
                accountNumber.textContent = 'Copied!';
                accountNumber.style.color = '#48bb78';
                
                setTimeout(() => {
                    accountNumber.textContent = originalText;
                    accountNumber.style.color = '#667eea';
                }, 1000);
            });
        });
    }
    
    // Add print optimization
    window.addEventListener('beforeprint', function() {
        document.title = 'Bill_<?php echo htmlspecialchars($billData["bill_number"]); ?>';
    });
    
    // Add hover effects to payment rows
    const paymentRows = document.querySelectorAll('.payment-row');
    paymentRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.borderColor = '#667eea';
            this.style.transform = 'translateY(-2px)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e2e8f0';
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Auto-scroll to amount breakdown on mobile
    if (window.innerWidth <= 768) {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('scroll') === 'amount') {
            setTimeout(() => {
                document.querySelector('.amount-breakdown-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 500);
        }
    }
});
</script>

<?php include 'footer.php'; ?>
>>>>>>> c9ccaba (Initial commit)
