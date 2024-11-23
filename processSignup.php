<?php
require('./data/connection.php');
$connectionString = new ConnectionString();


if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $sql = "SELECT COUNT(*) FROM Users WHERE username = ?";
    $searchTerm = "%" . $username . "%";  
    $params = array($searchTerm);
    
    $stmt = sqlsrv_query($connectionString->connection, $sql, $params);

    if ($stmt) {
        // Fetch the result
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $count = $row[''] ? $row[0] : 0;
        
        if ($count > 0) {
            echo "No";
        } else {
            echo "Yes";
        }
    } else {
        echo "Error in query execution.";
    }

    sqlsrv_close($connectionString->connection);
    }

    if (isset($_GET['email'])) {
        $username = $_GET['email'];
    
        $sql = "SELECT COUNT(*) FROM Users WHERE email = ?";
        $searchTerm = "%" . $username . "%";  
        $params = array($searchTerm);
        
        $stmt = sqlsrv_query($connectionString->connection, $sql, $params);
    
        if ($stmt) {
            // Fetch the result
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $count = $row[''] ? $row[0] : 0;
            
            if ($count > 0) {
                echo "No";
            } else {
                echo "Yes";
            }
        } else {
            echo "Error in query execution.";
        }
    
        sqlsrv_close($connectionString->connection);
        }
    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $requestBody = json_decode(file_get_contents('php://input'), true);

    if (isset($requestBody['password'])) {
        $firstName = trim($requestBody['firstName']);
        $lastName = trim($requestBody['lastName']);
        $email = trim($requestBody['email']);
        $address = trim($requestBody['address']);
        $dob = trim($requestBody['DOB']);
        $password = trim($requestBody['password']);
        $username = trim($requestBody['username']);
        
        $roleID = 2;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        sqlsrv_begin_transaction($connectionString->connection);

        try {
          $query = "INSERT INTO Users (username, firstName, lastName, address, DOB, RoleID, Password, email) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
          $params = [$username, $firstName, $lastName, $address, $dob, $roleID, $hashedPassword, $email];

            $stmt = sqlsrv_query($connectionString->connection, $query, $params);


            sqlsrv_commit($connectionString->connection);
            sqlsrv_free_stmt($stmt);
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "User registered successfully."]);
        } catch (Exception $e) {
            sqlsrv_rollback($connectionString->connection);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } finally {
            sqlsrv_close($connectionString->connection);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid payload."]);
    }
}
