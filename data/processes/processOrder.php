<?php
require('../connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection; 

// Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$customerID = $_SESSION['userID'];
$method = $_SERVER['REQUEST_METHOD']; 

switch ($method) {
    case 'POST':
        handleCreateOrder();
        break;

    case 'GET':
        handleGetOrder();
        break;

    default:
        echo json_encode(['error' => 'Invalid request method']);
        break;
}

function handleCreateOrder() {
    global $connection, $customerID;

    // Decode the incoming JSON payload
    $input = json_decode(file_get_contents('php://input'), true);
    $paymentMethodID = (int)$input['paymentMethodID'] ?? null;
    $totalCost = $input['totalCost'] ?? null;

    if (!$paymentMethodID || !$totalCost) {
        echo json_encode(['error' => 'Payment method or total cost missing']);
        exit;
    }

    try {
        sqlsrv_begin_transaction($connection);

        // Generate a unique 6-character order ID
        $uuidQuery = "SELECT LEFT(NEWID(), 6) AS OrderUUID";
        $uuidStmt = sqlsrv_query($connection, $uuidQuery);
        if ($uuidStmt === false) {
            throw new Exception('Failed to generate unique order ID: ' . print_r(sqlsrv_errors(), true));
        }
        $uuidRow = sqlsrv_fetch_array($uuidStmt, SQLSRV_FETCH_ASSOC);
        $uniqueOrderID = $uuidRow['OrderUUID'];

        // Fetch all items from the cart
        $cartQuery = "SELECT productID, quantity FROM Cart WHERE customerID = ?";
        $cartStmt = sqlsrv_query($connection, $cartQuery,[$customerID]);
        if ($cartStmt === false) {
            throw new Exception('Failed to fetch cart items: ' . print_r(sqlsrv_errors(), true));
        }

        while ($cartItem = sqlsrv_fetch_array($cartStmt, SQLSRV_FETCH_ASSOC)) {
            $productID = $cartItem['productID'];
            $quantity = $cartItem['quantity'];

            $orderInsertQuery = "INSERT INTO Orders (orderID, customerID, productID, quantity, paymentMethodID) VALUES (?, ?, ?, ?, ?)";
            $orderInsertParams = [$uniqueOrderID, $customerID, $productID, $quantity, $paymentMethodID];
            $orderInsertStmt = sqlsrv_query($connection, $orderInsertQuery, $orderInsertParams);

            if ($orderInsertStmt === false) {
                throw new Exception('Failed to add product to order: ' . print_r(sqlsrv_errors(), true));
            }
        }

        // Clear the cart
        $clearCartQuery = "DELETE FROM Cart WHERE customerID = ?";
        $clearCartStmt = sqlsrv_query($connection, $clearCartQuery, [$customerID]);
        if ($clearCartStmt === false) {
            throw new Exception('Failed to clear cart: ' . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_commit($connection);
        echo json_encode(['success' => true, 'orderID' => $uniqueOrderID]);

    } catch (Exception $e) {
        sqlsrv_rollback($connection);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handleGetOrder() {
    global $connection, $customerID;
    
    $query = "
    SELECT 
        o.orderID, 
        o.Date AS orderDate,
         p.uniqueID AS productID, 
        p.productName,
        p.productImage,
        p.productCost AS price,
        o.quantity
    FROM Orders o
    JOIN Products p ON o.productID = p.uniqueID
    WHERE o.customerID = ?
    ORDER BY o.orderID, o.Date "; // Group by orderID, and sort by date

// Execute the query
$stmt = sqlsrv_query($connection, $query, [$customerID]);

// Check for SQL errors
if ($stmt === false) {
    $errors = sqlsrv_errors(); // Capture any SQL errors
    echo json_encode(['error' => 'Failed to fetch orders', 'details' => $errors]);
    exit;
}

$orders = [];
$costSummary = 0;
$costTotal = 0;
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $costSummary = 0;

    $orderID = $row['orderID'];

    // Ensure the order is added only once
    if (!isset($orders[$orderID])) {
        //$costTotal += ($row['quantity']*number_format($row['price'], 2))+(($row['quantity']*number_format($row['price'], 2))*0.15);
        $orders[$orderID] = [
            'orderID' => $orderID,
            'orderDate' => $row['orderDate']->format('Y-m-d H:i:s'),
            'items' => [],
        ];
    }

    // Check if the product is already added for this order to avoid duplicates
    $productExists = false;
    foreach ($orders[$orderID]['items'] as $item) {
        if ($item['productID'] === $row['productID']) {
            $productExists = true;
            break;
        }
    }
    if (!$productExists) {
        $orders[$orderID]['items'][] = [
            'productID' => $row['productID'],
            'productName' => $row['productName'],
            'productImage' => $row['productImage'],
            'price' => number_format($row['price'], 2),
            'quantity' => $row['quantity'],
        ];
        $costTotal = 0;
    }
    $costTotal = $costSummary*0.15;

}

// Re-index the array to return a clean list of orders
$response = array_values($orders);

echo json_encode($response);
}

function clearCart($customerID) {
    global $connection;
    $query = "DELETE FROM Cart WHERE customerID = ?";
    $params = [$customerID];
    $stmt = sqlsrv_query($connection, $query, $params);

    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to clear cart']);
        exit;
    }
}
?>