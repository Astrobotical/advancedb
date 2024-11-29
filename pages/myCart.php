<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body class=" text-gray-800 font-sans">
  <!-- Navbar -->
  <?php include __DIR__ . '../../components/navbar.php'; ?>

  <!-- Cart Page Header -->
  <header class="bg-bgColor  py-6">
    <h1 class="text-center text-4xl font-bold">Shopping Cart</h1>
  </header>

  <!-- Cart Content -->
  <main class="px-6 md:px-16 py-12 bg-bgColor h-screen">
    <div class="max-w-4xl mx-auto">
      <!-- Cart Items -->
      <div class="bg-base-100 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Your Cart</h2>
        <div class="space-y-4">
          <!-- Item Row -->
          <div class="flex items-center justify-between border-b pb-4">
            <div class="flex items-center space-x-4">
              <img src="https://via.placeholder.com/80" alt="Item Image" class="w-20 h-20 object-cover rounded-md">
              <div>
                <h3 class="font-semibold text-lg text-primaryTextColor">Item Name</h3>
                <p class="text-primaryTextColor">Category</p>
              </div>
            </div>
            <div class="flex items-center space-x-6 ">
              <div>
                <label for="quantity" class="text-sm font-medium text-primaryTextColor">Qty:</label>
                <input type="number" id="quantity" value="1" class="w-16 border rounded-md text-center text-primaryTextColor">
              </div>
              <p class="text-lg font-bold text-primaryTextColor">$50.00</p>
              <button class="text-red-500 hover:text-red-700 font-semibold">Remove</button>
            </div>
          </div>
          <!-- Add more items as needed -->
        </div>
      </div>

      <!-- Order Summary -->
      <div class=" shadow-md rounded-lg p-6 bg-base-100">
        <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Order Summary</h2>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Subtotal</p>
          <p class="font-semibold text-primaryTextColor">$50.00</p>
        </div>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Tax</p>
          <p class="font-semibold text-primaryTextColor">$5.00</p>
        </div>
        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">Total</p>
          <p class="font-bold text-lg text-primaryTextColor">$55.00</p>
        </div>
        <button class="w-full mt-4 bg-btnPrimary text-white py-3 rounded-lg shadow hover:bg-teal-600">
          Proceed to Checkout
        </button>
      </div>
    </div>
  </main>
  <script src="../../assets/js/scripts.js"></script>
</body>
</html>