<?php
require('../connection.php');
$connectionString = new ConnectionString();
$connection =  $connectionString->connection;
// Get the product ID from the query string
$productID = isset($_GET['productID']) ? intval($_GET['productID']) : 0;

if ($productID > 0) {
    $query = "SELECT productName, productCost, productQuantity, categoryID FROM Products WHERE uniqueID = ?";
    $stmt = sqlsrv_query($connection, $query, [$productID]);

    if ($stmt === false) {
        die(json_encode(['error' => sqlsrv_errors()]));
    }

    $product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    echo json_encode($product);
    sqlsrv_free_stmt($stmt);
} else {
    echo json_encode(['error' => 'Invalid product ID']);
}

sqlsrv_close($connection);
?>