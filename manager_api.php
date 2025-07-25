<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'manager'){
    header("Location: index.php");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager API | Online Grocery Store</title>
  <meta name="description" content="Manager API for Online Grocery Store. View order details item by item using a RESTful web service.">
  <meta name="keywords" content="manager, api, orders, RESTful">
  <link rel="canonical" href="https://www.teach.scam.keele.ac.uk/prin/x5z36/Web_Development_21011707/manager_api.php">
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
      <h1 class="text-3xl text-white font-bold">Manager API</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-white hover:text-green-300 transition duration-300">Home</a>
        <?php if($isLoggedIn): ?>
          <a href="order.php" class="text-white hover:text-green-300 transition duration-300">Order</a>
          <a href="my_orders.php" class="text-white hover:text-green-300 transition duration-300">My Orders</a>
          <a href="logout.php" class="text-white hover:text-green-300 transition duration-300">Logout</a>
        <?php else: ?>
          <a href="register.php" class="text-white hover:text-green-300 transition duration-300">Register</a>
          <a href="login.php" class="text-white hover:text-green-300 transition duration-300">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  
  <!--Manager API Form -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <section class="bg-white p-6 rounded shadow-md max-w-lg mx-auto fade-in">
      <h2 class="text-2xl font-semibold mb-6 text-center text-green-700">View Order Details</h2>
      <form id="apiForm">
        <div class="mb-4">
          <label for="order_id" class="block text-gray-700 mb-2">Enter Order ID:</label>
          <input type="number" name="order_id" id="order_id" required class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <button type="submit" class="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 transition duration-300">Get Order Details</button>
      </form>
      <div id="apiResult" class="mt-6"></div>
    </section>
  </main>
  
  <!-- Footer -->
  <footer class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-center text-white">
      &copy; <?php echo date("Y"); ?> Online Grocery Store.
    </div>
  </footer>
  
  <script>
    $(document).ready(function(){
      // When the API form is submitted, fetch order details from the RESTful API
      $('#apiForm').submit(function(e){
        e.preventDefault();
        var order_id = $('#order_id').val();
        if(order_id === ''){
          alert("Please enter an order ID.");
          return;
        }
        $.ajax({
          url: 'API/orders.php',
          method: 'GET',
          data: { order_id: order_id },
          dataType: 'json',
          success: function(data){
            var result = '<div class="fade-in p-4 bg-gray-100 rounded">';
            result += '<p><strong>Order ID:</strong> ' + data.order_id + '</p>';
            result += '<p><strong>Order Date:</strong> ' + data.order_date + '</p>';
            result += '<p><strong>Customer Name:</strong> ' + data.customer_name + '</p>';
            result += '<p><strong>Email:</strong> ' + data.email + '</p>';
            result += '<p><strong>Phone:</strong> ' + data.phone + '</p>';
            result += '<hr>';
            if(data.items && data.items.length > 0) {
              data.items.forEach(function(item, index){
                result += '<p><strong>Item ' + (index+1) + ':</strong></p>';
                result += '<p>Product Name: ' + item.product_name + '</p>';
                result += '<p>Quantity: ' + item.quantity + '</p>';
                result += '<p>Price: £' + parseFloat(item.price).toFixed(2) + '</p>';
                result += '<p>Total: £' + parseFloat(item.total).toFixed(2) + '</p>';
                result += '<br>';
              });
            } else {
              result += '<p>No order items found.</p>';
            }
            result += '<hr>';
            result += '<p class="font-bold">Order Total: £' + parseFloat(data.order_total).toFixed(2) + '</p>';
            result += '</div>';
            $('#apiResult').html(result);
          },
          error: function(xhr){
            $('#apiResult').html('<p class="text-red-500">Error: ' + xhr.responseJSON.error + '</p>');
          }
        });
      });
    });
  </script>
</body>
</html>
