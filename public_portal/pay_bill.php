<<<<<<< HEAD
 
=======
 <?php
/**
 * Public Portal - Pay Bill for QUICKBILL 305
 * Payment interface with PayStack integration and mobile money
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
$paymentMethods = getPaymentMethods();
$assemblyName = getSystemSetting('assembly_name', 'Municipal Assembly');

// Get PayStack configuration
$paystackConfig = getConfig('paystack');
$paystackPublicKey = $paystackConfig['public_key'] ?? '';
$paystackTestMode = $paystackConfig['test_mode'] ?? true;

try {
    $db = new Database();
    
    // Get bill details with account information
    $billQuery = "
        SELECT 
            b.bill_id,
            b.bill_number,
            b.bill_type,
            b.reference_id,
            b.billing_year,
            b.old_bill,
            b.previous_payments,
            b.arrears,
            b.current_bill,
            b.amount_payable,
            b.status,
            b.generated_at,
            CASE 
                WHEN b.bill_type = 'Business' THEN bs.business_name
                WHEN b.bill_type = 'Property' THEN p.owner_name
            END as account_name,
            CASE 
                WHEN b.bill_type = 'Business' THEN bs.account_number
                WHEN b.bill_type = 'Property' THEN p.property_number
            END as account_number,
            CASE 
                WHEN b.bill_type = 'Business' THEN bs.owner_name
                WHEN b.bill_type = 'Property' THEN p.owner_name
            END as owner_name,
            CASE 
                WHEN b.bill_type = 'Business' THEN bs.telephone
                WHEN b.bill_type = 'Property' THEN p.telephone
            END as telephone
        FROM bills b
        LEFT JOIN businesses bs ON b.bill_type = 'Business' AND b.reference_id = bs.business_id
        LEFT JOIN properties p ON b.bill_type = 'Property' AND b.reference_id = p.property_id
        WHERE b.bill_id = ?
    ";
    
    $billData = $db->fetchRow($billQuery, [$billId]);
    
    if (!$billData) {
        setFlashMessage('error', 'Bill not found.');
        header('Location: search_bill.php');
        exit();
    }
    
    // Check if bill is payable
    if ($billData['amount_payable'] <= 0) {
        setFlashMessage('info', 'This bill has already been paid in full.');
        header('Location: view_bill.php?bill_id=' . $billId);
        exit();
    }
    
} catch (Exception $e) {
    writeLog("Payment page error: " . $e->getMessage(), 'ERROR');
    setFlashMessage('error', 'An error occurred while loading bill details.');
    header('Location: search_bill.php');
    exit();
}

include 'header.php';
?>

<div class="payment-page">
    <div class="container">
        <!-- Payment Header -->
        <div class="payment-header">
            <div class="payment-progress">
                <div class="progress-step active">
                    <div class="step-number">1</div>
                    <span>Bill Details</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step active">
                    <div class="step-number">2</div>
                    <span>Payment</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-number">3</div>
                    <span>Confirmation</span>
                </div>
            </div>
            
            <div class="payment-title">
                <h1>💳 Make Payment</h1>
                <p>Secure online payment for your <?php echo strtolower($billData['bill_type']); ?> bill</p>
            </div>
        </div>

        <div class="payment-content">
            <!-- Bill Summary Card -->
            <div class="bill-summary-card">
                <div class="bill-summary-header">
                    <h3>📄 Bill Summary</h3>
                    <span class="bill-number"><?php echo htmlspecialchars($billData['bill_number']); ?></span>
                </div>
                
                <div class="bill-summary-details">
                    <div class="summary-row">
                        <span class="summary-label">Account:</span>
                        <span class="summary-value"><?php echo htmlspecialchars($billData['account_number']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label"><?php echo $billData['bill_type'] === 'Business' ? 'Business:' : 'Owner:'; ?></span>
                        <span class="summary-value"><?php echo htmlspecialchars($billData['account_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Billing Year:</span>
                        <span class="summary-value"><?php echo $billData['billing_year']; ?></span>
                    </div>
                    <div class="summary-row total">
                        <span class="summary-label"><strong>Amount Payable:</strong></span>
                        <span class="summary-value amount"><strong>₵ <?php echo number_format($billData['amount_payable'], 2); ?></strong></span>
                    </div>
                </div>
                
                <div class="bill-summary-actions">
                    <a href="view_bill.php?bill_id=<?php echo $billId; ?>" class="btn btn-outline btn-sm">
                        <i class="fas fa-eye"></i>
                        View Full Bill
                    </a>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="payment-form-card">
                <div class="payment-form-header">
                    <h3>💰 Payment Details</h3>
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>SSL Secured</span>
                    </div>
                </div>
                
                <form id="paymentForm" class="payment-form">
                    <!-- Payment Amount -->
                    <div class="form-section">
                        <h4>Payment Amount</h4>
                        <div class="amount-selection">
                            <div class="amount-option active" data-amount="<?php echo $billData['amount_payable']; ?>">
                                <div class="option-radio">●</div>
                                <div class="option-details">
                                    <div class="option-label">Pay Full Amount</div>
                                    <div class="option-amount">₵ <?php echo number_format($billData['amount_payable'], 2); ?></div>
                                </div>
                            </div>
                            
                            <div class="amount-option" data-amount="custom">
                                <div class="option-radio">○</div>
                                <div class="option-details">
                                    <div class="option-label">Pay Custom Amount</div>
                                    <div class="custom-amount-input" style="display: none;">
                                        <input type="number" 
                                               id="customAmount" 
                                               placeholder="Enter amount"
                                               min="1" 
                                               max="<?php echo $billData['amount_payable']; ?>"
                                               step="0.01"
                                               class="form-control">
                                        <small>Maximum: ₵ <?php echo number_format($billData['amount_payable'], 2); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selectedAmount" value="<?php echo $billData['amount_payable']; ?>">
                    </div>

                    <!-- Payer Information -->
                    <div class="form-section">
                        <h4>Payer Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="payerName">Full Name *</label>
                                <input type="text" 
                                       id="payerName" 
                                       value="<?php echo htmlspecialchars($billData['owner_name']); ?>"
                                       required 
                                       class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="payerEmail">Email Address *</label>
                                <input type="email" 
                                       id="payerEmail" 
                                       placeholder="your.email@example.com"
                                       required 
                                       class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="payerPhone">Phone Number *</label>
                                <input type="tel" 
                                       id="payerPhone" 
                                       value="<?php echo htmlspecialchars($billData['telephone'] ?? ''); ?>"
                                       placeholder="+233 XXX XXX XXX"
                                       required 
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h4>Payment Method</h4>
                        <div class="payment-methods-grid">
                            <div class="payment-method-card active" data-method="card">
                                <div class="method-icon">💳</div>
                                <div class="method-info">
                                    <div class="method-name">Debit/Credit Card</div>
                                    <div class="method-description">Visa, Mastercard, Verve</div>
                                </div>
                                <div class="method-check">✓</div>
                            </div>
                            
                            <div class="payment-method-card" data-method="mobile_money">
                                <div class="method-icon">📱</div>
                                <div class="method-info">
                                    <div class="method-name">Mobile Money</div>
                                    <div class="method-description">MTN, Telecel, AirtelTigo</div>
                                </div>
                                <div class="method-check">○</div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="selectedPaymentMethod" value="card">
                    </div>

                    <!-- Mobile Money Details (hidden by default) -->
                    <div class="form-section" id="mobileMoneySection" style="display: none;">
                        <h4>Mobile Money Details</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="momoProvider">Provider *</label>
                                <select id="momoProvider" class="form-control">
                                    <option value="">Select Provider</option>
                                    <option value="MTN">MTN Mobile Money</option>
                                    <option value="Telecel">Telecel Cash</option>
                                    <option value="AirtelTigo">AirtelTigo Money</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="momoNumber">Mobile Money Number *</label>
                                <input type="tel" 
                                       id="momoNumber" 
                                       placeholder="0XX XXX XXXX"
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="terms-section">
                            <label class="checkbox-container">
                                <input type="checkbox" id="acceptTerms" required>
                                <span class="checkmark"></span>
                                I agree to the 
                                <a href="#" onclick="showTerms()">Terms and Conditions</a> 
                                and 
                                <a href="#" onclick="showPrivacy()">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <div class="payment-submit-section">
                        <div class="payment-summary">
                            <div class="summary-amount">
                                You will pay: <strong id="finalAmount">₵ <?php echo number_format($billData['amount_payable'], 2); ?></strong>
                            </div>
                            <div class="summary-note">
                                <i class="fas fa-info-circle"></i>
                                No additional charges. Your payment is secure and encrypted.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-payment" id="payButton">
                            <i class="fas fa-lock"></i>
                            Pay Securely Now
                        </button>
                        
                        <div class="payment-security">
                            <div class="security-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>256-bit SSL Encryption</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-certificate"></i>
                                <span>PCI DSS Compliant</span>
                            </div>
                            <div class="security-item">
                                <i class="fas fa-lock"></i>
                                <span>Bank-level Security</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Support Section -->
        <div class="payment-support">
            <div class="support-content">
                <h4>🤝 Need Help?</h4>
                <p>Our support team is available to assist you with your payment</p>
                <div class="support-contacts">
                    <a href="tel:+233123456789" class="support-item">
                        <i class="fas fa-phone"></i>
                        <span>+233 123 456 789</span>
                    </a>
                    <a href="mailto:support@<?php echo strtolower(str_replace(' ', '', $assemblyName)); ?>.gov.gh" class="support-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email Support</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Payment Page Styles */
