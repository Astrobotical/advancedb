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
        http_response_code(405); 
        header('Content-Type: application/json');
        echo json_encode(["error" => "Unsupported HTTP method"]);
        break;
}

sqlsrv_close($connectionString->connection);

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
        http_response_code(400); 
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
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input === null) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid JSON input"]);
        return;
    }



    $commit = $input['commit'];

    if ($commit <= 0) {
        // Rollback scenario for non-positive commit value
        sqlsrv_begin_transaction($connection); // Start the transaction, though rollback is inevitable
        sqlsrv_rollback($connection);
        echo json_encode(["success" => false, "message" => "Commit value not positive, changes were not applied"]);
        return;
    }

    $sql = "UPDATE Users SET firstName = ?, lastName = ?, username = ?, email = ?, address = ?, DOB = ?, RoleID = ? WHERE uniqueID = ?";
    $params = [
        $input['firstName'],
        $input['lastName'],
        $input['username'],
        $input['email'],
        $input['address'],
        $input['DOB'],
        $input['RoleID'],
        $input['uniqueID']
    ];

    if (!empty($input['Password'])) {
        $sql = "UPDATE Users SET firstName = ?, lastName = ?, username = ?, email = ?, address = ?, DOB = ?, RoleID = ?, Password = ? WHERE uniqueID = ?";
        $params = [
            $input['firstName'],
            $input['lastName'],
            $input['username'],
            $input['email'],
            $input['address'],
            $input['DOB'],
            $input['RoleID'],
            password_hash($input['Password'], PASSWORD_DEFAULT),
            $input['uniqueID']
        ];
    }

    sqlsrv_begin_transaction($connection); // Start a transaction
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        sqlsrv_rollback($connection); // Rollback on failure
        echo json_encode(["success" => false, "message" => "Failed to update user", "details" => sqlsrv_errors()]);
        return;
    }

    sqlsrv_commit($connection); 
    echo json_encode(["success" => true, "message" => "User updated successfully"]);
}

function deleteUser($connection) {
    header('Content-Type: application/json');
    parse_str(file_get_contents('php://input'), $input);

    if (!isset($input['uniqueID'])) {
        http_response_code(400); 
        echo json_encode(["error" => "Missing uniqueID"]);
        return;
    }

    $sql = "DELETE FROM Users WHERE uniqueID = ?";
    $params = [$input['uniqueID']];

    sqlsrv_begin_transaction($connection); 
    $stmt = sqlsrv_query($connection, $sql, $params);

    if ($stmt === false) {
        sqlsrv_rollback($connection); 
        echo json_encode(["error" => "Failed to delete user", "details" => sqlsrv_errors()]);
        return;
    }

    sqlsrv_commit($connection); 
    echo json_encode(["success" => "User deleted successfully"]);
}
?>