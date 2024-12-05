<?php 

session_start(); // Start the session

if (!isset($_SESSION['userID'])) {
    // Respond with an unauthorized message
    echo "<script>alert('Unauthorized access Please Login. Redirecting to the homepage.');</script>";
    
    // Redirect to the index page
    echo "<script>window.location.href = '/index.php';</script>";
    exit(); // Stop further script execution
}

?>
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
    <div id="orderList" class="space-y-6">
      <p class="text-primaryTextColor">Loading order history...</p>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
  async function loadOrderHistory() {
    try {
        const response = await fetch('../../data/processes/processOrder.php');
        const orders = await response.json();

        if (!response.ok || orders.error) {
            throw new Error(orders.error || 'Failed to load order history');
        }

        const ordersContainer = document.getElementById('orderList');
        ordersContainer.innerHTML = ''; // Clear any existing content

        if (orders.length === 0) {
            ordersContainer.innerHTML = '<p class="text-center text-2xl text-primaryTextColor">No orders found.</p>';
            return;
        }

        orders.forEach((order) => {
          let orderTotalSummary = 0 ;
          let calculation = 0;
          order.items.forEach((item) => {const itemTotal = item.quantity * parseFloat(item.price);
          orderTotalSummary += itemTotal;});
          const tax = orderTotalSummary * 0.15;
          orderTotalSummary += tax;
          orderTotalSummary = parseFloat(orderTotalSummary.toFixed(2));
            const orderElement = document.createElement('div');
            orderElement.className = 'bg-base-100 rounded-lg shadow p-6 mb-4';

            orderElement.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-primaryTextColor">Order ID: ${order.orderID}</h2>
                        <h2 class="text-xl  text-primaryTextColor">Order Total: $${orderTotalSummary}</h2>
                        <p class="text-primaryTextColor">Order Date: ${new Date(order.orderDate).toLocaleString()}</p>
                    </div>
                    <button
                        class="text-blue-500 hover:underline"
                        onclick="toggleDetails('${order.orderID}')">
                        View Details
                    </button>
                </div>
                <div id="${order.orderID}" class="hidden">
                    <div class="divide-y divide-gray-200">
                        ${order.items.map(item => `
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center">
                                    <img src="${item.productImage}" alt="${item.productName}" class="w-12 h-12 rounded mr-4">
                                    <div>
                                        <p class="font-medium text-primaryTextColor">${item.productName}</p>
                                        <p class="text-gray-500">Quantity: ${item.quantity}</p>
                                    </div>
                                </div>
                                <p class="font-medium text-primaryTextColor">$${parseFloat(item.price).toFixed(2)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            ordersContainer.appendChild(orderElement);
        });
    } catch (error) {
        console.error('Error loading order history:', error);
        document.getElementById('orderList').innerHTML =
            '<p class="text-center text-gray-500">Error loading order history.</p>';
    }
}
    function toggleDetails(orderId) {
      const element = document.getElementById(orderId);
      element.classList.toggle('hidden');
    }

    // Load order history on page load
    document.addEventListener('DOMContentLoaded', loadOrderHistory);
  </script>
  <script src="../../assets/js/scripts.js"></script>
</body>
</html>