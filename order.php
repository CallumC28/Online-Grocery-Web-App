<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Place Order | Online Grocery Store</title>
  <meta name="description" content="Place your order for fresh groceries online with multiple items. Enjoy a seamless ordering process with real-time pricing.">
  <meta name="keywords" content="order, grocery, online order, fresh products, shopping">
  <link rel="canonical" href="https://www.teach.scam.keele.ac.uk/prin/x5z36/Web_Development_21011707/order.php">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .fade-in { animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <!-- Header with Navigation -->
  <header class="bg-green-700 shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-3xl text-white font-bold">Place Your Order</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-white hover:text-green-300 transition duration-300">Home</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
          <a href="manager_api.php" class="text-white hover:text-green-300 transition duration-300">Manager API</a>
        <?php endif; ?>
        <a href="my_orders.php" class="text-white hover:text-green-300 transition duration-300">My Orders</a>
        <a href="logout.php" class="text-white hover:text-green-300 transition duration-300">Logout</a>
      </nav>
    </div>
  </header>
  
  <!-- Multi-Item Order Form -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <section class="bg-white p-6 rounded shadow-md max-w-lg mx-auto fade-in">
      <h2 class="text-2xl font-semibold mb-6 text-center text-green-700">Order Products</h2>
      
      <!-- Product Selection Form -->
      <form id="productSelectionForm" class="mb-4">
        <div class="mb-4">
          <label for="category" class="block text-gray-700 mb-2">Select Category:</label>
          <select name="category" id="category" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">--Select Category--</option>
            <option value="Vegetables">Vegetables</option>
            <option value="Meat">Meat</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="product" class="block text-gray-700 mb-2">Select Product:</label>
          <select name="product" id="product" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="">--Select Product--</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="quantity" class="block text-gray-700 mb-2">Quantity:</label>
          <input type="number" name="quantity" id="quantity" value="1" min="1" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <button type="button" id="addToCart" class="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 transition duration-300">Add to Cart</button>
      </form>
      
      <!-- Cart Items Table -->
      <div id="cartContainer" class="mb-4">
        <h3 class="text-xl font-semibold text-green-700 mb-2">Cart Items</h3>
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="border-b p-2">Product</th>
              <th class="border-b p-2">Quantity</th>
              <th class="border-b p-2">Price</th>
              <th class="border-b p-2">Total</th>
              <th class="border-b p-2">Remove</th>
            </tr>
          </thead>
          <tbody id="cartItems">
            <!-- Cart items will be added here dynamically -->
          </tbody>
        </table>
      </div>
      
      <!-- Submit Order Button -->
      <button id="submitOrder" class="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 transition duration-300 mt-4">Submit Order</button>
      <div id="orderMessage" class="mt-4 text-center text-gray-700"></div>
    </section>
  </main>
  
  <!-- Footer -->
  <footer class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-center text-white">
      &copy; <?php echo date("Y"); ?> Online Grocery Store.
    </div>
  </footer>
  
  <script>
    // Array to store items added to the cart
    var cart = [];
    
    // Function to update the cart display
    function updateCartUI() {
      var $cartItems = $('#cartItems');
      $cartItems.empty();
      var grandTotal = 0;
      cart.forEach(function(item, index){
        var total = item.price * item.quantity;
        grandTotal += total;
        var row = '<tr class="fade-in">';
        row += '<td class="p-2 border-b">' + item.name + '</td>';
        row += '<td class="p-2 border-b">' + item.quantity + '</td>';
        row += '<td class="p-2 border-b">$' + parseFloat(item.price).toFixed(2) + '</td>';
        row += '<td class="p-2 border-b">$' + parseFloat(total).toFixed(2) + '</td>';
        row += '<td class="p-2 border-b"><button class="removeItem bg-red-500 text-white px-2 py-1 rounded" data-index="'+index+'">X</button></td>';
        row += '</tr>';
        $cartItems.append(row);
      });
      if(cart.length > 0) {
        $cartItems.append('<tr class="font-semibold"><td colspan="3" class="p-2 border-t">Grand Total</td><td class="p-2 border-t">$' + parseFloat(grandTotal).toFixed(2) + '</td><td class="p-2 border-t"></td></tr>');
      }
    }
    
    $(document).ready(function(){
      // When the category changes, fetch products for that category
      $('#category').change(function(){
        var category = $(this).val();
        if(category !== ''){
          $.ajax({
            url: 'fetch_products.php',
            method: 'POST',
            data: { category: category },
            dataType: 'json',
            success: function(data){
              $('#product').empty().append('<option value="">--Select Product--</option>');
              $.each(data, function(key, value){
                $('#product').append('<option value="'+value.id+'">'+value.name+'</option>');
              });
            }
          });
        }
      });
      
      // Optionally, you can fetch product details when product changes (not required for cart)
      $('#product').change(function(){
        var product_id = $(this).val();
        if(product_id !== ''){
          $.ajax({
            url: 'fetch_product_details.php',
            method: 'POST',
            data: { product_id: product_id },
            dataType: 'json',
            success: function(data){
              // You can display additional product info if desired.
            }
          });
        }
      });
      
      // Add selected product to the cart
      $('#addToCart').click(function(){
        var product_id = $('#product').val();
        var quantity = parseInt($('#quantity').val());
        if(product_id === '' || quantity < 1) {
          alert("Please select a product and enter a valid quantity.");
          return;
        }
        // Get product details to include in the cart item
        $.ajax({
          url: 'fetch_product_details.php',
          method: 'POST',
          data: { product_id: product_id },
          dataType: 'json',
          success: function(data){
            var item = {
              product_id: product_id,
              name: data.name,
              price: parseFloat(data.price),
              quantity: quantity
            };
            cart.push(item);
            updateCartUI();
          }
        });
      });
      
      // Remove an item from the cart when "Remove" is clicked
      $(document).on('click', '.removeItem', function(){
        var index = $(this).data('index');
        cart.splice(index, 1);
        updateCartUI();
      });
      
      // Submit the entire cart as an order
      $('#submitOrder').click(function(){
        if(cart.length === 0){
          alert("Your cart is empty.");
          return;
        }
        $.ajax({
          url: 'order_action.php',
          method: 'POST',
          data: { cart: JSON.stringify(cart) },
          success: function(response){
            $('#orderMessage').html(response);
            // Clear the cart after a successful order submission
            cart = [];
            updateCartUI();
          }
        });
      });
    });
  </script>
</body>
</html>
