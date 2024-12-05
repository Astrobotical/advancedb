<?php
require('../connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile'];
    
    // Check for file upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'File upload failed.']);
        exit;
    }

    $fileName = $file['name'];
    $uploadDir = __DIR__ . '/../../uploadedCSV/';
    $errorDir  = __DIR__ . '/../../uploadedCSV/errors/';
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filePath = $uploadDir . basename($fileName); // Use correct path
    $sqlFilePath = str_replace('/', '\\', $filePath);

    // Move uploaded file to destination
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded file.']);
        exit;
    }

    // Correct BULK INSERT syntax
    $sql = "
    BULK INSERT Products
    FROM '$sqlFilePath'
    WITH (
        FIELDTERMINATOR = ',', 
        ROWTERMINATOR = '\n', 
        FIRSTROW = 2
    );
    ";

    try {
        sqlsrv_begin_transaction($connection);
        $stmt = sqlsrv_query($connection, $sql);

        if (!$stmt) {
            throw new Exception("BULK INSERT failed: " . print_r(sqlsrv_errors(), true));
        }
        sqlsrv_commit( $connection);
        echo json_encode(['success' => true, 'message' => 'Products successfully inserted using BULK INSERT.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if ($connection) {
            sqlsrv_close($connection);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
}
?>