.payment-page {
    padding: 40px 0;
    min-height: 700px;
    background: linear-gradient(135deg, #f0f9ff 0%, #f7fafc 100%);
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Payment Header */
.payment-header {
    text-align: center;
    margin-bottom: 40px;
}

.payment-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    opacity: 0.5;
    transition: all 0.3s;
}

.progress-step.active {
    opacity: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #4a5568;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    transition: all 0.3s;
}

.progress-step.active .step-number {
    background: #667eea;
    color: white;
}

.progress-step span {
    font-size: 0.9rem;
    color: #4a5568;
    font-weight: 500;
}

.progress-step.active span {
    color: #2d3748;
    font-weight: 600;
}

.progress-line {
    width: 60px;
    height: 2px;
    background: #e2e8f0;
    margin: 0 20px;
}

.payment-title h1 {
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 2.2rem;
}

.payment-title p {
    color: #718096;
    font-size: 1.1rem;
}

/* Payment Content */
.payment-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

/* Bill Summary Card */
.bill-summary-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    height: fit-content;
    border: 1px solid #e2e8f0;
    position: sticky;
    top: 100px;
}

.bill-summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
}

.bill-summary-header h3 {
    color: #2d3748;
    margin: 0;
    font-size: 1.2rem;
}

.bill-number {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    font-family: monospace;
}

