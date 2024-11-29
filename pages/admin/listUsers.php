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
  <header class="bg-bgColor text-primaryColor py-6">
    <h1 class="text-center text-3xl font-bold">Users Management</h1>
  </header>
  <!-- Main Content -->
  <main class="px-6 md:px-16 py-12 max-h-screen">
    <div class="max-w-6xl mx-auto ">
      <!-- Table -->
      <div class="container mx-auto mt-6">
  <table class="table w-full border-collapse border border-gray-300">
    <thead>
      <tr class="bg-teal-600 text-white">
        <th class="px-4 py-2">#</th>
        <th class="px-4 py-2">First Name</th>
        <th class="px-4 py-2">Last Name</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody id="userTable" class="text-center">
      <!-- Content will be dynamically loaded -->
    </tbody>
  </table>
  <!-- Placeholder for messages -->
  <div id="message" class="mt-4 text-center text-gray-500"></div>
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
        <input type="hidden" id="editUserId" name="user_id">
        <div class="flex justify-end space-x-2">
          <button type="button" id="cancelEdit" class="btn btn-outline">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
document.addEventListener('DOMContentLoaded', async function () {
  // Function to fetch and populate the table
  async function loadUserData() {
    try {
      // Make an HTTP GET request to fetch user data
      const response = await fetch('loadUserData.php');
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json(); // Parse response as JSON

      const userTable = document.getElementById('userTable');
      const message = document.getElementById('message');

      userTable.innerHTML = ''; // Clear any existing rows
      message.innerHTML = ''; // Clear any previous messages

      if (data.length === 0) {
        // No data case
        message.innerHTML = `<p class="text-red-500">No user data found.</p>`;
        return;
      }

      // Populate the table with user data
      data.forEach((user, index) => {
        const userRow = document.createElement('tr');
        userRow.className = 'text-white bg-gray-500 hover:bg-gray-400';

        userRow.innerHTML = `
          <td class="px-4 py-2 border border-gray-300">${index + 1}</td>
          <td class="px-4 py-2 border border-gray-300">${user.first_name}</td>
          <td class="px-4 py-2 border border-gray-300">${user.last_name}</td>
          <td class="px-4 py-2 border border-gray-300 space-x-2">
            <button class="btn btn-sm btn-primary text-white" onclick="editUser(${user.id}, '${user.first_name}', '${user.last_name}')">Edit</button>
            <button class="btn btn-sm btn-error text-white" onclick="deleteUser(${user.id})">Delete</button>
          </td>
        `;

        userTable.appendChild(userRow);
      });
    } catch (error) {
      console.error('Error loading user data:', error);
      const message = document.getElementById('message');
      message.innerHTML = `<p class="text-red-500">Error loading user data. Please try again later.</p>`;
    }
  }

  // Load user data when the DOM is ready
  loadUserData();
});

    // Modal Elements
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const cancelEdit = document.getElementById('cancelEdit');

    // Show Edit Modal with User Details
    function editUser(userId, firstName, lastName) {
      document.getElementById('editUserId').value = userId;
      document.getElementById('editFirstName').value = firstName;
      document.getElementById('editLastName').value = lastName;
      editModal.classList.remove('hidden');
    }

    // Close Edit Modal
    cancelEdit.addEventListener('click', () => {
      editModal.classList.add('hidden');
    });

    editForm.addEventListener('submit', function (event) {
      event.preventDefault();
      const formData = new FormData(editForm);
      const userId = formData.get('user_id');
      const firstName = formData.get('first_name');
      const lastName = formData.get('last_name');

      // Simulate Updating the Row
      alert(`User ID ${userId} updated to ${firstName} ${lastName}`);
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