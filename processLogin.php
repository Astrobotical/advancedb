
<?php
require('./data/connection.php');
$connectionString = new ConnectionString();

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$rememberMe = $data['rememberMe'] ?? false;

   // Begin transaction
   sqlsrv_begin_transaction($connectionString->connection);

   try {
       $query = "SELECT username, Password FROM Users WHERE username = ? OR email = ?";
       $params = [$username, $username];
       $stmt = sqlsrv_query($connectionString->connection, $query, $params);

       if ($stmt === false) {
           throw new Exception("Error fetching user data.");
       }

       $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

       if (!$user) {
           sqlsrv_rollback($connectionString->connection); 
           return ["status" => "error", "message" => "Invalid username or password."];
       }

       if (!password_verify($password, $user['Password'])) {
           sqlsrv_rollback($connectionString->connection); 
           return ["status" => "error", "message" => "Invalid username or password."];
       }
       sqlsrv_commit($connectionString->connection);
       http_response_code(200); 
       $_SESSION['username'] = $user['username'];
       return [
           "status" => "success",
           "message" => "Login successful.",
           "user" => [
               "username" => $user['username']
           ]
       ];
   } catch (Exception $e) {
    http_response_code(401);
       sqlsrv_rollback($connectionString->connection); 
       return ["status" => "error", "message" => $e->getMessage()];
   } finally {
       sqlsrv_close($connectionString->connection); 
   }
?>