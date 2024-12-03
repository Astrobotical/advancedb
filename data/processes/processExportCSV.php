<?php

require('../connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection;
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getCategories();
        break;
    case 'POST':
        makeCSV();
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Unsupported HTTP method"]);
}

function makeCSV()
{
    $data = json_decode(file_get_contents('php://input'), true);

    // Create "CSV" directory if it doesn't exist
    $csvFolder = __DIR__ . '/../../CSV';
    if (!file_exists($csvFolder)) {
        mkdir($csvFolder, 0777, true);
    }

    // Save the file
    $csvFile = $csvFolder . '/bulk_insert_products.csv';
    $file = fopen($csvFile, 'w');

    if ($file) {
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create CSV file.']);
    }
}
function getCategories()
{
    global $connection;

    // Query to fetch categories
    $query = "SELECT uniqueID AS id, type AS name FROM Categories";

    $stmt = sqlsrv_query($connection, $query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch categories.']);
        exit;
    }

    $categories = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $categories[] = $row;
    }

    echo json_encode(['success' => true, 'categories' => $categories]);
}
