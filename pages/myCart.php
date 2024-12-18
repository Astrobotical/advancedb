<?php
session_start(); // Start the session

if (!isset($_SESSION['userID'])) {
    // Respond with an unauthorized message
    echo "<script>alert('Unauthorized access Please Login. Redirecting to the homepage.');</script>";
    
    // Redirect to the index page
    echo "<script>window.location.href = '/index.php';</script>";
    exit(); // Stop further script execution
}

require('/../data/connection.php');
$connectionString = new ConnectionString();
$connection = $connectionString->connection;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <link href="../../public/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-bgColor  font-sans">
  <!-- Navbar -->
  <?php include __DIR__ . '../../components/navbar.php'; ?>

  <!-- Cart Page Header -->
  <header class="text-primaryTextColor  py-6">
    <h1 class="text-center text-4xl font-bold text-primaryTextColor">Shopping Cart</h1>
  </header>

  <!-- Cart Content -->
  <main class="px-6 md:px-16 py-12 bg-bgColor h-screen">
    <div class="max-w-4xl mx-auto">
  <!-- Inside the `Cart Content` div -->
   <div id="cartItems" class="space-y-4 pb-4"></div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="shadow-md rounded-lg p-6 bg-base-100" id="orderSummary">
        <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Order Summary</h2>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Subtotal</p>
          <p class="font-semibold text-primaryTextColor" id="subtotal">$0.00</p>
        </div>
        <div class="flex justify-between items-center mb-2">
          <p class="text-primaryTextColor">Tax</p>
          <p class="font-semibold text-primaryTextColor">15%</p>
        </div>
        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">Total</p>
          <p class="font-bold text-lg text-primaryTextColor" id="calculatedTotal">$0.00</p>
        </div>
        <button class="w-full mt-4 bg-btnPrimary text-white py-3 rounded-lg shadow hover:bg-teal-600" id="proceedToCheckoutBtn">
          Proceed to Checkout
        </button>
      </div>
    </div>
    <div id="paymentMethodsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-bgColor p-6 rounded-lg w-96  border border-solid border-white">
            <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Select a Payment Method</h2>
            <div class="mb-4">
                <label for="paymentMethodSelect" class="block text-lg text-primaryTextColor">Payment Method</label>
                <select id="paymentMethodSelect" class="w-full border rounded-md py-2 px-3">
                    <option value="new">Use New Card</option>
                    <?php
                    $customerID = $_SESSION['userID']; 
                    $query = "SELECT uniqueID, cardNumber, expiryDate FROM PaymentMethods WHERE customerID = ?";
                    $stmt = sqlsrv_query($connection, $query, [$customerID]);

                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $maskedCard = substr($row['cardNumber'], 0, 4) . " **** **** " . substr($row['cardNumber'], -4);
                        $changed = $row['expiryDate']->format('M-Y');  // DateTime::createFromFormat('Y-m',(String)$row['expiryDate'] );
                        echo "<option value='{$row['uniqueID']}'>Card ending in {$maskedCard} (Expires {$changed} )</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- New Card Details (Hidden by default) -->
            <div id="newCardFields" class="space-y-4">
                <label for="cardNumber" class="block text-sm font-medium text-primaryTextColor">Card Number</label>
                <input type="text" id="cardNumber" class="w-full border rounded-md py-2 px-3" placeholder="Enter Card Number" maxlength="16">

                <label for="expiryDate" class="block text-sm font-medium text-primaryTextColor">Expiry Date</label>
                <input type="month" id="expiryDate" class="w-full border rounded-md py-2 px-3">

                <label for="cvv" class="block text-sm font-medium text-primaryTextColor">CVV</label>
                <input type="text" id="cvv" class="w-full border rounded-md py-2 px-3" placeholder="Enter CVV" maxlength="4">
            </div>

            <div class="flex justify-end mt-4 space-x-2">
                <button id="closeModalBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                <button id="selectPaymentMethodBtn" class="bg-btnPrimary text-white px-4 py-2 rounded-md">Proceed</button>
            </div>
        </div>
    </div>
    <div id="makePaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-bgColor p-6 rounded-lg w-96 border border-solid border-white">
        <h2 class="text-2xl font-bold mb-4 text-primaryTextColor">Checkout</h2>

        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">Total Items Qty</p>
          <p class="font-bold text-lg text-primaryTextColor"id="totalItems">0</p>
        </div>
        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">TotalCost </p>
          <p class="font-bold text-lg text-primaryTextColor" id="totalCost">$0.00</p>
        </div>
        <div class="flex justify-between items-center border-t pt-4 mt-4">
          <p class="font-bold text-lg text-primaryTextColor">Payment Method </p>
          <p class="font-bold text-lg text-primaryTextColor" id="paymentMethodCard"></p>
          <input type="hidden" id="paymentMethods">
        </div>



        <!-- New Card Details Section -->
        <div id="newCardFields" class="space-y-4 hidden">
            <label for="cardNumber" class="block text-sm font-medium text-primaryTextColor">Card Number</label>
            <input type="text" id="cardNumber" class="w-full border rounded-md py-2 px-3" placeholder="Enter Card Number" maxlength="16">

            <label for="expiryDate" class="block text-sm font-medium text-primaryTextColor">Expiry Date</label>
            <input type="month" id="expiryDate" class="w-full border rounded-md py-2 px-3">

            <label for="cvv" class="block text-sm font-medium text-primaryTextColor">CVV</label>
            <input type="text" id="cvv" class="w-full border rounded-md py-2 px-3" placeholder="Enter CVV" maxlength="4">
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end mt-4 space-x-2">
            <button id="closeModalCheckoutBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
            <button id="confirmPaymentCheckoutBtn" class="bg-btnPrimary text-white px-4 py-2 rounded-md">Confirm</button>
        </div>
    </div>
