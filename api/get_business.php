<?php

header("Content-Type: application/json");

$host = '127.0.0.1';      // or RDS endpoint
$dbname = 'quickbill_305';
$user = 'francis';
$pass = 'Mum@vida1';

try {
    // Connect to DB
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Get account_number from query string
    if (!isset($_GET['account_number'])) {
        echo json_encode(['error' => 'account_number parameter is required']);
        exit;
    }

    $account_number = $_GET['account_number'];

    // Prepare and execute query
    $stmt = $pdo->prepare("SELECT * FROM businesses WHERE account_number = :account_number");
    $stmt->execute(['account_number' => $account_number]);

 $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Business not found']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