.bill-summary-details {
    margin-bottom: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f7fafc;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row.total {
    border-top: 2px solid #e2e8f0;
    padding-top: 15px;
    margin-top: 10px;
}

.summary-label {
    color: #4a5568;
    font-size: 0.9rem;
}

.summary-value {
    color: #2d3748;
    font-weight: 600;
    text-align: right;
}

.summary-value.amount {
    color: #d69e2e;
    font-size: 1.1rem;
}

.bill-summary-actions {
    text-align: center;
}

/* Payment Form Card */
.payment-form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.payment-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.payment-form-header h3 {
    color: #2d3748;
    margin: 0;
    font-size: 1.3rem;
}

.security-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f0fff4;
    color: #22543d;
    padding: 6px 12px;
    border-radius: 15px;
    border: 1px solid #9ae6b4;
    font-size: 0.8rem;
    font-weight: 600;
}

.security-badge i {
    color: #38a169;
}

/* Form Sections */
.form-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid #f7fafc;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.form-section h4 {
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 1.1rem;
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    color: #2d3748;
    font-weight: 500;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:invalid {
    border-color: #f56565;
}

/* Amount Selection */
.amount-selection {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.amount-option {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.amount-option:hover {
    border-color: #667eea;
    background: #f0f9ff;
}

.amount-option.active {
    border-color: #667eea;
    background: #f0f9ff;
}

.option-radio {
    color: #667eea;
    font-size: 1.2rem;
    font-weight: bold;
}

.option-details {
    flex: 1;
}

.option-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 3px;
}

.option-amount {
    color: #d69e2e;
    font-weight: bold;
    font-size: 1.1rem;
}

.custom-amount-input {
    margin-top: 10px;
}

.custom-amount-input input {
    margin-bottom: 5px;
}

.custom-amount-input small {
    color: #718096;
    font-size: 0.8rem;
}

/* Payment Methods */
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.payment-method-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.payment-method-card:hover {
    border-color: #667eea;
    background: #f0f9ff;
}

.payment-method-card.active {
    border-color: #667eea;
    background: #f0f9ff;
}

.method-icon {
    font-size: 2rem;
}

.method-info {
    flex: 1;
}

.method-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 3px;
}

