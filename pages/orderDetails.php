<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History</title>
  <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-bgColor h-screen">
<?php include __DIR__ . '../../components/navbar.php'; ?>
  <div class="max-w-5xl mx-auto p-4">
    <header class="mb-6">
      <h1 class="text-3xl font-bold text-primaryTextColor">Order History</h1>
      <p class="text-primaryTextColor">Review your past orders</p>
    </header>

    <!-- Order List -->
    <div class="space-y-6">
      <!-- Single Order -->
      <div class="bg-base-100 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h2 class="text-xl font-semibold text-primaryTextColor">Order ID: #123456</h2>
            <p class="text-primaryTextColor">Order Date: November 20, 2024</p>
          </div>
          <button
            class="text-blue-500 hover:underline"
            onclick="toggleDetails('order-1')">
            View Details
          </button>
        </div>
        <div id="order-1" class="hidden">
          <!-- Order Products -->
          <div class="divide-y divide-gray-200">
            <div class="flex items-center justify-between py-4">
              <div class="flex items-center">
                <img src="https://via.placeholder.com/50" alt="Product Image" class="w-12 h-12 rounded mr-4">
                <div>
                  <p class="font-medium text-primaryTextColor">Product Name 1</p>
                  <p class="text-gray-500">Quantity: 2</p>
                </div>
              </div>
              <p class="font-medium text-primaryTextColor">$40.00</p>
            </div>
            <div class="flex items-center justify-between py-4">
              <div class="flex items-center">
                <img src="https://via.placeholder.com/50" alt="Product Image" class="w-12 h-12 rounded mr-4">
                <div>
                  <p class="font-medium text-primaryTextColor">Product Name 2</p>
                  <p class="text-gray-500">Quantity: 1</p>
                </div>
              </div>
              <p class="font-medium text-primaryTextColor">$25.00</p>
            </div>
          </div>
          <!-- Payment Summary -->
          <div class="mt-4 text-primaryTextColor">
            <div class="flex justify-between">
              <p>Subtotal:</p>
              <p>$65.00</p>
            </div>
            <div class="flex justify-between">
              <p>Shipping:</p>
              <p>$5.00</p>
            </div>
            <div class="flex justify-between font-semibold text-primaryTextColor">
              <p>Total:</p>
              <p>$70.00</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Another Order -->
      <div class="bg-base-100 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h2 class="text-xl font-semibold text-primaryTextColor">Order ID: #654321</h2>
            <p class="text-primaryTextColor">Order Date: November 15, 2024</p>
          </div>
          <button
            class="text-blue-500 hover:underline"
            onclick="toggleDetails('order-2')">
            View Details
          </button>
        </div>
        <div id="order-2" class="hidden">
          <!-- Order Products -->
          <div class="divide-y divide-gray-200">
            <div class="flex items-center justify-between py-4">
              <div class="flex items-center">
                <img src="https://via.placeholder.com/50" alt="Product Image" class="w-12 h-12 rounded mr-4">
                <div>
                  <p class="font-medium text-primaryTextColor">Product Name A</p>
                  <p class="text-gray-500">Quantity: 3</p>
                </div>
              </div>
              <p class="font-medium text-primaryTextColor">$60.00</p>
            </div>
            <div class="flex items-center justify-between py-4">
              <div class="flex items-center">
                <img src="https://via.placeholder.com/50" alt="Product Image" class="w-12 h-12 rounded mr-4">
                <div>
                  <p class="font-medium text-primaryTextColor">Product Name B</p>
                  <p class="text-gray-500">Quantity: 1</p>
                </div>
              </div>
              <p class="font-medium text-primaryTextColor">$30.00</p>
            </div>
          </div>
          <!-- Payment Summary -->
          <div class="mt-4 text-primaryTextColor">
            <div class="flex justify-between">
              <p>Subtotal:</p>
              <p>$90.00</p>
            </div>
            <div class="flex justify-between">
              <p>Shipping:</p>
              <p>$10.00</p>
            </div>
            <div class="flex justify-between font-semibold text-primaryTextColor">
              <p>Total:</p>
              <p>$100.00</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    function toggleDetails(orderId) {
      const element = document.getElementById(orderId);
      element.classList.toggle('hidden');
    }
  </script>
<script src="../../assets/js/scripts.js"></script>
</body>
</html>