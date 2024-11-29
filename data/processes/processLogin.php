
<?php
require('/../connection.php');
$connectionString = new ConnectionString();

try {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        throw new Exception("Username and password are required.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    $rememberMe = isset($_POST['rememberMe']) ? true : false;

    sqlsrv_begin_transaction($connectionString->connection);

    $query = "SELECT * FROM Users WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_query($connectionString->connection, $query, $params);

    if ($stmt === false) {
        throw new Exception("Error fetching user data.");
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if (!$user) {
        sqlsrv_rollback($connectionString->connection); 
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "User does not exists "]);
        exit;
    }

    if (!password_verify($password, $user['Password'])) {
        sqlsrv_rollback($connectionString->connection); 
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Password is invalid."]);
        exit;
    }

    sqlsrv_commit($connectionString->connection);

    $_SESSION['username'] = $user['username'];

    if ($rememberMe) {
        setcookie('username', $user['username'], time() + (30 * 24 * 60 * 60), '/');
        setcookie('rememberMe', true, time() + (30 * 24 * 60 * 60), '/');
    }

    http_response_code(200); 

    echo json_encode([
        "status" => "success",
        "message" => "Login successful.",
        "user" => [
            "username" => $user['username']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(401);
    sqlsrv_rollback($connectionString->connection); 
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
} finally {
    sqlsrv_close($connectionString->connection); 
}
?>