<?php
session_start();
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
try {
    // Establish the connection
    $conn = sqlsrv_connect($servername, $connection);

    // Check the connection
    if (!$conn) {
        echo "<script>alert('Failed to connect');</script>";
    }

    // Default query to fetch all products with category type
    $sql = "
        SELECT p.uniqueID, p.productName, p.productCost, p.productImage, p.productQuantity, p.CategoryID, c.type AS CategoryType
        FROM products p
        JOIN Categories c ON p.CategoryID = c.uniqueID
    ";

    $isSearching =  false;
    // Check if a category type has been passed via the search form
    if (isset($_GET['categoryType']) && !empty($_GET['categoryType'])) {
        $isSearching = $_GET['categoryType'] == ''? false : true;
        $categoryType = $_GET['categoryType'];  // Get the category type from the form
        $sql .= " WHERE c.type LIKE ?";
        $params = ['%' . $categoryType . '%'];  // Use LIKE for partial matching
    } else {
        $params = [];
    }

    // Prepare the statement
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Cart Handler
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $productID = intval($_POST['productID']);
        $quantity = intval($_POST['Qty']);
        $customerID =  $_SESSION['userID'];
        echo '<script type="text/javascript">alert("Data has been submitted to '  . '");</script>';
        echo "<script>alert('Item Added');</script>";

        // Check if product is already in cart
        $checkCartSQL = "SELECT * FROM Cart WHERE productID = ? AND customerID = ?";
        $checkCartStmt = sqlsrv_query($conn, $checkCartSQL, [$productID, $customerID]);

        if ($checkCartStmt && sqlsrv_has_rows($checkCartStmt)) {
            echo "<script>console.log('Item Updated'); alert('Item Updated');</script>";
            echo "<script>alert('Item Updated');//Swal.fire({title: 'Item was added to the cart',icon: 'success',timer: 1500,showConfirmButton: false});</script>";
            // Update existing cart item
            $updateCartSQL = "UPDATE Cart SET quantity = quantity + ? WHERE productID = ? AND customerID = ?";
            sqlsrv_query($conn, $updateCartSQL, [$quantity, $productID, $customerID]);
        } else {
            // Add new item to cart
            $insertCartSQL = "INSERT INTO Cart (customerID, productID, quantity) VALUES (?, ?, ?)";
            $result =  sqlsrv_query($conn, $insertCartSQL, [$customerID, $productID, $quantity]);
            echo "<script>alert('Item Added');//Swal.fire({title: 'Item was added to the cart',icon: 'success',timer: 1500,showConfirmButton: false});</script>";
        }
    }
    $xmlQuery = " SELECT 
        P.uniqueID AS ProductID,
        P.productName AS ProductName,
        P.productCost AS ProductCost,
        P.productImage AS ProductImage,
        P.productQuantity AS ProductQuantity,
        C.type AS Category FROM 
        Products P INNER JOIN Categories C ON P.CategoryID = C.uniqueID
        FOR XML PATH('Product'), ROOT('Products');";

    $result = sqlsrv_query($conn, $xmlQuery);
    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

    $xmlData = null;
    foreach ($row as $key => $value) {
        
        if (strpos($key, 'XML') !== false) {
            $xmlData = $value;
       
            break;
        }
    }

    if (!$xmlData) {
        die("No XML data returned.");
    }
    $xml = simplexml_load_string($xmlData);

    if ($xml === false) {
        echo "Failed to parse XML.";
        print_r(libxml_get_errors());
        exit;
    }

    $products = [];
    foreach ($xml->Product as $product) {
        $products[] = [
            'uniqueID' => (string)$product->ProductID,
            'productName' => (string)$product->ProductName,
            'productCost' => (float)$product->ProductCost,
            'productImage' => (string)$product->ProductImage,
            'productQuantity' => (int)$product->ProductQuantity,
            'CategoryType' => (string)$product->Category,
        ];
    }
} catch (Exception $e) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Website</title>
    <link href="../../public/css/tailwind.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* General page layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 28px;
        }

        /* Search bar style */
        .search-container {
            text-align: center;
            margin: 20px;
        }

        .search-container input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #e91e63;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #c2185b;
        }

        /* Product container */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        /* Product card style */
        .product-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 250px;
            margin: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        /* Product image */
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }

        /* Product info section */
        .product-info {
            padding: 15px;
            text-align: center;
        }

        .product-info h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .product-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .product-info .price {
            font-size: 16px;
            font-weight: bold;
            color: #e91e63;
        }

        .buy-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #e91e63;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .buy-btn:hover {
            background: #c2185b;
        }
    </style>
</head>

<body class="bg-bgColor h-screen">
    <?php include __DIR__ . '../../components/navbar.php'; ?>
    <header>
        <h1>Welcome to Our Ecommerce Store</h1>
    </header>

    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET" action="">
            <input type="text" name="categoryType" placeholder="Enter Category Type" value="<?php echo isset($_GET['categoryType']) ? htmlspecialchars($_GET['categoryType']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        <?php
        if (!$conn) {
            echo 'No Connection was Made';
        } else {
            if(!$isSearching){
            foreach($products as $product) :?>
                <form method="POST" class="product-card">
                    <img src="<?php echo htmlspecialchars($product['productImage']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['productName']); ?></h3>
                        <p class="price">$<?php echo htmlspecialchars($product['productCost']); ?></p>
                        <p>In Stock: <?php echo htmlspecialchars($product['productQuantity']); ?></p>
                        <p>Category: <?php echo htmlspecialchars($product['CategoryType']); ?></p>
                        <input type="hidden" name="productID" value="<?php echo htmlspecialchars($product['uniqueID']); ?>">
                        <div class="flex justify-center items-center space-x-2">
                            <label for="Qty">Quantity:</label>
                            <input type="number" name="Qty" min="1" value="1" class="w-6" required>
                        </div>
                        <?php if(!isset($_SESSION['userID'])){?>
                            <a href="/pages/auth/login.php" class="btn  btn-disabled mt-1" style="color:black;">Please Login</a>
                        <?php } else {?>
                            <button type="submit" name="submit" class="buy-btn">Add to Cart</button>
                            <?php } ?>
                        
                    </div>
                </form>
                <?php endforeach;
            } else {
            
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        ?>
                <form method="POST" class="product-card">
                    <img src="<?php echo $row['productImage']; ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                    <div class="product-info">
                        <h3><?php echo $row['productName']; ?></h3>
                        <p class="price">$<?php echo $row['productCost']; ?></p>
                        <p>In Stock: <?php echo $row['productQuantity']; ?></p>
                        <p>Category: <?php echo $row['CategoryType']; ?></p>
                        <input type="hidden" name="productID" value="<?php echo $row['uniqueID']; ?>">
                        <div class="flex">
                            <label for="Qty">Quantity:</label>
                            <input type="number" name="Qty" min="1" value="1" class="w-6" required>
                        </div>
                        <button type="submit" name="submit" class="buy-btn">Add to Cart</button>
                    </div>
                </form>
        <?php
            }
            }
        
        }
        ?>
    </div>
    <script src="../../assets/js/scripts.js"></script>
</body>

</html>

<?php
// Free resources and close connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>