<<<<<<< HEAD
 
=======
 <?php
/**
 * Public Portal - Payment Success for QUICKBILL 305
 * Displays payment confirmation and receipt
 */

// Define application constant
define('QUICKBILL_305', true);

// Include configuration files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Start session for public portal
session_start();

// Get parameters from URL
$paymentReference = isset($_GET['reference']) ? sanitizeInput($_GET['reference']) : '';
$billId = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;

if (!$paymentReference || !$billId) {
    setFlashMessage('error', 'Invalid payment reference or bill ID.');
    header('Location: search_bill.php');
    exit();
}

// Initialize variables
$paymentData = null;
$billData = null;
$accountData = null;
$assemblyName = getSystemSetting('assembly_name', 'Municipal Assembly');

try {
    $db = new Database();
    
    // Get payment details
    $paymentQuery = "
        SELECT 
            p.payment_id,
            p.payment_reference,
            p.bill_id,
            p.amount_paid,
            p.payment_method,
            p.payment_channel,
            p.transaction_id,
            p.payment_status,
            p.payment_date,
            p.processed_by,
            p.notes
        FROM payments p
        WHERE p.payment_reference = ? AND p.bill_id = ?
        ORDER BY p.payment_date DESC
        LIMIT 1
    ";
    
    $paymentData = $db->fetchRow($paymentQuery, [$paymentReference, $billId]);
    
    if (!$paymentData) {
        // Payment might be processing, show loading state
        $isProcessing = true;
    } else {
        $isProcessing = false;
        
        // Get bill details
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
    }
    
} catch (Exception $e) {
    writeLog("Payment success page error: " . $e->getMessage(), 'ERROR');
    $isProcessing = false;
    $paymentData = null;
}

include 'header.php';
?>

<?php if (isset($isProcessing) && $isProcessing): ?>
<!-- Payment Processing State -->
<div class="payment-processing-page">
    <div class="container">
        <div class="processing-card">
            <div class="processing-animation">
                <div class="spinner"></div>
                <div class="processing-icon">💳</div>
            </div>
            
            <h1>⏳ Processing Your Payment</h1>
            <p>Please wait while we confirm your payment...</p>
            
            <div class="processing-details">
                <div class="detail-row">
                    <span>Payment Reference:</span>
                    <span class="reference"><?php echo htmlspecialchars($paymentReference); ?></span>
                </div>
                <div class="detail-row">
                    <span>Bill ID:</span>
                    <span><?php echo $billId; ?></span>
                </div>
            </div>
            
            <div class="processing-actions">
                <button onclick="checkPaymentStatus()" class="btn btn-primary" id="checkStatusBtn">
                    <i class="fas fa-sync"></i>
                    Check Status
                </button>
                
                <a href="verify_payment.php" class="btn btn-outline">
                    <i class="fas fa-search"></i>
                    Manual Verification
                </a>
            </div>
            
            <div class="processing-note">
                <i class="fas fa-info-circle"></i>
                <p>Payment confirmation usually takes 1-2 minutes. If processing takes longer, please use the verification page.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh to check payment status
let checkCount = 0;
const maxChecks = 10;

function checkPaymentStatus() {
    const btn = document.getElementById('checkStatusBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
    btn.disabled = true;
    
    setTimeout(() => {
        window.location.reload();
    }, 2000);
}

// Auto-check every 5 seconds for up to 10 times
const autoCheck = setInterval(() => {
    checkCount++;
    if (checkCount >= maxChecks) {
        clearInterval(autoCheck);
        return;
    }
    
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            if (!html.includes('payment-processing-page')) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.log('Auto-check failed:', error);
        });
}, 5000);
</script>

