<?php
// Server and database configuration
$servername = "BURKE\SQLEXPRESS";
$database = "EcommerceDB";

// Windows Authentication
$uid = ""; 
$pass = ""; 

// Connection options
$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
];

$conn = sqlsrv_connect($servername, $connection);

if (!$conn) {
    die("<script>alert('Failed to connect');</script>");
}

// Fetch categories for the sidebar
$categoriesQuery = "SELECT uniqueID, type FROM Categories";
$categoriesStmt = sqlsrv_query($conn, $categoriesQuery);

if ($categoriesStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Default query to fetch all products
$sql = "SELECT uniqueID, productName, productImage, productCost, productQuantity, CategoryID FROM Products";

$params = [];
if (isset($_GET['categoryID']) && !empty($_GET['categoryID'])) {
    $categoryID = intval($_GET['categoryID']);
    $sql .= " WHERE CategoryID = ?";
    $params = [$categoryID];
}

// Fetch products
$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Store</title>
    <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<?php include __DIR__ . '../../components/navbar.php'; ?>

<div class="flex">
    <!-- Sidebar -->
    <aside class="w-1/4 bg-white p-4 shadow-md">
        <h2 class="text-lg font-semibold mb-4">Filter by Category</h2>
        <form method="GET" action="">
            <ul>
                <?php while ($cat = sqlsrv_fetch_array($categoriesStmt, SQLSRV_FETCH_ASSOC)): ?>
                    <li>
                        <button type="submit" name="categoryID" value="<?= $cat['uniqueID'] ?>" 
                            class="block py-2 px-4 bg-gray-100 hover:bg-gray-200 rounded-md text-left w-full">
                            <?= htmlspecialchars($cat['type']) ?>
                        </button>
                    </li>
                <?php endwhile; ?>
            </ul>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <img src="<?php echo $product['productImage']; ?>" 
                        alt="<?= htmlspecialchars($product['productName']) ?>" 
                        class="w-full h-48 object-cover">
                    <div class="p-4">   
                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($product['productName']) ?></h3>
                        <p class="text-gray-600">Price: $<?= number_format($product['productCost'], 2) ?></p>
                        <p class="text-gray-600">Quantity: <?= $product['productQuantity'] ?></p>
                        <form method="POST" action="addToCart.php" class="mt-4">
                            <input type="hidden" name="productID" value="<?= $product['uniqueID'] ?>">
                            <button type="submit" 
                                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

</body>
</html>

<?php
// Free resources and close connection
sqlsrv_free_stmt($categoriesStmt);
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>