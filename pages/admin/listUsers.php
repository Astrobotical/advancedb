<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users Management</title>
  <link href="../../public/css/tailwind.css" rel="stylesheet">         
</head>
<body class="bg-bgColor text-gray-800 font-sans h-screen">
<?php include '../../components/navbar.php';?>
  <!-- Header -->
  <header class="bg-bgColor text-primaryColor py-3">
    <h1 class="text-center text-3xl font-bold">Users Management</h1>
  </header>
  <!-- Main Content -->
  <main class="px-6 md:px-16 py-5 max-h-screen">
    <div class="max-w-6xl mx-auto ">
      <!-- Table -->
      <div class="container mx-auto mt-6">
      <table class="table w-full border-collapse">
    <thead>
      <tr class="bg-teal-600 text-white">
        <th class="px-4 py-2">#</th>
        <th class="px-4 py-2">First Name</th>
        <th class="px-4 py-2">Last Name</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody id="userTable">
      <!-- Data will be inserted dynamically -->
    </tbody>
  </table>

  <div id="noDataMessage" class="mt-4 hidden text-center text-gray-600">
    No users found. Add new users to display them here.
  </div>
    </div>
  </main>

  <!-- Edit Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
      <h2 class="text-2xl font-bold mb-4">Edit User</h2>
      <form id="editForm">
        <div class="mb-4">
          <label for="editFirstName" class="block text-sm font-medium">First Name</label>
          <input type="text" id="editFirstName" name="first_name" class="input input-bordered w-full" required>
        </div>
        <div class="mb-4">
          <label for="editLastName" class="block text-sm font-medium">Last Name</label>
          <input type="text" id="editLastName" name="last_name" class="input input-bordered w-full" required>
        </div>
        <div class="mb-4">
        <label for="editRole" class="block text-sm font-medium">Role</label>
        <select id="editRole" name="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
          <option value="0" class="pl-2">Select a Role if Empty</option>
          <option value="1" class="pl-2">Admin</option>
          <option value="2" class="pl-2">Customer</option>
        </select>
        </div>
        <input type="hidden" id="editUserId" name="user_id">
        <div class="flex justify-end space-x-2">
          <button type="button" id="cancelEdit" class="btn btn-outline">Cancel</button>
          <button type="submit"  class="btn btn-primary">Update Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript -->
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

      // Clear previous content
      userTable.innerHTML = '';
      noDataMessage.classList.add('hidden');

      // Check if data exists
      if (data.length === 0) {
        noDataMessage.classList.remove('hidden');
        return;
      }

      // Populate the table with user data
      data.forEach((user, index) => {
        const userRow = document.createElement('tr');
        userRow.className = 'text-white bg-gray-500';

        userRow.innerHTML = `
          <td class="px-4 py-2">${index + 1}</td>
          <td class="px-4 py-2">${user.firstName}</td>
          <td class="px-4 py-2">${user.lastName}</td>
          <td class="px-4 py-2 space-x-2">
            <button class="btn btn-sm btn-primary text-white" onclick="editUser(${user.uniqueID}, '${user.firstName}', '${user.lastName}','${user.RoleID}')">Edit</button>
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

    // Show Edit Modal with User Details
    async function editUser(userId, firstName, lastName,role){
    var userID=  document.getElementById('editUserId');
    var FirstName = document.getElementById('editFirstName');
    var LastName =  document.getElementById('editLastName');
    var Role=  document.getElementById('editRole');
    userID.value = userId;FirstName.value = firstName;LastName.value = lastName;Role.value = role;
 
    editModal.classList.remove('hidden');
    }

    // Close Edit Modal
    cancelEdit.addEventListener('click', () => {
      editModal.classList.add('hidden');
    });
    
    async function update(){
     
   
    }
    editForm.addEventListener('submit', async function  (event) {
      event.preventDefault(); 
      console.log('Update Clicked 23')
      var userID =  document.getElementById('editUserId');
      var FirstName = document.getElementById('editFirstName');
      var LastName =  document.getElementById('editLastName');
      var Role=  document.getElementById('editRole');
      console.log('Update Clicked')
      try {
        const response = await fetch('../../data/processes/processUsers.php', {
          method: 'PUT',headers: {'Content-Type': 'application/json',},
      body: JSON.stringify({uniqueID:userID.value,RoleID:Role.value,firstName:FirstName.value,lastName:LastName.value}),
    });
    const result = await response.json();
    if (response.ok) {
      alert('User updated successfully!');
      closeEditModal();
      loadUserData(); // Reload user data
    } else {
      alert(`Error: ${result.error}`);
    }} catch (error) {
    //console.error('Failed to update user:', error);
   // alert('An error occurred. Please try again later.');
  }
      editModal.classList.add('hidden');
  
    });

    // Simulate Delete User
    function deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        alert(`User ID ${userId} deleted.`);
        // Perform deletion (AJAX or Page Reload)
      }
    }
  </script>
    <script src="/../../assets/js/scripts.js"></script>
</body>
</html>