<?php elseif ($paymentData && $paymentData['payment_status'] === 'Successful'): ?>
<!-- Payment Success State -->
<div class="payment-success-page">
    <div class="container">
        <!-- Success Header -->
        <div class="success-header">
            <div class="success-animation">
                <div class="checkmark-circle">
                    <div class="checkmark">✓</div>
                </div>
            </div>
            
            <h1>🎉 Payment Successful!</h1>
            <p>Your payment has been processed successfully</p>
            
            <div class="success-amount">
                ₵ <?php echo number_format($paymentData['amount_paid'], 2); ?>
            </div>
        </div>

        <!-- Payment Receipt -->
        <div class="payment-receipt">
            <div class="receipt-header">
                <div class="receipt-title">
                    <h3>📧 Payment Receipt</h3>
                    <span class="receipt-status success">PAID</span>
                </div>
                <div class="receipt-reference">
                    <strong>Ref: <?php echo htmlspecialchars($paymentData['payment_reference']); ?></strong>
                </div>
            </div>
            
            <div class="receipt-content">
                <!-- Assembly Information -->
                <div class="receipt-section">
                    <div class="assembly-info">
                        <h4><?php echo htmlspecialchars($assemblyName); ?></h4>
                        <p>Official Payment Receipt</p>
                        <p>Date: <?php echo formatDateTime($paymentData['payment_date']); ?></p>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="receipt-section">
                    <h5>Payment Details</h5>
                    <div class="receipt-grid">
                        <div class="receipt-row">
                            <span>Payment Reference:</span>
                            <span class="value"><?php echo htmlspecialchars($paymentData['payment_reference']); ?></span>
                        </div>
                        <div class="receipt-row">
                            <span>Payment Date:</span>
                            <span class="value"><?php echo formatDateTime($paymentData['payment_date']); ?></span>
                        </div>
                        <div class="receipt-row">
                            <span>Payment Method:</span>
                            <span class="value">
                                <?php 
                                $methodIcon = $paymentData['payment_method'] === 'Mobile Money' ? '📱' : '💳';
                                echo $methodIcon . ' ' . htmlspecialchars($paymentData['payment_method']); 
                                ?>
                                <?php if (!empty($paymentData['payment_channel'])): ?>
                                    - <?php echo htmlspecialchars($paymentData['payment_channel']); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php if (!empty($paymentData['transaction_id'])): ?>
                        <div class="receipt-row">
                            <span>Transaction ID:</span>
                            <span class="value"><?php echo htmlspecialchars($paymentData['transaction_id']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="receipt-row">
                            <span>Status:</span>
                            <span class="value success">✅ Successful</span>
                        </div>
                    </div>
                </div>
                
                <!-- Bill Information -->
                <?php if ($billData): ?>
                <div class="receipt-section">
                    <h5>Bill Information</h5>
                    <div class="receipt-grid">
                        <div class="receipt-row">
                            <span>Bill Number:</span>
                            <span class="value"><?php echo htmlspecialchars($billData['bill_number']); ?></span>
                        </div>
                        <div class="receipt-row">
                            <span>Account Number:</span>
                            <span class="value"><?php echo htmlspecialchars($billData['account_number']); ?></span>
                        </div>
                        <div class="receipt-row">
                            <span>Account Name:</span>
                            <span class="value"><?php echo htmlspecialchars($billData['account_name']); ?></span>
                        </div>
                        <div class="receipt-row">
                            <span>Bill Type:</span>
                            <span class="value">
                                <?php echo $billData['bill_type'] === 'Business' ? '🏢 Business Permit' : '🏠 Property Rates'; ?>
                            </span>
                        </div>
                        <div class="receipt-row">
                            <span>Billing Year:</span>
                            <span class="value"><?php echo $billData['billing_year']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Amount Breakdown -->
                <div class="receipt-section">
                    <h5>Amount Details</h5>
                    <div class="amount-breakdown">
                        <div class="amount-row">
                            <span>Amount Paid:</span>
                            <span class="amount">₵ <?php echo number_format($paymentData['amount_paid'], 2); ?></span>
                        </div>
                        <?php if ($billData && $billData['amount_payable'] > 0): ?>
                        <div class="amount-row">
                            <span>Remaining Balance:</span>
                            <span class="amount">₵ <?php echo number_format($billData['amount_payable'], 2); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="amount-row success">
                            <span><strong>Bill Status:</strong></span>
                            <span class="amount"><strong>✅ Fully Paid</strong></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Notes -->
                <?php if (!empty($paymentData['notes'])): ?>
                <div class="receipt-section">
                    <h5>Notes</h5>
                    <p class="receipt-notes"><?php echo htmlspecialchars($paymentData['notes']); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="receipt-footer">
                    <p><strong>Thank you for your payment!</strong></p>
                    <p>For inquiries about this payment, please contact us with the payment reference number.</p>
                    <div class="footer-contact">
                        <span>📞 +233 123 456 789</span>
                        <span>📧 support@<?php echo strtolower(str_replace(' ', '', $assemblyName)); ?>.gov.gh</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="success-actions">
            <button onclick="downloadReceipt()" class="btn btn-primary btn-lg">
                <i class="fas fa-download"></i>
                Download Receipt
            </button>
            
            <button onclick="printReceipt()" class="btn btn-outline">
                <i class="fas fa-print"></i>
                Print Receipt
            </button>
            
            <button onclick="shareReceipt()" class="btn btn-secondary" id="shareBtn">
                <i class="fas fa-share"></i>
                Share Receipt
            </button>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <h3>✨ What's Next?</h3>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon">📧</div>
                    <h4>Email Confirmation</h4>
                    <p>A confirmation email will be sent to your email address within 5 minutes</p>
                </div>
                
                <div class="step-card">
                    <div class="step-icon">📱</div>
                    <h4>SMS Notification</h4>
                    <p>You'll receive an SMS confirmation with your payment details</p>
                </div>
                
                <div class="step-card">
                    <div class="step-icon">🏢</div>
                    <h4>Office Records</h4>
                    <p>Your payment will be updated in our office records within 1 hour</p>
                </div>
            </div>
        </div>

        <!-- Additional Actions -->
        <div class="additional-actions">
            <a href="search_bill.php" class="btn btn-outline">
                <i class="fas fa-search"></i>
                Pay Another Bill
            </a>
            
            <a href="verify_payment.php" class="btn btn-secondary">
                <i class="fas fa-check-circle"></i>
                Verify Another Payment
            </a>
            
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Payment Failed/Not Found State -->
<div class="payment-error-page">
    <div class="container">
        <div class="error-card">
            <div class="error-icon">❌</div>
            <h1>Payment Not Found</h1>
            <p>We couldn't find a successful payment with the provided reference.</p>
            
            <div class="error-details">
                <div class="detail-row">
                    <span>Payment Reference:</span>
                    <span class="reference"><?php echo htmlspecialchars($paymentReference); ?></span>
                </div>
                <div class="detail-row">
                    <span>Bill ID:</span>
                    <span><?php echo $billId; ?></span>
                </div>
            </div>
            
            <div class="error-suggestions">
                <h4>💡 What you can do:</h4>
                <ul>
                    <li>Wait a few more minutes for payment processing to complete</li>
                    <li>Check your payment method for any issues</li>
                    <li>Use the payment verification page to check status</li>
                    <li>Contact support if the problem persists</li>
                </ul>
            </div>
            
            <div class="error-actions">
                <a href="verify_payment.php" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Verify Payment
                </a>
                
                <a href="pay_bill.php?bill_id=<?php echo $billId; ?>" class="btn btn-outline">
                    <i class="fas fa-redo"></i>
                    Try Payment Again
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* Payment Success Page Styles */
.payment-success-page,
.payment-processing-page,
.payment-error-page {
    padding: 40px 0;
    min-height: 700px;
    background: linear-gradient(135deg, #f0fff4 0%, #f7fafc 100%);
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Success Header */
.success-header {
    text-align: center;
    margin-bottom: 40px;
}

.success-animation {
    margin-bottom: 30px;
}

.checkmark-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: scaleIn 0.5s ease;
    box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
}

.checkmark {
    color: white;
    font-size: 3rem;
    font-weight: bold;
    animation: checkmarkDraw 0.3s ease 0.2s both;
}

.success-header h1 {
    color: #22543d;
    margin-bottom: 10px;
    font-size: 2.5rem;
}

.success-header p {
    color: #2f855a;
    font-size: 1.1rem;
    margin-bottom: 20px;
}

.success-amount {
    font-size: 3rem;
    font-weight: bold;
    color: #48bb78;
    text-shadow: 0 2px 4px rgba(72, 187, 120, 0.3);
}

/* Payment Receipt */
.payment-receipt {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
    border: 1px solid #e2e8f0;
}

.receipt-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.receipt-title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.receipt-title h3 {
    margin: 0;
    font-size: 1.3rem;
}

.receipt-status {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.receipt-status.success {
    background: rgba(72, 187, 120, 0.2);
    color: #f0fff4;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.receipt-reference {
    font-family: monospace;
    font-size: 1.1rem;
}

.receipt-content {
    padding: 30px;
}

.receipt-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f7fafc;
}

.receipt-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.assembly-info {
    text-align: center;
    margin-bottom: 20px;
}

.assembly-info h4 {
    color: #2d3748;
    margin-bottom: 5px;
    font-size: 1.3rem;
}

.assembly-info p {
    color: #4a5568;
    margin: 0;
}

.receipt-section h5 {
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.receipt-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
}

.receipt-row span:first-child {
    color: #4a5568;
    font-weight: 500;
}

.receipt-row .value {
    color: #2d3748;
    font-weight: 600;
    text-align: right;
    font-family: monospace;
}

.receipt-row .value.success {
    color: #38a169;
}

.amount-breakdown {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.amount-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e2e8f0;
}

.amount-row:last-child {
    border-bottom: none;
}

.amount-row.success {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    border-radius: 6px;
    padding: 10px;
    margin-top: 10px;
}

.amount-row .amount {
    font-weight: bold;
    color: #2d3748;
    font-family: monospace;
}

.receipt-notes {
    background: #f0f9ff;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #bae6fd;
    color: #1e40af;
    font-style: italic;
}

.receipt-footer {
    text-align: center;
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.receipt-footer p {
    margin-bottom: 10px;
    color: #4a5568;
}

.footer-contact {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.footer-contact span {
    color: #667eea;
    font-weight: 500;
    font-size: 0.9rem;
}

/* Action Buttons */
.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

/* Next Steps */
.next-steps {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.next-steps h3 {
    color: #2d3748;
    margin-bottom: 25px;
    font-size: 1.3rem;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.step-card {
    background: #f7fafc;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    text-align: center;
}

.step-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.step-card h4 {
    color: #2d3748;
    margin-bottom: 10px;
    font-size: 1rem;
}

.step-card p {
    color: #4a5568;
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Additional Actions */
.additional-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Processing Page Styles */
.processing-card,
.error-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.processing-animation {
    position: relative;
    margin-bottom: 30px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner {
    width: 100px;
    height: 100px;
    border: 4px solid #e2e8f0;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    position: absolute;
}

.processing-icon {
    font-size: 3rem;
    position: absolute;
    z-index: 1;
}

.processing-card h1,
.error-card h1 {
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 2rem;
}

.processing-card p,
.error-card p {
    color: #4a5568;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.processing-details,
.error-details {
    background: #f7fafc;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    border: 1px solid #e2e8f0;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.detail-row .reference {
    font-family: monospace;
    font-weight: bold;
    color: #667eea;
}

.processing-actions,
.error-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.processing-note {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    padding: 15px;
    border-radius: 8px;
    color: #1e40af;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
}

.processing-note i {
    color: #3b82f6;
    flex-shrink: 0;
}

/* Error Page Styles */
.payment-error-page {
    background: linear-gradient(135deg, #fff5f5 0%, #f7fafc 100%);
}

.error-icon {
    font-size: 5rem;
    margin-bottom: 20px;
}

.error-suggestions {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: left;
}

.error-suggestions h4 {
    color: #1e40af;
    margin-bottom: 15px;
}

.error-suggestions ul {
    color: #1e40af;
    line-height: 1.6;
}

.error-suggestions li {
    margin-bottom: 8px;
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

/* Animations */
@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes checkmarkDraw {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Print Styles */
@media print {
    .success-actions,
    .next-steps,
    .additional-actions,
    .processing-actions,
    .error-actions {
        display: none !important;
    }
    
    .payment-receipt {
        box-shadow: none;
        border: 1px solid #000;
    }
    
    .receipt-header {
        background: #f8f9fa !important;
        color: #000 !important;
    }
    
    body {
        background: white !important;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .success-actions,
    .additional-actions,
    .processing-actions,
    .error-actions {
        flex-direction: column;
    }
    
    .steps-grid {
        grid-template-columns: 1fr;
    }
    
    .receipt-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .receipt-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .receipt-row .value {
        text-align: left;
    }
    
    .footer-contact {
        flex-direction: column;
        gap: 10px;
    }
    
    .success-header h1 {
        font-size: 2rem;
    }
    
    .success-amount {
        font-size: 2.5rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .processing-card,
    .error-card,
    .payment-receipt,
    .next-steps {
        padding: 25px;
    }
    
    .checkmark-circle {
        width: 80px;
        height: 80px;
    }
    
    .checkmark {
        font-size: 2rem;
    }
    
    .success-amount {
        font-size: 2rem;
    }
}
</style>

<script>
// Download receipt functionality
function downloadReceipt() {
    // Create a clean version for download
    const receiptContent = document.querySelector('.payment-receipt').cloneNode(true);
    
    // Create download window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt_<?php echo htmlspecialchars($paymentData['payment_reference'] ?? 'Unknown'); ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .payment-receipt { background: white; }
                .receipt-header { background: #f8f9fa !important; color: #000 !important; padding: 20px; border-bottom: 2px solid #000; }
                .receipt-content { padding: 20px; }
                .receipt-grid { margin-bottom: 20px; }
                .receipt-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee; }
                .amount-breakdown { background: #f8f9fa; padding: 15px; margin: 10px 0; }
                .receipt-footer { background: #f8f9fa; padding: 15px; text-align: center; margin-top: 20px; }
            </style>
        </head>
        <body>
            ${receiptContent.outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Print receipt functionality
function printReceipt() {
    window.print();
}

// Share receipt functionality
function shareReceipt() {
    const shareData = {
        title: 'Payment Receipt - <?php echo htmlspecialchars($paymentData['payment_reference'] ?? 'Unknown'); ?>',
        text: 'Payment successful! Reference: <?php echo htmlspecialchars($paymentData['payment_reference'] ?? 'Unknown'); ?>',
        url: window.location.href
    };
    
    if (navigator.share) {
        navigator.share(shareData)
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing', error));
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            const shareBtn = document.getElementById('shareBtn');
            const originalText = shareBtn.innerHTML;
            shareBtn.innerHTML = '<i class="fas fa-check"></i> Link Copied!';
            shareBtn.style.background = '#48bb78';
            
            setTimeout(() => {
                shareBtn.innerHTML = originalText;
                shareBtn.style.background = '';
            }, 2000);
        });
    }
}

// Auto-scroll to receipt on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animations
    const elements = document.querySelectorAll('.payment-receipt, .next-steps, .success-actions');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'all 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 200 * (index + 1));
    });
    
    // Show confetti effect (if available)
    if (typeof confetti !== 'undefined') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
});
</script>

<?php include 'footer.php'; ?>
>>>>>>> c9ccaba (Initial commit)
