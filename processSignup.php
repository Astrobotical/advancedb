<?php
   $host = 'localhost'; 
   $db = 'your_database';
   $user = 'your_user'; 
   $pass = 'your_password'; 
if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $conn = new mysqli($host, $user, $pass, $db);

    // Check for connection errors
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    // Return JSON response
    echo json_encode(['exists' => $count > 0]);
    exit;
} else {
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}