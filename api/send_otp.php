<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$phone_number = json_decode(file_get_contents('php://input'), true)['phone_number'];

// In production, integrate with SMS service (Twilio, etc.)
// For demo, always return success
echo json_encode(['success' => true, 'message' => 'OTP sent']);
?>

