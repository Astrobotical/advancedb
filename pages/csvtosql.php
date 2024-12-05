<?php
// Server and database configuration
$servername = "BURKE\SQLEXPRESS";
$database = "EcommerceDB";
$uid = "";
$pass = "";

// Connection options
$connection = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
];

// Establish the connection
$conn = sqlsrv_connect($servername, $connection);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Check if file was uploaded
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
        $fileTmpPath = $_FILES['csvFile']['tmp_name'];
        
        // Open the uploaded CSV file
        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            // Skip the header if it exists
            fgetcsv($handle); 

            // Prepare the SQL statement with placeholders
            $sql = "INSERT INTO Users (username, firstName, lastName, address, DOB, email, Password, RoleID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // Read each row from the CSV
            while (($row = fgetcsv($handle)) !== FALSE) {
                // Ensure there are exactly 8 values in the row
                if (count($row) == 8) {
                    // Check if the email already exists in the database
                    $email = $row[5];
                    $checkEmailSql = "SELECT COUNT(*) AS emailCount FROM Users WHERE email = ?";
                    $params = [$email];
                    $stmt = sqlsrv_query($conn, $checkEmailSql, $params);
                    if ($stmt === false) {
                        echo "<p style='color: red;'>Error checking email: " . print_r(sqlsrv_errors(), true) . "</p>";
                    } else {
                        $rowCount = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['emailCount'];
                        if ($rowCount > 0) {
                            // Skip this row if the email already exists
                            echo "<p>Email {$email} already exists, skipping this row.</p>";
                        } else {
                            // Insert the row if email doesn't exist
                            $params = [$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]];
                            $stmt = sqlsrv_query($conn, $sql, $params);

                            // Check if the query executed successfully
                            if ($stmt === false) {
                                echo "<p style='color: red;'>Error inserting row: " . print_r(sqlsrv_errors(), true) . "</p>";
                            } else {
                                echo "<p>Row inserted successfully: " . implode(", ", $row) . "</p>";
                            }
                        }
                    }
                } else {
                    echo "<p style='color: red;'>Invalid row, skipping: " . implode(", ", $row) . "</p>";
                }
            }

            // Close the file handle
            fclose($handle);
        } else {
            echo "<p style='color: red;'>Error reading CSV file.</p>";
        }
    } else {
        echo "<p style='color: red;'>Error uploading file.</p>";
    }
}

// Close the database connection
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV File Upload and Insert</title>
</head>
<body>
    <h2>Upload CSV File to Insert into Database</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="csvFile">Choose CSV file:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <button type="submit" name="submit">Upload and Insert</button>
    </form>
</body>
</html>