</div>
  </main>
  <script>
        var cardData = {};
        var cartTotal = 0;
        var cartSubtotal = 0;
        var cartLasttotal = 0;
        var totalCartQty = 0;
    document.addEventListener('DOMContentLoaded', loadCartItems);
 // Show the checkout modal when the button is clicked
 document.getElementById('proceedToCheckoutBtn').addEventListener('click', function () {
            document.getElementById('paymentMethodsModal').classList.remove('hidden');
        });

        // Close the modal when the cancel button is clicked
        document.getElementById('closeModalBtn').addEventListener('click', function () {
            document.getElementById('paymentMethodsModal').classList.add('hidden');
        });
        document.getElementById('confirmPaymentCheckoutBtn').addEventListener('click', function () {
            document.getElementById('makePaymentModal').classList.add('hidden');
            processOrder(); 

        });
        document.getElementById('closeModalCheckoutBtn').addEventListener('click', function () {
            document.getElementById('makePaymentModal').classList.add('hidden');
            cardData = {};
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
        document.getElementById('selectPaymentMethodBtn').addEventListener('click', async function () {
            const paymentMethod = document.getElementById('paymentMethodSelect');
        
            let cartItems =[]; 
            if (paymentMethod.value === 'new') {
                cardData.cardNumber = document.getElementById('cardNumber').value;
                cardData.expiryDate = document.getElementById('expiryDate').value;
                cardData.cvv = document.getElementById('cvv').value;
                await addPaymentMethod();
            } else {
               //Hide current modal 
            document.getElementById('paymentMethodsModal').classList.add('hidden');
            //Show next modal
            document.getElementById('makePaymentModal').classList.remove('hidden');
              //console.log(paymentMethod.options[paymentMethod.selectedIndex].text);
                cardData.paymentMethodID = paymentMethod.value;
                cardData.paymentCard = paymentMethod.options[paymentMethod.selectedIndex].text;
                
            }
            console.log(cardData.paymentCard)
       
            document.getElementById('paymentMethods').value = cardData.paymentMethodID;
            document.getElementById('paymentMethodCard').innerText =cardData.paymentCard;
           document.getElementById('totalItems').innerText = totalCartQty;
           document.getElementById('totalCost').innerText = cartLasttotal;
        });
        async function addPaymentMethod() {
  try {
    const paymentResponse = await fetch('../../data/processes/processPaymentMethods.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(cardData),
    });

    if (paymentResponse.ok) {
      const data = await paymentResponse.json(); // Await to parse JSON

      // Assuming the response contains `paymentMethodID` and `paymentCard`
      cardData.paymentMethodID = data.paymentMethodID;
      cardData.paymentCard = data.paymentCard;

      // Hide current modal
      document.getElementById('paymentMethodsModal').classList.add('hidden');
      // Show next modal
      document.getElementById('makePaymentModal').classList.remove('hidden');

      console.log(data); // Log the response for debugging
    } else {
      // Handle HTTP errors
      console.error('Failed to add payment method:', paymentResponse.status);
      alert('Failed to add payment method. Please try again.');
    }
  } catch (error) {
    // Handle network or unexpected errors
    console.error('An error occurred:', error);
    alert('An unexpected error occurred. Please try again.');
  }
}
async function loadCartItems() {
  try {
    cartSubtotal = 0;
    cartLasttotal = 0;
    totalCartQty = 0;
    document.getElementById('subtotal').innerText = `$0`;
    document.getElementById('calculatedTotal').innerText = `$0`;
    const response = await fetch('../../data/processes/processCart.php?method=GET');
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to load cart data.');

    const cartItemsContainer = document.getElementById('cartItems');
    cartItemsContainer.innerHTML = ''; // Clear existing content

    if (data.length === 0) {
      cartItemsContainer.innerHTML = '<p class="text-center text-2xl mb-6 text-primaryTextColor">Your cart is empty.</p>';
      return;
    }

    data.forEach((item) => {
      cartSubtotal += item.quantity * item.productCost;
      totalCartQty += item.quantity;
      const itemRow = document.createElement('div');
itemRow.innerHTML = `
  <div class="flex items-center justify-between border border-solid rounded-lg p-4 p-6 bg-base-100 shadow-lg">
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
          min="1"
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
  </div>
`;
      cartItemsContainer.appendChild(itemRow);
    });
    cartLasttotal = (cartSubtotal* 0.15)+cartSubtotal;
    document.getElementById('subtotal').innerText = `$${cartSubtotal}`;
    document.getElementById('calculatedTotal').innerText = `$${cartLasttotal}`;
  } catch (error) {
    console.error('Error loading cart items:', error);
    document.getElementById('cartItems').innerHTML = '<p class="text-center text-gray-500">Error loading cart items.</p>';
  }
  
}

async function updateQuantity(itemId, quantity) {
  try {
    const response = await fetch('../../data/processes/processCart.php?method=UPDATE', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ uniqueID: itemId, quantity: parseInt(quantity, 10) }),
    });
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to update quantity.');

    alert('Quantity updated successfully.');
    loadCartItems();
  } catch (error) {
    console.error('Error updating quantity:', error);
  }
}

