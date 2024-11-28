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
  <header class="bg-bgColor text-white py-6">
    <h1 class="text-center text-3xl font-bold">Users Management</h1>
  </header>
  <!-- Main Content -->
  <main class="px-6 md:px-16 py-12 max-h-screen">
    <div class="max-w-6xl mx-auto ">
      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="table w-fullh  border-collapse">
          <thead>
            <tr class="bg-teal-600 text-white">
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">First Name</th>
              <th class="px-4 py-2">Last Name</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody id="userTable">
            <!-- Example Row (to be replaced dynamically) -->
            <!-- PHP Backend: Fetch Users -->
            <?php
              // Assuming `$users` is an array of users fetched from your database
              $users = [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith'],
              ];
              foreach ($users as $index => $user) {
                echo "<tr class='text-white bg-gray-500'>
                  <td class='px-4 py-2'>" . ($index + 1) . "</td>
                  <td class='px-4 py-2'>{$user['first_name']}</td>
                  <td class='px-4 py-2'>{$user['last_name']}</td>
                  <td class='px-4 py-2 space-x-2'>
                    <button class='btn btn-sm btn-primary text-primaryTextColor' onclick='editUser({$user['id']}, \"{$user['first_name']}\", \"{$user['last_name']}\")'>Edit</button>
                    <button class='btn btn-sm btn-error text-primaryTextColor' onclick='deleteUser({$user['id']})'>Delete</button>
                  </td>
                </tr>";
              }
            ?>
          </tbody>
        </table>
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

    // Submit Edit Form (AJAX Simulation)
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