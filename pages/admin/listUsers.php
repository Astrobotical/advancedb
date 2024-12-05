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
$servername = "BURKE\SQLEXPRESS";
$database = "EcommerceDB";
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
                           $hashedPassword = password_hash($row[6], PASSWORD_DEFAULT);
                            $params = [$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $hashedPassword, $row[7]];
                            $stmt = sqlsrv_query($conn, $sql, $params);

                            // Check if the query executed successfully
                            if ($stmt === false) {
                                echo "<p style='color: red;'>Error inserting row: " . print_r(sqlsrv_errors(), true) . "</p>";
                            } else {
                                echo "<p class='text-white'>Row inserted successfully: " . implode(", ", $row) . "</p>";
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
  <title>Users Management</title>
  <link href="../../public/css/tailwind.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>         
</head>
<body class="bg-bgColor text-gray-800 font-sans h-screen">
<?php include '../../components/navbar.php';?>
  <!-- Header -->
  <header class="bg-bgColor text-primaryColor py-3">
    <h1 class="text-center  text-primaryTextColor text-3xl font-bold ">Users Management</h1>
  </header>
  <!-- Main Content -->
  <main class="px-6 md:px-16 py-5 max-h-screen">
  <div class="p-6 flex flex-col">
    <h2 class="text-2xl font-bold  mb-4  text-primaryTextColor">Upload CSV File to Insert into Database</h2>
    
    <form action="" method="POST" enctype="multipart/form-data" class="flex items-start space-x-4">
    <div>
        <label for="csvFile" class="block text-gray-600 font-medium text-primaryTextColor mb-1">Choose CSV file:</label>
        <input 
            type="file" 
            name="csvFile" 
            id="csvFile" 
            accept=".csv" 
            required 
            class="block px-3 py-2 text-primaryTextColor border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
    </div>
    <button 
        type="submit" 
        name="submit" 
        class="btn btn-primary text-white font-medium py-2 px-6 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 mt-6">
        Upload and Insert
    </button>
</form>
</div>
    <div class="w-full mx-auto ">
    <div class="w-full flex justify-center mt-6">
  <div class="w-full max-w-screen-lg">
    <table class="table w-full divide-y divide-gray-200">
      <thead>
        <tr class="bg-btnPrimary text-white">
          <th class="px-4 py-2 border border-gray-300">#</th>
          <th class="px-4 py-2 border border-gray-300">First Name</th>
          <th class="px-4 py-2 border border-gray-300">Last Name</th>
          <th class="px-4 py-2 border border-gray-300">Actions</th>
        </tr>
      </thead>
      <tbody id="userTable">
        <!-- Dynamic rows will be inserted here -->
      </tbody>
    </table>

    <div id="noDataMessage" class="mt-4 hidden text-center text-gray-600">
      No users found. Add new users to display them here.
    </div>
  </div>
</div>
  </main>

  <!-- Edit Modal -->
<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-base-100 p-6 rounded-lg shadow-lg max-w-lg w-full">
    <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Edit User</h2>
    <form id="editForm">
      <div class="mb-4">
        <label for="editUsername" class="block text-sm font-medium text-primaryTextColor">Username</label>
        <input type="text" id="editUsername" name="username" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editFirstName" class="block text-sm font-medium text-primaryTextColor">First Name</label>
        <input type="text" id="editFirstName" name="first_name" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editLastName" class="block text-sm font-medium text-primaryTextColor">Last Name</label>
        <input type="text" id="editLastName" name="last_name" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editAddress" class="block text-sm font-medium text-primaryTextColor">Address</label>
        <textarea id="editAddress" name="address" class="textarea textarea-bordered w-full border border-solid text-primaryTextColor" required></textarea>
      </div>
      <div class="mb-4">
        <label for="editDOB" class="block text-sm font-medium text-primaryTextColor">Date of Birth</label>
        <input type="date" id="editDOB" name="dob" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editEmail" class="block text-sm font-medium text-primaryTextColor">Email</label>
        <input type="email" id="editEmail" name="email" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editPassword" class="block text-sm font-medium text-primaryTextColor">Password</label>
        <input type="password" id="editPassword" name="password" class="input input-bordered w-full text-primaryTextColor" required>
      </div>
      <div class="mb-4">
        <label for="editRole" class="block text-sm font-medium text-primaryTextColor">Role</label>
        <select id="editRole" name="role" class="input input-bordered w-full text-primaryTextColor">
          <option value="0">Select a Role</option>
          <option value="1">Admin</option>
          <option value="2">Customer</option>
        </select>
      </div>
      <input type="hidden" id="editUserId" name="user_id">
      <div class="flex items-center mb-4">
        <label for="toggleCommit" class="text-primaryTextColor mr-2">Allow Update:</label>
        <input type="checkbox" id="toggleCommit" name="commit_changes" class="toggle">
      </div>
      <div class="flex justify-end space-x-2">
        <button type="button" id="cancelEdit" class="btn btn-outline text-primaryTextColor">Cancel</button>
        <button type="submit" class="btn text-white btn-primary">Update Changes</button>
      </div>
    </form>
  </div>
</div>


  <script>
document.addEventListener('DOMContentLoaded', () => {
    loadUserData();
  });

  async function loadUserData() {
    const userTable = document.getElementById('userTable');
    const noDataMessage = document.getElementById('noDataMessage');

    try {
      // Fetch data from PHP backend
      const response = await fetch('../../data/processes/processUsers.php');
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      userTable.innerHTML = '';
      noDataMessage.classList.add('hidden');
      if (data.length === 0) {
        noDataMessage.classList.remove('hidden');
        return;
      }

      // Populate the table with user data
      data.forEach((user, index) => {
        const userRow = document.createElement('tr');
        userRow.className = 'text-white bg-base-100';

        userRow.innerHTML = `
          <td class="px-4 py-2 text-primaryTextColor">${index + 1}</td>
          <td class="px-4 py-2 text-primaryTextColor">${user.firstName}</td>
          <td class="px-4 py-2 text-primaryTextColor">${user.lastName}</td>
          <td class="px-4 py-2 space-x-2">
         <button class="btn btn-sm btn-primary text-white" 
                        onclick="editUser(
                            ${user.uniqueID}, 
                            '${user.username}', 
                            '${user.firstName}', 
                            '${user.lastName}', 
                            '${user.address}', 
                            '${user.DOB}', 
                            '${user.email}', 
                            ${user.RoleID}
                        )">
                        Edit
                    </button>
            <button class="btn btn-sm btn-error text-white" onclick="deleteUser(${user.uniqueID})">Delete</button>
          </td>
        `;

        userTable.appendChild(userRow);
      });
    } catch (error) {
      console.error('Error loading user data:', error);
      noDataMessage.textContent = 'An error occurred while fetching user data. Please try again later.';
      noDataMessage.classList.remove('hidden');
    }
  }
 
 

    // Modal Elements
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const cancelEdit = document.getElementById('cancelEdit');

  function editUser(userId, username, firstName, lastName, address, dob, email, role, password) {
    document.getElementById('editUserId').value = userId;
    document.getElementById('editUsername').value = username;
    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editLastName').value = lastName;
    document.getElementById('editAddress').value = address;
    document.getElementById('editDOB').value = dob;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPassword').value = password;
    document.getElementById('editRole').value = role;
    editModal.classList.remove('hidden');
  }


    cancelEdit.addEventListener('click', () => {
      editModal.classList.add('hidden');
    });
    
    async function update(){
     
   
    }
    editForm.addEventListener('submit', async function  (event) {
      event.preventDefault(); 
      const userId = document.getElementById('editUserId').value;
    const username = document.getElementById('editUsername').value;
    const firstName = document.getElementById('editFirstName').value;
    const lastName = document.getElementById('editLastName').value;
    const address = document.getElementById('editAddress').value;
    const dob = document.getElementById('editDOB').value;
    const email = document.getElementById('editEmail').value;
    const password = document.getElementById('editPassword').value;
    const role = document.getElementById('editRole').value;
    const commit = document.getElementById('toggleCommit').checked;

    try {
      const response = await fetch('../../data/processes/processUsers.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userId, username, firstName, lastName, address, dob, email, password, role, commit }),
      });
      const result = await response.json();

      if (response.ok && result.success) {
        Swal.fire({ title: 'User updated successfully' });
        loadUserData();
        editModal.classList.add('hidden');
      } else {
        Swal.fire({ title: 'Error updating user', text: result.error });
      }
    } catch (error) {
      console.error('Update failed:', error);
      Swal.fire({ title: 'Error', text: 'An unexpected error occurred.' });
    }
    });

    async function deleteUser(userId) {
      uniqueID
      if (confirm('Are you sure you want to delete this user?')) {
        alert(`User ID ${userId} deleted.`);
        try {
        const response = await fetch('../../data/processes/processUsers.php', {
          method: 'DELETE',headers: {'Content-Type': 'application/json',},
      body: JSON.stringify({uniqueID:userId}),
    });
    const result = await response.json();
    if (response.ok) {
      Swal.fire({title: 'User Account Has been deleted'});
      alert('User wad deleted successfully!');
      loadUserData(); 
      location.reload() 
    } else {
      alert(`Error: ${result.error}`);
    }} catch (error) {
    //console.error('Failed to update user:', error);
   // alert('An error occurred. Please try again later.');
  }
      }
    }
  </script>
    <script src="/../../assets/js/scripts.js"></script>
</body>
</html>