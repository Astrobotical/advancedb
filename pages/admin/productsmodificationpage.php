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
$uploadDir = '../../productsImage';
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
        $imageName = pathinfo($_FILES['productImage']['name'], PATHINFO_FILENAME);
        $imageExtension = strtolower(pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION));

        // Generate a unique file name to avoid collisions
        $uploadedImageName = $imageName . '.' . $imageExtension;  // Keep the original extension
        $uploadDir = '../../productsImage';  // Directory to save the images
        $uploadedImagePath = $uploadDir . '/' . $uploadedImageName;
        $uploadedImageURL = '/productsImage/' . $uploadedImageName;

        // Move the uploaded file to the 'productsimages' folder
        if (!move_uploaded_file($imageTmpName, $uploadedImagePath)) {
            die("Failed to upload image.");
        }
    }

    // Insert product details into the database
    $sql = "INSERT INTO Products (productName, productCost, productQuantity, CategoryID, productImage)
            VALUES (?, ?, ?, ?, ?)";
    $params = [$productName, $productCost, $productQuantity, $categoryID, $uploadedImageURL];
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>   
</head>
<body>
<?php include '../../components/navbar.php'; ?>
<div class="container mx-auto mt-10 p-4">
    <div class="flex">
    <h1 class="text-2xl font-bold mb-6">Manage Products & Categories</h1>
    <button type="button" class="px-4 ml-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
      onclick="toggleModal(true)"> Bulk Insert Products </button>
</div>
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
    <!-- Modal -->
  <div 
    id="bulkInsertModal" 
    class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white w-full max-w-4xl p-6 rounded-lg shadow-lg">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Bulk Insert Products</h2>
        <button class="text-gray-500 hover:text-gray-700" onclick="toggleModal(false)">✖</button>
      </div>
      <form id="bulkInsertForm">
        <table class="w-full border-collapse border border-gray-300 text-left text-primaryTextColor">
          <thead>
            <tr>
            <th class="border border-gray-300 px-4 py-2">#</th>
              <th class="border border-gray-300 px-4 py-2">Product Name</th>
              <th class="border border-gray-300 px-4 py-2">Product Cost</th>
              <th class="border border-gray-300 px-4 py-2">Product Image</th>
              <th class="border border-gray-300 px-4 py-2">Category</th>
              <th class="border border-gray-300 px-4 py-2">Product Quantity</th>
              <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody id="productRows">
            <!-- Rows dynamically added here -->
          </tbody>
        </table>
        <div class="mt-4 flex justify-end space-x-2">
          <button type="button" class="px-4 py-2 bg-green-500 text-white rounded" onclick="addRow()">Add Row</button>
          <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded" onclick="exportToCSV()">Export to CSV</button>
          <button type="button" class="px-4 py-2 bg-indigo-500 text-white rounded" onclick="bulkInsert()">Bulk Insert</button>
        </div>
      </form>
    </div>
  </div>

</div>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
        await fetchCategories();
    });
    let rowCount = 0; 
    let categories = []; // Categories fetched from the database

// Fetch categories from the database when the page loads
async function fetchCategories() {
  try {
    const response = await fetch('../../data/processes/processExportCSV.php');
    const result = await response.json();
    if (result.success) {
      categories = result.categories;
    } else {
      alert('Failed to fetch categories: ' + result.error);
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
    alert('An error occurred while fetching categories.');
  }
}

// Populate category dropdowns
function populateCategoryDropdown(selectElement) {
  selectElement.innerHTML = categories
    .map(
      (category) => `<option value="${category.id}">${category.name}</option>`
    )
    .join('');
}
    // Toggle Modal
    function toggleModal(show) {
      const modal = document.getElementById('bulkInsertModal');
      modal.classList.toggle('hidden', !show);
    }

    function addRow() {
    rowCount++;
    const tbody = document.getElementById('productRows');
    const newRow = document.createElement('tr'); // Correctly creates a table row element.
    newRow.innerHTML = `
    <td class="border border-gray-300 px-4 py-2">${rowCount}</td>
    <td class="border border-gray-300 px-4 py-2"><input type="text" name="productName[]" class="w-full"></td>
    <td class="border border-gray-300 px-4 py-2"><input type="number" step="0.01" name="productCost[]" class="w-full"></td>
    <td><input type="file" class="product-image-input border border-gray-300 rounded w-full" accept="image/*"></td>
    <td class="border border-gray-300 px-4 py-2">
        <select name="categoryID[]" class="w-full category-dropdown"></select>
    </td>
    <td class="border border-gray-300 px-4 py-2"><input type="number" name="productQuantity[]" class="w-full"></td>
    <td class="border border-gray-300 px-4 py-2"><button type="button" onclick="removeRow(this)" class="text-red-500">Remove</button></td>
    `;
  
    tbody.appendChild(newRow);

    // Select the dropdown and populate it.
    const categoryDropdown = newRow.querySelector('.category-dropdown');
    if (categoryDropdown) {
        populateCategoryDropdown(categoryDropdown);
    } else {
        console.error('Category dropdown not found in the new row.');
    }
}

function removeRow(button) {
    const row = button.parentElement.parentElement;
    const index = Array.from(row.parentNode.children).indexOf(row);
    row.remove();
    updateRowNumbers();
}

// Update row numbers after removal
function updateRowNumbers() {
    const rows = document.querySelectorAll('#productRows tr');
    rowCount = 0; 
    rows.forEach((row, index) => {
        rowCount++;
        row.firstElementChild.textContent = rowCount;
    });
}

// Export data to CSV
async function exportToCSV() {
    const rows = document.querySelectorAll('#productRows tr');
    const csvData = [['#', 'Product Name', 'Product Cost', 'Product Image', 'Category ID', 'Product Quantity']];
    rows.forEach((row, index) => {
        const inputs = row.querySelectorAll('input');
        const imageInput = row.querySelector('input[type="file"]');
        const productImagePath = imageInput ? imageInput.files[0]?.name : ''; // Get the image file name
        const data = [
            index + 1,
            inputs[0].value,
            inputs[1].value,
            productImagePath, // Add the image file name
            inputs[3].value,
            inputs[4].value
        ];
        csvData.push(data);
    });

    const response = await fetch('../../data/processes/processExportCSV.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(csvData)
    });

    const result = await response.json();
    if (result.success) {
        alert('CSV file created successfully in "CSV" folder.');
    } else {
        alert('Failed to create CSV file: ' + result.error);
    }
}

// Bulk Insert
async function bulkInsert() {
    const rows = document.querySelectorAll('#productRows tr');
    const products = Array.from(rows).map(row => {
        const inputs = row.querySelectorAll('input');
        const imageInput = row.querySelector('input[type="file"]');
        const productImage = imageInput ? imageInput.files[0]?.name : ''; // Get image file name

        return {
            productName: inputs[0].value,
            productCost: parseFloat(inputs[1].value),
            productImage: productImage, // Include the image file name
            categoryID: parseInt(inputs[3].value),
            productQuantity: parseInt(inputs[4].value)
        };
    });

    try {
        const response = await fetch('../../data/processes/bulkInsertProducts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(products)
        });
        const result = await response.json();
        if (result.success) {
            alert('Products inserted successfully!');
            toggleModal(false); // Close modal after successful insertion
        } else {
            alert('Failed to insert products: ' + result.error);
        }
    } catch (error) {
        console.error('Error during bulk insert:', error);
        alert('An error occurred during bulk insert.');
    }
    }
  </script>
    <script src="/../../assets/js/scripts.js"></script>
</body>
</html>