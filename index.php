<?php
session_start();
include 'connect.php';
// Check if the user is logged in and if the user is a manager
$isLoggedIn = isset($_SESSION['user_id']);
$isManager = (isset($_SESSION['role']) && $_SESSION['role'] === 'manager');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Online Grocery Store | Fresh Products Online</title>
  <meta property="og:title" content="Online Grocery Store | Fresh Products Online">
  <meta name="description" content="Browse and order fresh grocery products online. Categories include Vegetables and Meat with real-time pricing available for registered users.">
  <meta name="keywords" content="grocery, online store, vegetables, meat, fresh products">
  <link rel="canonical" href="https://www.teach.scam.keele.ac.uk/prin/x5z36/Web_Development_21011707/index.php">
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
      <h1 class="text-3xl text-white font-bold">Online Grocery Store</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-white hover:text-green-300 transition duration-300">Home</a>
        <?php if($isLoggedIn): ?>
          <a href="order.php" class="text-white hover:text-green-300 transition duration-300">Order</a>
          <a href="my_orders.php" class="text-white hover:text-green-300 transition duration-300">My Orders</a>
          <?php if($isManager): ?>
            <a href="manager_api.php" class="text-white hover:text-green-300 transition duration-300">Manager API</a>
          <?php endif; ?>
          <a href="logout.php" class="text-white hover:text-green-300 transition duration-300">Logout</a>
        <?php else: ?>
          <a href="register.php" class="text-white hover:text-green-300 transition duration-300">Register</a>
          <a href="login.php" class="text-white hover:text-green-300 transition duration-300">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  
  <!-- Product Browser -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <section class="bg-white p-6 rounded shadow-md max-w-lg mx-auto fade-in">
      <h2 class="text-2xl font-semibold mb-6 text-center text-green-700">Browse Products</h2>
      <!-- Product Selection Form -->
      <form id="productForm">
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
      </form>
      <!-- Div to display product details (includes image, name, and if logged in the price) once the ajax returns the info -->
      <div id="productDetails" class="mt-6 text-center"></div>
    </section>
  </main>
  
  <!-- Footer for SEO -->
  <footer class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-center text-white">
      &copy; <?php echo date("Y"); ?> Online Grocery Store. All rights reserved.
    </div>
  </footer>
  
  <script>
    // Variable indicating whether the user is logged in or not
    var isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    
    $(document).ready(function(){
      // When the category dropdown changes, fetch products in that category
      $('#category').change(function(){
        var category = $(this).val();
        if(category != ''){
          $.ajax({
            url: 'fetch_products.php',  // Returns list of products
            method: 'POST',
            data: { category: category },
            dataType: 'json',
            success: function(data){
              $('#product').empty();
              $('#product').append('<option value="">--Select Product--</option>');
              $.each(data, function(key, value){
                $('#product').append('<option value="'+value.id+'">'+value.name+'</option>');
              });
            }
          });
        }
      });
      
      // When a product is selected, fetch its details and display them
      $('#product').change(function(){
        var product_id = $(this).val();
        if(product_id != ''){
          $.ajax({
            url: 'fetch_product_details.php',  // Returns product details as JSON
            method: 'POST',
            data: { product_id: product_id },
            dataType: 'json',
            success: function(data){
              var details = '<div class="fade-in">';
              details += '<h3 class="text-2xl font-semibold mb-2 text-green-700">'+data.name+'</h3>';
              details += '<img src="'+data.image+'" alt="'+data.name+' image" class="mb-2 w-96 h-auto mx-auto object-cover rounded hover:scale-105 transition-transform duration-300">';
              if(isLoggedIn && data.price !== null) {
                details += '<p class="text-gray-700 font-semibold">Price: £' +data.price+'</p>';
              } else {
                details += '<p class="text-gray-700 font-semibold">Login to view price</p>';
              }
              details += '</div>';
              $('#productDetails').html(details);
            }
          });
        } else {
          $('#productDetails').html('');
        }
      });
    });
  </script>
</body>
</html>

<!-- URL - https://www.teach.scam.keele.ac.uk/prin/x5z36/Web_Development_Coursework_Improvement/index.php -->