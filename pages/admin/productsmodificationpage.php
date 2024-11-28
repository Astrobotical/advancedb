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
    "TrustServerCertificate" => true
];

// Establish the connection
$conn = sqlsrv_connect($servername, $connection);

// Check the connection
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Handling Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addProduct'])) {
    // Get form data for adding product
    $productName = $_POST['productName'];
    $productCost = $_POST['productCost'];
    $productQuantity = $_POST['productQuantity'];
    $categoryID = $_POST['categoryID'];
    $imageData = null; // Default value if no image is uploaded

    // Handle image upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $imageTmpName = $_FILES['productImage']['tmp_name'];
        $imageData = file_get_contents($imageTmpName); // Read the image into binary data
    }

    // Insert product into the database
    if ($imageData) {
        $sql = "INSERT INTO products (productName, productCost, productQuantity, CategoryID, productImage) 
                VALUES (?, ?, ?, ?, CONVERT(VARBINARY(MAX), ?))";
        $params = [$productName, $productCost, $productQuantity, $categoryID, $imageData];
    } else {
        $sql = "INSERT INTO products (productName, productCost, productQuantity, CategoryID) 
                VALUES (?, ?, ?, ?)";
        $params = [$productName, $productCost, $productQuantity, $categoryID];
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product added successfully!";
}

// Handling Modify Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifyProduct'])) {
    $productID = $_POST['productID'];
    $productName = $_POST['productName'];
    $productCost = $_POST['productCost'];
    $productQuantity = $_POST['productQuantity'];
    $categoryID = $_POST['categoryID'];
    $imageData = null; // Default value if no image is uploaded

    // Handle image upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $imageTmpName = $_FILES['productImage']['tmp_name'];
        $imageData = file_get_contents($imageTmpName); // Read the image into binary data
    }

    // Update product in the database
    if ($imageData) {
        $sql = "UPDATE products SET productName = ?, productCost = ?, productQuantity = ?, CategoryID = ?, productImage = ? 
                WHERE uniqueID = ?";
        $params = [$productName, $productCost, $productQuantity, $categoryID, $imageData, $productID];
    } else {
        $sql = "UPDATE products SET productName = ?, productCost = ?, productQuantity = ?, CategoryID = ? 
                WHERE uniqueID = ?";
        $params = [$productName, $productCost, $productQuantity, $categoryID, $productID];
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product updated successfully!";
}

// Handling Delete Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteProduct'])) {
    $productID = $_POST['productID'];

    // Delete product from the database
    $sql = "DELETE FROM products WHERE uniqueID = ?";
    $params = [$productID];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product deleted successfully!";
}

// Fetch all products for displaying
$sql = "SELECT uniqueID, productName, productCost, productQuantity, CategoryID FROM products";
$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Website - Manage Products</title>
    <?php include("links.php"); ?>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        header { background: #333; color: #fff; padding: 20px; text-align: center; margin-top:5%; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; padding: 20px; }
        .product-card { background: #fff; border: 1px solid #ddd; border-radius: 10px; width: 250px; margin: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease-in-out; }
        .product-card:hover { transform: translateY(-10px); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 10px 10px 0 0; }
        .product-info { padding: 15px; text-align: center; }
        .product-info h3 { margin: 0; font-size: 18px; color: #333; }
        .product-info p { margin: 5px 0; font-size: 14px; color: #555; }
        .product-info .price { font-size: 16px; font-weight: bold; color: #e91e63; }
        .product-info .btn { display: inline-block; margin-top: 10px; padding: 10px 15px; background: #e91e63; color: #fff; text-decoration: none; border-radius: 5px; }
        .product-info .btn:hover { background: #c2185b; }
        form { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); margin-top: 30px; }
        form input, form select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        form button { padding: 10px 15px; background: #333; color: #fff; border: none; border-radius: 5px; }
    </style>
</head>
<body>
<?php include 'navbar.php';?>
<header>
    <h1>Manage Products</h1>
</header>

<div class="container">
    <!-- Add Product Form -->
    <h2>Add Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="productName" placeholder="Product Name" required><br>
        <input type="number" step="0.01" name="productCost" placeholder="Product Cost" required><br>
        <input type="number" name="productQuantity" placeholder="Product Quantity" required><br>
        <input type="text" name="categoryID" placeholder="Category ID" required><br>
        <input type="file" name="productImage" accept="image/*"><br>
        <button type="submit" name="addProduct">Add Product</button>
    </form>

    <!-- Modify Product Form -->
    <h2>Modify Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <select name="productID" required>
            <option value="">Select Product</option>
            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= $row['uniqueID'] ?>"><?= $row['productName'] ?></option>
            <?php endwhile; ?>
        </select><br>
        <input type="text" name="productName" placeholder="Product Name" required><br>
        <input type="number" step="0.01" name="productCost" placeholder="Product Cost" required><br>
        <input type="number" name="productQuantity" placeholder="Product Quantity" required><br>
        <input type="text" name="categoryID" placeholder="Category ID" required><br>
        <input type="file" name="productImage" accept="image/*"><br>
        <button type="submit" name="modifyProduct">Modify Product</button>
    </form>

    

</body>
</html>

<?php
// Free resources and close connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>

<?php
// Server and database configuration
$servername = "DESKTOP-IIQI6MO\\SQLEXPRESS";
$database = "EcommerceDB";

// Windows Authentication
$uid = ""; 
$pass = ""; 

// Connection options
$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
    "TrustServerCertificate" => true
];

// Establish the connection
$conn = sqlsrv_connect($servername, $connection);

// Check the connection
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Handle Delete Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteProduct'])) {
    $productID = $_POST['productID'];

    // Delete product from the database
    $sqlDelete = "DELETE FROM products WHERE uniqueID = ?";
    $params = [$productID];
    $stmtDelete = sqlsrv_query($conn, $sqlDelete, $params);

    if ($stmtDelete === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product deleted successfully!";
}

// Fetch products for the delete form dropdown
$sql = "SELECT uniqueID, productName FROM products";
$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Website - Manage Products</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { display: flex; flex-direction: column; align-items: center; padding: 20px; }
        form { margin-top: 20px; background: #fff; padding: 20px; border-radius: 10px; width: 300px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, button { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background-color: #333; color: #fff; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <!-- Delete Product Form -->
    <form action="" method="POST">
        <label for="productID">Select Product</label>
        <select id="productID" name="productID" required>
            <option value="">Select Product</option>
            <?php 
            // Display products in dropdown
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
                <option value="<?= $row['uniqueID'] ?>"><?= $row['productName'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="deleteProduct">Delete Product</button>
    </form>
</div>

</body>
</html>

<?php
// Free resources and close connection
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
<nav class="navbar">
<link rel="stylesheet" type="text/css" href="navbar.css" />
    <a href="index.php" class="nav-logo">Ecommerce Store</a>
    <ul class="nav-links">
        <li><a href="about.php">About Us</a></li>
        <li><a href="catalog.php">Products</a>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    </ul>
</nav>