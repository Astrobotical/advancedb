<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Server and database configuration
$servername = "BURKE\\SQLEXPRESS";
$database = "EcommerceDB";

// Windows Authentication
$uid = "";
$pass = "";

// Connection options
$connectionOptions = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
];

// Establish the connection
$conn = sqlsrv_connect($servername, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Ensure the 'productsimages' folder exists
$uploadDir = 'productsimages';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);  // Create the directory if it doesn't exist
}

// Handle Add Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addCategory'])) {
    $categoryType = $_POST['categoryType'];

    $sql = "INSERT INTO Categories (type) VALUES (?)";
    $params = [$categoryType];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Category added successfully!";
}

// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addProduct'])) {
    $productName = $_POST['productName'];
    $productCost = $_POST['productCost'];
    $productQuantity = $_POST['productQuantity'];
    $categoryID = $_POST['categoryID'];

    // Handle image upload
    $productImagePath = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $imageTmpName = $_FILES['productImage']['tmp_name'];
        $imageName = basename($_FILES['productImage']['name']);
        $productImagePath = $uploadDir . '/' . $imageName;  // Save path relative to 'productsimages' folder

        // Move the uploaded image to the 'productsimages' folder
        if (!move_uploaded_file($imageTmpName, $productImagePath)) {
            die("Failed to upload image.");
        }
    }

    $sql = "INSERT INTO Products (productName, productCost, productQuantity, CategoryID, productImage)
            VALUES (?, ?, ?, ?, ?)";
    $params = [$productName, $productCost, $productQuantity, $categoryID, $productImagePath];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product added successfully!";
}

// Handle Update Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateProduct'])) {
    $productID = $_POST['productID'];
    $productName = $_POST['productName'];
    $productCost = $_POST['productCost'];
    $productQuantity = $_POST['productQuantity'];
    $categoryID = $_POST['categoryID'];

    // Handle image upload
    $productImagePath = $_POST['existingImagePath'];  // Retain the existing image path if no new image is uploaded
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $imageTmpName = $_FILES['productImage']['tmp_name'];
        $imageName = basename($_FILES['productImage']['name']);
        $productImagePath = $uploadDir . '/' . $imageName;  // Save path relative to 'productsimages' folder

        // Move the uploaded image to the 'productsimages' folder
        if (!move_uploaded_file($imageTmpName, $productImagePath)) {
            die("Failed to upload image.");
        }
    }

    $sql = "UPDATE Products 
            SET productName = ?, productCost = ?, productQuantity = ?, CategoryID = ?, productImage = ?
            WHERE uniqueID = ?";
    $params = [$productName, $productCost, $productQuantity, $categoryID, $productImagePath, $productID];
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product updated successfully!";
}

// Handle Delete Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteProduct'])) {
    $productID = $_POST['productID'];

    // Fetch the image path to delete it from the server
    $sql = "SELECT productImage FROM Products WHERE uniqueID = ?";
    $params = [$productID];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $imagePath = $product['productImage'];

    // Delete the image from the server
    if (file_exists($imagePath)) {
        unlink($imagePath);  // Delete the image file
    }

    // Delete the product from the database
    $sql = "DELETE FROM Products WHERE uniqueID = ?";
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Product deleted successfully!";
}

// Fetch Categories for Dropdown
$categoriesQuery = "SELECT uniqueID, type FROM Categories";
$categoriesStmt = sqlsrv_query($conn, $categoriesQuery);
$categories = [];
while ($row = sqlsrv_fetch_array($categoriesStmt, SQLSRV_FETCH_ASSOC)) {
    $categories[] = $row;
}

// Fetch Products for Dropdown and Display
$productsQuery = "SELECT uniqueID, productName FROM Products";
$productsStmt = sqlsrv_query($conn, $productsQuery);
$products = [];
while ($row = sqlsrv_fetch_array($productsStmt, SQLSRV_FETCH_ASSOC)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce - Manage Products & Categories</title>
    <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body>
<?php include '../../components/navbar.php'; ?>
<div class="container mx-auto mt-10 p-4">
    <h1 class="text-2xl font-bold mb-6">Manage Products & Categories</h1>

    <!-- Add Category Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Add Category</h2>
        <form action="" method="POST">
            <input type="text" name="categoryType" placeholder="Category Name" required class="border p-2 w-full mb-4">
            <button type="submit" name="addCategory" class="bg-blue-500 text-white px-4 py-2 rounded">Add Category</button>
        </form>
    </div>

    <!-- Add Product Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Add Product</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="productName" placeholder="Product Name" required class="border p-2 w-full mb-4">
            <input type="number" step="0.01" name="productCost" placeholder="Product Cost" required class="border p-2 w-full mb-4">
            <input type="number" name="productQuantity" placeholder="Product Quantity" required class="border p-2 w-full mb-4">
            <select name="categoryID" required class="border p-2 w-full mb-4">
                <option value="">Select Category</option>
                <?php 
                $categoryStmt = sqlsrv_query($conn, "SELECT uniqueID, type FROM Categories");
                while ($row = sqlsrv_fetch_array($categoryStmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['uniqueID']}'>{$row['type']}</option>";
                }
                ?>
            </select>
            <input type="file" name="productImage" accept="image/*" class="border p-2 w-full mb-4">
            <button type="submit" name="addProduct" class="bg-green-500 text-white px-4 py-2 rounded">Add Product</button>
        </form>
    </div>

    <!-- Update Product Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Update Product</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <select name="productID" required class="border p-2 w-full mb-4">
                <option value="">Select Product</option>
                <?php 
                $productStmt = sqlsrv_query($conn, "SELECT uniqueID, productName FROM Products");
                while ($row = sqlsrv_fetch_array($productStmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['uniqueID']}'>{$row['productName']}</option>";
                }
                ?>
            </select>
            <input type="text" name="productName" placeholder="Product Name" required class="border p-2 w-full mb-4">
            <input type="number" step="0.01" name="productCost" placeholder="Product Cost" required class="border p-2 w-full mb-4">
            <input type="number" name="productQuantity" placeholder="Product Quantity" required class="border p-2 w-full mb-4">
            <select name="categoryID" required class="border p-2 w-full mb-4">
                <option value="">Select Category</option>
                <?php 
                $categoryStmt = sqlsrv_query($conn, "SELECT uniqueID, type FROM Categories");
                while ($row = sqlsrv_fetch_array($categoryStmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['uniqueID']}'>{$row['type']}</option>";
                }
                ?>
            </select>
            <input type="file" name="productImage" accept="image/*" class="border p-2 w-full mb-4">
            <button type="submit" name="updateProduct" class="bg-yellow-500 text-white px-4 py-2 rounded">Update Product</button>
        </form>
    </div>

    <!-- Delete Product Section -->
    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Delete Product</h2>
        <form action="" method="POST">
            <select name="productID" required class="border p-2 w-full mb-4">
                <option value="">Select Product</option>
                <?php 
                $productStmt = sqlsrv_query($conn, "SELECT uniqueID, productName FROM Products");
                while ($row = sqlsrv_fetch_array($productStmt, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='{$row['uniqueID']}'>{$row['productName']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="deleteProduct" class="bg-red-500 text-white px-4 py-2 rounded">Delete Product</button>
        </form>
    </div>
</div>
</body>
</html>