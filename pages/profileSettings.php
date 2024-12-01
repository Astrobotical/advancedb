<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-bgColor h-screen">
<?php include __DIR__ . '../../components/navbar.php'; ?>
    <div class="max-w-4xl mx-auto p-6 bg-base-100 shadow-lg rounded-lg mt-10">
        <h1 class="text-2xl font-bold mb-6 text-teal-600">User Settings</h1>
        <form id="userSettingsForm" class="space-y-4">
    
            <div>
                <label for="username" class="block font-medium text-primaryTextColor">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-md" required />
            </div>

            <div>
                <label for="firstName" class="block font-medium text-primaryTextColor">First Name</label>
                <input type="text" id="firstName" name="firstName" class="w-full px-4 py-2 border rounded-md" required />
            </div>
            <div>
                <label for="lastName" class="block font-medium text-primaryTextColor">Last Name</label>
                <input type="text" id="lastName" name="lastName" class="w-full px-4 py-2 border rounded-md" required />
            </div>
            <div>
                <label for="address" class="block font-medium text-primaryTextColor">Address</label>
                <textarea id="address" name="address" class="w-full px-4 py-2 border rounded-md" required></textarea>
            </div>
            <div>
                <label for="dob" class="block font-medium text-primaryTextColor">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="w-full px-4 py-2 border rounded-md" required />
            </div>
            <div>
                <label for="email" class="block font-medium text-primaryTextColor">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-md" required />
            </div>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white font-bold rounded-md hover:bg-teal-700">
                Save Changes
            </button>
        </form>
        <p id="message" class="mt-4 text-center text-sm font-semibold text-red-500"></p>
    </div>

    <script>

         async function fetchUserData() {
            try {
                const response = await fetch('../data/processes/processProfileSettings.php');
                const data = await response.json();
                
                if (data.success) {
                    var parsedDate = new Date(data.user.DOB.date);
                    console.log(parsedDate);
                    document.getElementById('username').value = data.user.username;
                    document.getElementById('firstName').value = data.user.firstName;
                    document.getElementById('lastName').value = data.user.lastName;
                    document.getElementById('address').value = data.user.address;
                    document.getElementById('dob').value = `${parsedDate.getFullYear()}-${String(parsedDate.getMonth() + 1).padStart(2, '0')}-${String(parsedDate.getDate()).padStart(2, '0')}`;
                    document.getElementById('email').value = data.user.email;
                } else {
                    document.getElementById('message').textContent = data.error || 'Failed to fetch user data.';
                    document.getElementById('message').classList.add('text-red-600');
                }
            } catch (error) {
                //console.error(error);
                document.getElementById('message').textContent = 'An error occurred while fetching user data.';
            }
        }

        document.addEventListener('DOMContentLoaded', fetchUserData);

        document.getElementById('userSettingsForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {};
            formData.forEach((value, key) => (data[key] = value));

            try {
                const response = await fetch('../data/processes/processProfileSettings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });

                const result = await response.json();
                const message = document.getElementById('message');
                if (result.success) {
                    message.textContent = 'Profile updated successfully!';
                    message.classList.add('text-green-600');
                } else {
                    message.textContent = result.error || 'Failed to update profile.';
                    message.classList.add('text-red-600');
                }
            } catch (error) {
                console.error(error);
                document.getElementById('message').textContent = 'An error occurred while updating the profile.';
            }
        });
    </script>
    <script src="../../assets/js/scripts.js"></script>
</body>
</html>