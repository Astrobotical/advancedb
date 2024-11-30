<?php

require('../connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection;
$method = $_GET['method'] ?? 'GET';

switch ($method) {
    case 'GET':
        fetchCartItems();
        break;
    case 'UPDATE':
        updateCartItem();
        break;
    case 'DELETE':
        deleteCartItem();
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Invalid method"]);
        break;
}

function fetchCartItems() {
    global $connection;

    $customerID = $_SESSION['userID']; // Ensure user is logged in and session is active
    $sql = "
        SELECT 
            c.uniqueID, 
            p.productName, 
            p.productCost, 
            p.productImage, 
            cat.type AS categoryType, 
            c.quantity 
        FROM Cart c
        JOIN Products p ON c.productID = p.uniqueID
        JOIN Categories cat ON p.CategoryID = cat.uniqueID
        WHERE c.customerID = ?
    ";
    $stmt = sqlsrv_query($connection, $sql, [$customerID]);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch cart items", "details" => sqlsrv_errors()]);
        return;
    }

    $cartItems = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $cartItems[] = $row;
    }

    echo json_encode($cartItems);
}

function updateCartItem() {
    global $connection;

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['uniqueID'], $input['quantity'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $sql = "UPDATE Cart SET quantity = ? WHERE uniqueID = ?";
    $params = [$input['quantity'], $input['uniqueID']];
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update cart item", "details" => sqlsrv_errors()]);
        return;
    }

    echo json_encode(["success" => "Cart item updated successfully"]);
}

function deleteCartItem() {
    global $connection;

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['uniqueID'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing cart item ID"]);
        return;
    }

    $sql = "DELETE FROM Cart WHERE uniqueID = ?";
    $params = [$input['uniqueID']];
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete cart item", "details" => sqlsrv_errors()]);
        return;
    }

    echo json_encode(["success" => "Cart item deleted successfully"]);
}
?>