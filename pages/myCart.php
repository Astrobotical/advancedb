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
  <!-- Inside the `Cart Content` div -->
   <div id="cartItems" class="space-y-4"></div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="shadow-md rounded-lg p-6 bg-base-100" id="orderSummary">
        <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Order Summary</h2>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Subtotal</p>
          <p class="font-semibold text-primaryTextColor">$0.00</p>
        </div>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Tax</p>
          <p class="font-semibold text-primaryTextColor">$5.00</p>
        </div>
        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">Total</p>
          <p class="font-bold text-lg text-primaryTextColor">$0.00</p>
        </div>
        <button class="w-full mt-4 bg-btnPrimary text-white py-3 rounded-lg shadow hover:bg-teal-600">
          Proceed to Checkout
        </button>
      </div>
    </div>
    <div id="checkoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-2xl font-bold mb-4">Checkout</h2>
            <div class="mb-4">
                <label for="paymentMethodSelect" class="block text-lg">Payment Method</label>
                <select id="paymentMethodSelect" class="w-full border rounded-md py-2 px-3">
                    <option value="new">Use New Card</option>
                    <?php
                    $customerID = $_SESSION['userID']; 
                    $query = "SELECT uniqueID, cardNumber, expiryDate FROM PaymentMethods WHERE customerID = ?";
                    $stmt = sqlsrv_query($connection, $query, [$customerID]);

                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $maskedCard = substr($row['cardNumber'], 0, 4) . " **** **** " . substr($row['cardNumber'], -4);
                        echo "<option value='{$row['uniqueID']}'>Card ending in {$maskedCard} (Expires {$row['expiryDate']})</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- New Card Details (Hidden by default) -->
            <div id="newCardFields" class="space-y-4">
                <label for="cardNumber" class="block text-sm font-medium">Card Number</label>
                <input type="text" id="cardNumber" class="w-full border rounded-md py-2 px-3" placeholder="Enter Card Number" maxlength="16">

                <label for="expiryDate" class="block text-sm font-medium">Expiry Date</label>
                <input type="month" id="expiryDate" class="w-full border rounded-md py-2 px-3">

                <label for="cvv" class="block text-sm font-medium">CVV</label>
                <input type="text" id="cvv" class="w-full border rounded-md py-2 px-3" placeholder="Enter CVV" maxlength="4">
            </div>

            <div class="flex justify-end mt-4 space-x-2">
                <button id="closeModalBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                <button id="checkoutBtn" class="bg-btnPrimary text-white px-4 py-2 rounded-md">Proceed</button>
            </div>
        </div>
    </div>
  </main>
  <script>
    document.addEventListener('DOMContentLoaded', loadCartItems);
 // Show the checkout modal when the button is clicked
 document.getElementById('proceedToCheckoutBtn').addEventListener('click', function () {
            document.getElementById('checkoutModal').classList.remove('hidden');
        });

        // Close the modal when the cancel button is clicked
        document.getElementById('closeModalBtn').addEventListener('click', function () {
            document.getElementById('checkoutModal').classList.add('hidden');
        });

        // Toggle between using a new card or saved card
        document.getElementById('paymentMethodSelect').addEventListener('change', function () {
            const newCardFields = document.getElementById('newCardFields');
            if (this.value === 'new') {
                newCardFields.style.display = 'block';
            } else {
                newCardFields.style.display = 'none';
            }
        });

        // Handle checkout form submission
        document.getElementById('checkoutBtn').addEventListener('click', async function () {
            const paymentMethod = document.getElementById('paymentMethodSelect').value;
            let cardData = {};

            if (paymentMethod === 'new') {
                cardData.cardNumber = document.getElementById('cardNumber').value;
                cardData.expiryDate = document.getElementById('expiryDate').value;
                cardData.cvv = document.getElementById('cvv').value;
            } else {
                cardData.paymentMethodID = paymentMethod;
            }

            // Send the data to the server for processing the checkout
            const response = await fetch('../../data/processes/checkoutProcess.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(cardData)
            });

            const result = await response.json();
            if (result.success) {
                alert('Checkout successful!');
                window.location.href = 'orderConfirmation.php'; // Redirect to order confirmation page
            } else {
                alert('Error during checkout: ' + result.error);
            }

            // Close the modal after processing
            document.getElementById('checkoutModal').classList.add('hidden');
        });
async function loadCartItems() {
  try {
    const response = await fetch('../../data/processes/cartActions.php?method=GET');
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to load cart data.');

    const cartItemsContainer = document.getElementById('cartItems');
    cartItemsContainer.innerHTML = ''; // Clear existing content

    if (data.length === 0) {
      cartItemsContainer.innerHTML = '<p class="text-center text-gray-500">Your cart is empty.</p>';
      return;
    }

    data.forEach((item) => {
      const itemRow = document.createElement('div');
      itemRow.className = 'flex items-center justify-between border-b pb-4';
      itemRow.innerHTML = `
        <div class="flex items-center space-x-4">
          <img src="${item.productImage}" alt="${item.productName}" class="w-20 h-20 object-cover rounded-md">
          <div>
            <h3 class="font-semibold text-lg text-primaryTextColor">${item.productName}</h3>
            <p class="text-primaryTextColor">${item.categoryType}</p>
          </div>
        </div>
        <div class="flex items-center space-x-6">
          <div>
            <label for="quantity-${item.uniqueID}" class="text-sm font-medium text-primaryTextColor">Qty:</label>
            <input 
              type="number" 
              id="quantity-${item.uniqueID}" 
              value="${item.quantity}" 
              class="w-16 border rounded-md text-center text-primaryTextColor"
              onchange="updateQuantity(${item.uniqueID}, this.value)"
            >
          </div>
          <p class="text-lg font-bold text-primaryTextColor">$${item.productCost}</p>
          <button 
            class="text-red-500 hover:text-red-700 font-semibold" 
            onclick="deleteItem(${item.uniqueID})"
          >
            Remove
          </button>
        </div>
      `;
      cartItemsContainer.appendChild(itemRow);
    });
  } catch (error) {
    console.error('Error loading cart items:', error);
    document.getElementById('cartItems').innerHTML = '<p class="text-center text-gray-500">Error loading cart items.</p>';
  }
}

async function updateQuantity(itemId, quantity) {
  try {
    const response = await fetch('../../data/processes/cartActions.php?method=UPDATE', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ uniqueID: itemId, quantity: parseInt(quantity, 10) }),
    });
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to update quantity.');

    alert('Quantity updated successfully.');
  } catch (error) {
    console.error('Error updating quantity:', error);
  }
}

async function deleteItem(itemId) {
  try {
    const response = await fetch('../../data/processes/cartActions.php?method=DELETE', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ uniqueID: itemId }),
    });
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to delete item.');

    alert('Item removed successfully.');
    loadCartItems(); // Reload the cart
  } catch (error) {
    console.error('Error deleting item:', error);
  }
}

  </script>
  <script src="../../assets/js/scripts.js"></script>
</body>
</html>