.method-description {
    color: #718096;
    font-size: 0.9rem;
}

.method-check {
    color: #667eea;
    font-size: 1.2rem;
    font-weight: bold;
}

/* Terms Section */
.terms-section {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    color: #4a5568;
    font-size: 0.9rem;
    line-height: 1.4;
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #667eea;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    flex-shrink: 0;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark {
    background: #667eea;
    color: white;
}

.checkbox-container input[type="checkbox"]:checked + .checkmark:before {
    content: "✓";
    font-size: 12px;
    font-weight: bold;
}

.checkbox-container a {
    color: #667eea;
    text-decoration: none;
}

.checkbox-container a:hover {
    text-decoration: underline;
}

/* Payment Submit Section */
.payment-submit-section {
    margin-top: 30px;
    text-align: center;
}

.payment-summary {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.summary-amount {
    font-size: 1.2rem;
    color: #22543d;
    margin-bottom: 8px;
}

.summary-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #2f855a;
    font-size: 0.9rem;
}

.btn-payment {
    width: 100%;
    padding: 18px 24px;
    font-size: 1.2rem;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    transition: all 0.3s;
}

.btn-payment:hover {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.3);
}

.btn-payment:disabled {
    background: #e2e8f0;
    color: #a0aec0;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.payment-security {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.security-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #4a5568;
    font-size: 0.8rem;
}

.security-item i {
    color: #48bb78;
}

/* Payment Support */
.payment-support {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    border: 1px solid #e2e8f0;
}

.support-content h4 {
    color: #2d3748;
    margin-bottom: 8px;
}

.support-content p {
    color: #718096;
    margin-bottom: 20px;
}

.support-contacts {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.support-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.support-item:hover {
    color: #5a67d8;
    transform: translateY(-2px);
    text-decoration: none;
}

.support-item i {
    width: 16px;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
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

.btn-sm {
    padding: 8px 16px;
    font-size: 0.9rem;
}

.btn-lg {
    padding: 15px 30px;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .payment-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .bill-summary-card {
        position: static;
        order: 2;
    }
    
    .payment-form-card {
        order: 1;
    }
    
    .payment-progress {
        margin-bottom: 20px;
    }
    
    .progress-line {
        width: 40px;
        margin: 0 10px;
    }
    
    .payment-title h1 {
        font-size: 1.8rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .payment-methods-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-security,
    .support-contacts {
        flex-direction: column;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .payment-form-card,
    .bill-summary-card {
        padding: 20px;
    }
    
    .progress-step span {
        display: none;
    }
    
    .payment-title h1 {
        font-size: 1.5rem;
    }
}
</style>

<!-- PayStack Inline Script -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
// PayStack Configuration
const paystackConfig = {
    publicKey: '<?php echo $paystackPublicKey; ?>',
    testMode: <?php echo $paystackTestMode ? 'true' : 'false'; ?>
};

// Bill Data
const billData = {
    billId: <?php echo $billId; ?>,
    billNumber: '<?php echo htmlspecialchars($billData['bill_number']); ?>',
    accountNumber: '<?php echo htmlspecialchars($billData['account_number']); ?>',
    accountName: '<?php echo htmlspecialchars($billData['account_name']); ?>',
    maxAmount: <?php echo $billData['amount_payable']; ?>
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment form
    initializePaymentForm();
    
    // Set up event listeners
    setupEventListeners();
    
    // Validate PayStack
    if (!paystackConfig.publicKey) {
        console.error('PayStack public key not configured');
        document.getElementById('payButton').disabled = true;
    }
});

function initializePaymentForm() {
    // Initialize amount selection
    updatePaymentAmount();
    
    // Initialize payment method
    updatePaymentMethod();
    
    // Auto-format phone numbers
    formatPhoneNumbers();
}

function setupEventListeners() {
    // Amount selection
    document.querySelectorAll('.amount-option').forEach(option => {
        option.addEventListener('click', function() {
            selectAmountOption(this);
        });
    });
    
    // Payment method selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.addEventListener('click', function() {
            selectPaymentMethod(this);
        });
    });
    
    // Custom amount input
    document.getElementById('customAmount').addEventListener('input', function() {
        updateCustomAmount();
    });
    
    // Form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment();
    });
    
    // Terms checkbox
    document.getElementById('acceptTerms').addEventListener('change', function() {
        updatePaymentButton();
    });
    
    // Form validation
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', updatePaymentButton);
    });
}

