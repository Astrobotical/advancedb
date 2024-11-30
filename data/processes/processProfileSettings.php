<?php
require('../connection.php');
$connectionString = new ConnectionString();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userID = $_SESSION['userID']; 
    if (!$userID) {
        http_response_code(403); 
        echo json_encode(["error" => "User not authenticated"]);
        exit();
    }

    $sql = "SELECT username, firstName, lastName, address, DOB, email FROM Users WHERE uniqueID = ?";
    $params = [$userID];
    $stmt = sqlsrv_query($connectionString->connection, $sql, $params);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to fetch user data", "details" => sqlsrv_errors()]);
        exit();
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if (!$user) {
        echo json_encode(["error" => "User not found"]);
        exit();
    }

    echo json_encode(["success" => true, "user" => $user]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Unsupported HTTP method"]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['username'], $input['firstName'], $input['lastName'], $input['address'], $input['dob'], $input['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Missing required fields"]);
        exit();
    }

    $userID = $_SESSION['userID']; // Ensure user is authenticated and get their ID
    if (!$userID) {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "User not authenticated"]);
        exit();
    }

    // Prepare and execute update query
    $sql = "UPDATE Users SET 
            username = ?, 
            firstName = ?, 
            lastName = ?, 
            address = ?, 
            DOB = ?, 
            email = ? 
            WHERE uniqueID = ?";
    $params = [
        $input['username'],
        $input['firstName'],
        $input['lastName'],
        $input['address'],
        $input['dob'],
        $input['email'],
        $userID
    ];

    sqlsrv_begin_transaction($connectionString->connection); // Begin transaction

    $stmt = sqlsrv_query($connectionString->connection, $sql, $params);
    if ($stmt === false) {
        sqlsrv_rollback($connectionString->connection); // Rollback on failure
        echo json_encode(["error" => "Failed to update user", "details" => sqlsrv_errors()]);
        exit();
    }

    sqlsrv_commit($connectionString->connection); // Commit transaction
    echo json_encode(["success" => "Profile updated successfully"]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Unsupported HTTP method"]);
}
?>