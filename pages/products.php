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
try{
// Establish the connection
$conn = sqlsrv_connect($servername, $connection);

// Check the connection
if (!$conn) {
    echo "<script>alert('Failed to connect');<script>";
  //  die(print_r(sqlsrv_errors(), true));
}

// Default query to fetch all products
$sql = "SELECT uniqueID, productName, productCost, productQuantity, CategoryID FROM products";

// Check if a category ID has been passed via the search form
if (isset($_GET['categoryID']) && !empty($_GET['categoryID'])) {
    $categoryID = intval($_GET['categoryID']);  // Ensure the categoryID is an integer
    $sql .= " WHERE CategoryID = ?";
    $params = [$categoryID];
} else {
    $params = [];
}

// Prepare the statement
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
}catch(Exception $e){
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
<body>
<?php include __DIR__ . '../../components/navbar.php'; ?>
<header>
    <h1>Welcome to Our Ecommerce Store</h1>
</header>

<!-- Search Bar -->
<div class="search-container">
    <form method="GET" action="">
        <input type="text" name="categoryID" placeholder="Enter Category ID" value="<?php echo isset($_GET['categoryID']) ? htmlspecialchars($_GET['categoryID']) : ''; ?>">
        <button type="submit">Search</button>
    </form>
</div>

<div class="container">
    <?php
    if(!$conn){echo 'No Connection was Made';}else{
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Get the product name
        $productName = strtolower(htmlspecialchars($row['productName']));
        
        // Manually specify the image paths for each product
        if ($productName == "cap") {
            $imagePath = "images/cap.jfif";
        } elseif ($productName == "hoodie") {
            $imagePath = "images/hoodie.jfif";
        } elseif ($productName == "jacket") {
            $imagePath = "images/jacket.jfif";
        } elseif ($productName == "jeans") {
            $imagePath = "images/jeans.jfif";
        } elseif ($productName == "sneakers") {
            $imagePath = "images/sneakers.jfif";
        } elseif ($productName == "t-shirt") {
            $imagePath = "images/t-shirt.jfif";  // T-shirt image path
        } else {
            $imagePath = "images/default.jfif";  // Fallback image if product name doesn't match
        }

        // Display product card
        echo '<div class="product-card">';
        echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($row['productName']) . '">';
        echo '<div class="product-info">';
        echo '<h3>' . htmlspecialchars($row['productName']) . '</h3>';
        echo '<p class="price">$' . htmlspecialchars($row['productCost']) . '</p>';
        echo '<p>Quantity: ' . htmlspecialchars($row['productQuantity']) . '</p>';
        echo '<p>Category ID: ' . htmlspecialchars($row['CategoryID']) . '</p>';
        echo '<a href="#" class="buy-btn">Buy Now</a>';
        echo '</div>';
        echo '</div>
    }';
    }
}
    ?>
</div>

</body>
</html>

<?php
// Free resources and close connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>