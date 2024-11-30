<?php
require('../connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection; 

if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$customerID = $_SESSION['userID'];
$productID = $_POST['productID'];
$quantity = $_POST['quantity'];
$paymentMethodID = $_POST['paymentMethodID'] ?? null;  

// Check if payment method is provided or not
if (!$paymentMethodID) {
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];

    // Add logic to handle new card information and save it to PaymentMethods table
    $query = "INSERT INTO PaymentMethods (customerID, cardNumber, cvv, expiryDate) VALUES (?, ?, ?, ?)";
    $params = [$customerID, $cardNumber, $cvv, $expiryDate];
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to save payment method']);
        exit;
    }

    // Retrieve the last inserted ID using SCOPE_IDENTITY()
    $paymentMethodIDQuery = "SELECT SCOPE_IDENTITY() AS PaymentMethodID";
    $stmt = sqlsrv_query($connection, $paymentMethodIDQuery);
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to retrieve payment method ID']);
        exit;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $paymentMethodID = $row['PaymentMethodID'];
}

// Create order record in Orders table
$query = "INSERT INTO Orders (customerID, productID, quantity) VALUES (?, ?, ?)";
$params = [$customerID, $productID, $quantity];
$stmt = sqlsrv_query($connection, $query, $params);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to create order']);
    exit;
}

// Retrieve the last inserted order ID using SCOPE_IDENTITY()
$orderIDQuery = "SELECT SCOPE_IDENTITY() AS OrderID";
$stmt = sqlsrv_query($connection, $orderIDQuery);
if ($stmt === false) {
    echo json_encode(['error' => 'Failed to retrieve order ID']);
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$orderID = $row['OrderID'];

echo json_encode(['success' => true, 'orderID' => $orderID]);