
<?php
// Decode the incoming JSON payload
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';
$rememberMe = $data['rememberMe'] ?? false;

if ($username === 'admin' && $password === 'password123') {
    echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
}

?>