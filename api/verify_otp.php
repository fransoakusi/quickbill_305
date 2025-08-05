<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents('php://input'), true);
$phone_number = $input['phone_number'];
$otp = $input['otp'];

// In production, verify against stored OTP
// For demo, accept "1234" as valid
$success = ($otp === '1234');

echo json_encode(['success' => $success]);
?>

