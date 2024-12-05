<?php
session_start(); // Start the session

if (!isset($_SESSION['userID'])) {
    // Respond with an unauthorized message
    echo "<script>alert('Unauthorized access Please Login. Redirecting to the homepage.');</script>";
    
    // Redirect to the index page
    echo "<script>window.location.href = '/index.php';</script>";
    exit(); // Stop further script execution
}else if(isset($_SESSION['userID'])&& !$_SESSION['userID']== 'Admin'){
  echo "<script>alert('Unauthorized access');</script>";
}
// Server and database configuration
$servername = "BURKE\\SQLEXPRESS";
$database = "EcommerceDB";
$connectionOptions = [
    "Database" => $database,
    "Uid" => "",
    "PWD" => "",
];

// Establish the connection
$conn = sqlsrv_connect($servername, $connectionOptions);

// Check connection
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}


$logsPerPage = 10; 
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $logsPerPage;


$countQuery = "SELECT COUNT(*) AS totalLogs FROM Logs";
$countStmt = sqlsrv_query($conn, $countQuery);
$totalLogs = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC)['totalLogs'];
$totalPages = ceil($totalLogs / $logsPerPage);

// Fetch logs for the current page
$query = "SELECT logID, tableName, logTime, logMessage 
          FROM Logs 
          ORDER BY logTime DESC 
          OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params = [$offset, $logsPerPage];
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
$logs = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $logs[] = $row;
}
sqlsrv_free_stmt($stmt);
sqlsrv_free_stmt($countStmt);
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link href="../../public/css/tailwind.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>         
</head>
<body class="text-bgColor min-h-screen">
    <?php include '../../components/navbar.php';?>
    <div class="container text-bgColor max-w-screen-lg mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-6 text-primaryTextColor">System Logs</h1>
        <div class="bg-base-100 shadow rounded-lg overflow-hidden">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-bgColor">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primaryTextColor uppercase tracking-wider">Log ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primaryTextColor uppercase tracking-wider">Table Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primaryTextColor uppercase tracking-wider">Log Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-primaryTextColor uppercase tracking-wider">Message</th>
                    </tr>
                </thead>
                <tbody class="bg-base-100 divide-y divide-gray-200">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-primaryTextColor"><?php echo htmlspecialchars($log['logID']); ?></td>
                                <td class="px-6 py-4 text-sm text-primaryTextColor"><?php echo htmlspecialchars($log['tableName']); ?></td>
                                <td class="px-6 py-4 text-sm text-primaryTextColor"><?php echo htmlspecialchars($log['logTime']->format('Y-m-d H:i:s')); ?></td>
                                <td class="px-6 py-4 text-sm text-primaryTextColor"><?php echo htmlspecialchars($log['logMessage']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm btn btn-primary text-primaryTextColor">No logs available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="flex justify-center mt-6">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>" class="px-4 py-2 btn btn-primary text-primaryTextColor rounded-lg mx-1">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="px-4 py-2 <?php echo $i === $currentPage ? 'bg-blue-500 text-primaryTextColor' : 'bg-gray-300 text-primaryTextColor'; ?> rounded-lg mx-1">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>" class="px-4 py-2 bg-gray-300 text-primaryTextColor rounded-lg mx-1">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="/../../assets/js/scripts.js"></script>
</body>
</html>