<?php
require('../connection.php');


$connectionString = new ConnectionString();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        fetchUsers($connectionString->connection);
        break;
    case 'POST':
        addUser($connectionString->connection);
        break;
    case 'PUT':
        updateUser($connectionString->connection);
        break;
    case 'DELETE':
        deleteUser($connectionString->connection);
        break;
    default:
        http_response_code(405); // Method Not Allowed
        header('Content-Type: application/json');
        echo json_encode(["error" => "Unsupported HTTP method"]);
        break;
}

sqlsrv_close($connectionString->connection); // Close the connection


function fetchUsers($connection) {
    header('Content-Type: application/json');

    $sql = "SELECT uniqueID, username, firstName, lastName, address, DOB, RoleID, email FROM Users";
    $stmt = sqlsrv_query($connection, $sql);

    if ($stmt === false) {
        echo json_encode(["error" => "Failed to fetch users", "details" => sqlsrv_errors()]);
        return;
    }

    $users = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $users[] = $row;
    }

    echo json_encode($users);
}


function addUser($connection) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['username'], $input['firstName'], $input['lastName'], $input['address'], $input['DOB'], $input['RoleID'], $input['Password'], $input['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $sql = "INSERT INTO Users (username, firstName, lastName, address, DOB, RoleID, Password, email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        $input['username'],
        $input['firstName'],
        $input['lastName'],
        $input['address'],
        $input['DOB'],
        $input['RoleID'],
        password_hash($input['Password'], PASSWORD_DEFAULT), // Hash the password
        $input['email']
    ];

    sqlsrv_begin_transaction($connection); // Start a transaction
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        sqlsrv_rollback($connection); // Rollback on failure
        echo json_encode(["error" => "Failed to add user", "details" => sqlsrv_errors()]);
        return;
    }

    sqlsrv_commit($connection); // Commit on success
    echo json_encode(["success" => "User added successfully"]);
}


function updateUser($connection) {
     // Decode the input data
     $input = json_decode(file_get_contents('php://input'), true);

     // Check for invalid JSON
     if ($input === null) {
         http_response_code(400); // Bad Request
         echo json_encode(["error" => "Invalid JSON input"]);
         return;
     }
 
     // Validate input fields
     if (!isset($input['firstName'], $input['lastName'], $input['RoleID'], $input['uniqueID'])) {
         http_response_code(400); // Bad Request
         echo json_encode(["error" => "Missing required fields"]);
         return;
     }
     $RoleID = (int)$input['RoleID'];
     $sql = "UPDATE Users SET firstName = ?, lastName = ?, RoleID = ? WHERE uniqueID = ?";
     $params = [
         $input['firstName'],
         $input['lastName'],
         $RoleID,
         $input['uniqueID']
     ];
 
    sqlsrv_begin_transaction($connection); // Start a transaction
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        sqlsrv_rollback($connection); // Rollback on failure
        echo json_encode(["error" => "Failed to update user", "details" => sqlsrv_errors()]);
        return;
    }

    sqlsrv_commit($connection); // Commit on success
    echo json_encode(["success" => "User updated successfully"]);
}


function deleteUser($connection) {
    header('Content-Type: application/json');
    parse_str(file_get_contents('php://input'), $input);

    if (!isset($input['uniqueID'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Missing uniqueID"]);
        return;
    }

    $sql = "DELETE FROM Users WHERE uniqueID = ?";
    $params = [$input['uniqueID']];

    sqlsrv_begin_transaction($connection); // Start a transaction
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        sqlsrv_rollback($connection); // Rollback on failure
        echo json_encode(["error" => "Failed to delete user", "details" => sqlsrv_errors()]);
        return;
    }

    sqlsrv_commit($connection); // Commit on success
    echo json_encode(["success" => "User deleted successfully"]);
}
?>