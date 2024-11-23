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
if(isset($_POST['password'])){
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $dob = trim($_POST['DOB']);
    $password = trim($_POST['password']);
    echo "Something here";
    // 1  for admin and 2 for customer
    $roleID = 1; 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    sqlsrv_begin_transaction($connectionString->connection);

    try {
        $query = "INSERT INTO Users (firstName, lastName, address, DOB, RoleID, Password, email) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$firstName, $lastName, $address, $dob, $roleID, $hashedPassword, $email];

        $stmt = sqlsrv_query($connectionString->connection, $query, $params);

        if ($stmt === false) {
            throw new Exception("Failed to insert user.");
        }

        sqlsrv_commit($connectionString->connection);
        sqlsrv_free_stmt($stmt);
        return ["status" => "success", "message" => "User registered successfully."];
    } catch (Exception $e) {
        sqlsrv_rollback($connectionString->connection);
        return ["status" => "error", "message" => $e->getMessage()];
    } finally {
        sqlsrv_close($connectionString->connection);
    }
}