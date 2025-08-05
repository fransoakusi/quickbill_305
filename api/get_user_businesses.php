<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$phone_number = $_GET['phone_number'];

// Query database for user's businesses
// For demo, return sample data
$businesses = [
    [

        // ... other fields
    ],
    // Add more businesses
];

echo json_encode(['success' => true, 'businesses' => $businesses]);
?>