function selectAmountOption(option) {
    // Remove active class from all options
    document.querySelectorAll('.amount-option').forEach(opt => {
        opt.classList.remove('active');
        opt.querySelector('.option-radio').textContent = '○';
    });
    
    // Add active class to selected option
    option.classList.add('active');
    option.querySelector('.option-radio').textContent = '●';
    
    // Show/hide custom amount input
    const customInput = document.querySelector('.custom-amount-input');
    const isCustom = option.dataset.amount === 'custom';
    
    if (isCustom) {
        customInput.style.display = 'block';
        document.getElementById('customAmount').focus();
    } else {
        customInput.style.display = 'none';
        document.getElementById('selectedAmount').value = option.dataset.amount;
        updatePaymentAmount();
    }
}

function updateCustomAmount() {
    const customAmount = parseFloat(document.getElementById('customAmount').value) || 0;
    const maxAmount = billData.maxAmount;
    
    if (customAmount > 0 && customAmount <= maxAmount) {
        document.getElementById('selectedAmount').value = customAmount;
        updatePaymentAmount();
    }
}

function updatePaymentAmount() {
    const amount = parseFloat(document.getElementById('selectedAmount').value) || 0;
    const finalAmountElement = document.getElementById('finalAmount');
    
    if (finalAmountElement) {
        finalAmountElement.textContent = `₵ ${amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
    
    updatePaymentButton();
}

function selectPaymentMethod(card) {
    // Remove active class from all cards
    document.querySelectorAll('.payment-method-card').forEach(c => {
        c.classList.remove('active');
        c.querySelector('.method-check').textContent = '○';
    });
    
    // Add active class to selected card
    card.classList.add('active');
    card.querySelector('.method-check').textContent = '✓';
    
    // Update selected method
    const method = card.dataset.method;
    document.getElementById('selectedPaymentMethod').value = method;
    
    // Show/hide mobile money section
    const mobileMoneySection = document.getElementById('mobileMoneySection');
    if (method === 'mobile_money') {
        mobileMoneySection.style.display = 'block';
    } else {
        mobileMoneySection.style.display = 'none';
    }
    
    updatePaymentMethod();
}

function updatePaymentMethod() {
    const method = document.getElementById('selectedPaymentMethod').value;
    const payButton = document.getElementById('payButton');
    
    if (method === 'card') {
        payButton.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Card';
    } else if (method === 'mobile_money') {
        payButton.innerHTML = '<i class="fas fa-mobile-alt"></i> Pay with Mobile Money';
    }
    
    updatePaymentButton();
}

function formatPhoneNumbers() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, ''); // Remove non-digits
            
            // Format Ghana phone number
            if (value.startsWith('233')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+233' + value.substring(1);
            } else if (value.length > 0 && !value.startsWith('+')) {
                value = '+233' + value;
            }
            
            this.value = value;
        });
    });
}

function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    // Remove existing error styling
    field.classList.remove('error');
    
    // Validate based on field type
    switch (field.type) {
        case 'email':
            if (value && !isValidEmail(value)) {
                field.classList.add('error');
            }
            break;
        case 'tel':
            if (value && !isValidPhone(value)) {
                field.classList.add('error');
            }
            break;
        case 'number':
            const numValue = parseFloat(value);
            const max = parseFloat(field.max);
            const min = parseFloat(field.min);
            if (value && (numValue < min || numValue > max)) {
                field.classList.add('error');
            }
            break;
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^\+233[0-9]{9}$/;
    return phoneRegex.test(phone);
}

function updatePaymentButton() {
    const payButton = document.getElementById('payButton');
    const form = document.getElementById('paymentForm');
    const termsAccepted = document.getElementById('acceptTerms').checked;
    const amount = parseFloat(document.getElementById('selectedAmount').value) || 0;
    
    // Check if form is valid
    const isValid = form.checkValidity() && termsAccepted && amount > 0;
    
    payButton.disabled = !isValid;
    
    if (isValid) {
        payButton.classList.remove('btn-disabled');
    } else {
        payButton.classList.add('btn-disabled');
    }
}

function processPayment() {
    const form = document.getElementById('paymentForm');
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Check terms acceptance
    if (!document.getElementById('acceptTerms').checked) {
        alert('Please accept the terms and conditions to proceed.');
        return;
    }
    
    // Get form data
    const paymentData = {
        amount: parseFloat(document.getElementById('selectedAmount').value),
        payerName: document.getElementById('payerName').value.trim(),
        payerEmail: document.getElementById('payerEmail').value.trim(),
        payerPhone: document.getElementById('payerPhone').value.trim(),
        paymentMethod: document.getElementById('selectedPaymentMethod').value,
        billId: billData.billId,
        billNumber: billData.billNumber,
        accountNumber: billData.accountNumber
    };
    
    // Add mobile money data if selected
    if (paymentData.paymentMethod === 'mobile_money') {
        paymentData.momoProvider = document.getElementById('momoProvider').value;
        paymentData.momoNumber = document.getElementById('momoNumber').value.trim();
        
        if (!paymentData.momoProvider || !paymentData.momoNumber) {
            alert('Please fill in all mobile money details.');
            return;
        }
    }
    
    // Show loading
    showLoading('Processing your payment...');
    
    // Process based on payment method
    if (paymentData.paymentMethod === 'card') {
        processCardPayment(paymentData);
    } else if (paymentData.paymentMethod === 'mobile_money') {
        processMobileMoneyPayment(paymentData);
    }
}

function processCardPayment(paymentData) {
    if (typeof PaystackPop === 'undefined') {
        hideLoading();
        alert('Payment system not available. Please try again later.');
        return;
    }
    
    // Generate payment reference
    const reference = 'PAY' + Date.now() + Math.random().toString(36).substr(2, 5).toUpperCase();
    
    const handler = PaystackPop.setup({
        key: paystackConfig.publicKey,
        email: paymentData.payerEmail,
        amount: Math.round(paymentData.amount * 100), // Convert to kobo
        currency: 'GHS',
        ref: reference,
        metadata: {
            bill_id: paymentData.billId,
            bill_number: paymentData.billNumber,
            account_number: paymentData.accountNumber,
            payer_name: paymentData.payerName,
            payer_phone: paymentData.payerPhone
        },
        callback: function(response) {
            hideLoading();
            // Redirect to success page
            window.location.href = `payment_success.php?reference=${response.reference}&bill_id=${paymentData.billId}`;
        },
        onClose: function() {
            hideLoading();
            console.log('Payment cancelled by user');
        }
    });
    
    hideLoading();
    handler.openIframe();
}

function processMobileMoneyPayment(paymentData) {
    // For mobile money, we'll send to our backend to process
    fetch('../api/payments/process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            window.location.href = `payment_success.php?reference=${data.reference}&bill_id=${paymentData.billId}`;
        } else {
            window.location.href = `payment_failed.php?error=${encodeURIComponent(data.message)}&bill_id=${paymentData.billId}`;
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Payment error:', error);
        window.location.href = `payment_failed.php?error=${encodeURIComponent('Payment processing failed')}&bill_id=${paymentData.billId}`;
    });
}

function showTerms() {
    alert('Terms and Conditions\n\n• Payment processing fees may apply\n• All payments are final and non-refundable\n• You must provide accurate payment information\n• Disputes must be reported within 7 days\n• We use secure encryption for all transactions');
}

function showPrivacy() {
    alert('Privacy Policy\n\n• Your personal information is protected\n• Payment data is encrypted and secure\n• We do not share your information with third parties\n• Transaction records are kept for audit purposes\n• Contact us for data protection inquiries');
}

// Add custom styles for form validation
const validationStyles = document.createElement('style');
validationStyles.textContent = `
    .form-control.error {
        border-color: #f56565 !important;
        box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.1) !important;
    }
    
    .btn-disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
`;
document.head.appendChild(validationStyles);
</script>

<?php include 'footer.php'; ?>
>>>>>>> c9ccaba (Initial commit)