async function deleteItem(itemId) {
  try {
    const response = await fetch('../../data/processes/processCart.php?method=DELETE', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ uniqueID: itemId }),
    });
    const data = await response.json();

    if (!response.ok) throw new Error(data.error || 'Failed to delete item.');
    loadCartItems(); 
    alert('Item removed successfully.');
    // Reload the cart
  } catch (error) {
    console.error('Error deleting item:', error);
  }
}
async function processOrder() {
        const paymentMethodID = cardData.paymentMethodID; 
        const totalCost = cartLasttotal; 
if (!paymentMethodID) {
    alert('Please select a payment method.');
    return;
}

try {
    // Send the payment method ID and total cost to the server
    const response = await fetch('../../data/processes/processOrder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            paymentMethodID,
            totalCost,
        }),
    });

    const result = await response.json();
    if (response.ok) {
        if (result.success) {
            alert(`Order placed successfully! Order ID: ${result.orderID}`);
            // Optionally redirect to an order summary or confirmation page
            window.location.reload();
        } else {
            alert(result.error || 'Failed to place order.');
        }
    } else {
        alert('An unexpected error occurred. Please try again.');
    }
} catch (error) {
    console.error('Error processing order:', error);
    alert('Failed to process your order. Please try again.');
}
    }
async function clearCart() {
    try {
        const response = await fetch('../../data/processes/processCart.php?method=CLEAR', {
            method: 'POST',
        });

        const data = await response.json();

        if (!response.ok) throw new Error(data.error || 'Failed to clear cart.');

        // Reload the cart view to show the empty cart
        loadCartItems();
    } catch (error) {
        console.error('Error clearing the cart:', error);
        alert('There was an error clearing your cart.');
    }
}

  </script>
  <script src="../../assets/js/scripts.js"></script>
</body>
</html>