
<?php
require('../connection.php');
$connectionString = new ConnectionString();
$connection =  $connectionString->connection;
switch ($_SERVER['REQUEST_METHOD']){
    case 'GET':
        break;
    case 'POST':
        addPaymentMethod();
        break;
    case 'Update':
    break;
}

function addPaymentMethod(){
    header('Content-Type: application/json');
    $requestBody = json_decode(file_get_contents('php://input'), true);
    global $connection;
    $customerID = $_SESSION['userID'];
    $paymentMethodID = $requestBody['paymentMethodID'] ?? null;  

// Check if payment method is provided or not
if (Empty($paymentMethodID)) {
    $cardNumber = $requestBody['cardNumber'];
    $expiryDate = DateTime::createFromFormat('Y-m', $requestBody['expiryDate']);
    $formattedexpiryDate =  $expiryDate->format('Y-m-01');
    $cvv = $requestBody['cvv'];

    // Add logic to handle new card information and save it to PaymentMethods table
    $query = "INSERT INTO PaymentMethods (customerID, cardNumber, cvv, expiryDate) VALUES (?, ?, ?, ?)";
    $params = [$customerID, $cardNumber, $cvv, $formattedexpiryDate];
    sqlsrv_begin_transaction($connection);
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to save payment method','more'=> sqlsrv_errors()]);
        sqlsrv_rollback($connection);
        exit;
    }
    sqlsrv_commit($connection);
    //echo json_encode(['success'=>'Successfully added the payment method']);


    // Retrieve the last inserted ID using SCOPE_IDENTITY()
    $paymentMethodIDQuery = "SELECT uniqueID, customerID, cardNumber, cvv, expiryDate, DateAdded FROM PaymentMethods ORDER BY DateAdded DESC";
    $stmt = sqlsrv_query($connection, $paymentMethodIDQuery);
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to retrieve payment method ID']);
        exit;
    }
    http_response_code(200);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    $paymentMethodID = $row;
    $paymentCard =  $maskedCard = substr($row['cardNumber'], 0, 4) . " **** **** " . substr($row['cardNumber'], -4);
    echo json_encode(['success'=>'Successfully added the payment method', 
    'paymentMethodID'=> $paymentMethodID['uniqueID'],'paymentCard'=> $paymentCard]);
}
}