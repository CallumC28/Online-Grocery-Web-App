<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'connect.php';

$user_id = $_SESSION['user_id'];
// Fetch orders for the logged-in user from orders
$order_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders | Online Grocery Store</title>
  <meta name="description" content="View all of your past orders on the Online Grocery Store.">
  <meta name="keywords" content="my orders, order history, online grocery">
  <link rel="canonical" href="https://www.teach.scam.keele.ac.uk/prin/x5z36/Web_Development_21011707/my_orders.php">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in { animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <header class="bg-green-700 shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-3xl text-white font-bold">My Orders</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-white hover:text-green-300 transition duration-300">Home</a>
        <a href="order.php" class="text-white hover:text-green-300 transition duration-300">Order</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
          <a href="manager_api.php" class="text-white hover:text-green-300 transition duration-300">Manager API</a>
        <?php endif; ?>
        <a href="logout.php" class="text-white hover:text-green-300 transition duration-300">Logout</a>
      </nav>
    </div>
  </header>
  
  <!-- Display User Orders -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <section class="fade-in flex-grow">
      <?php if (count($orders) === 0): ?>
        <p class="text-center text-gray-700">You have not placed any orders yet.</p>
      <?php else: ?>
        <?php foreach ($orders as $order): ?>
          <div class="bg-white p-6 rounded shadow-md mb-6">
            <h2 class="text-xl font-bold text-green-700">Order ID: <?php echo htmlspecialchars($order['id']); ?></h2>
            <p class="text-gray-700">Order Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
            <?php
              // Fetch order items for this order from order_items joined with products
              $order_id = $order['id'];
              $items_stmt = $conn->prepare("SELECT oi.quantity, oi.price, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
              $items_stmt->bind_param("i", $order_id);
              $items_stmt->execute();
              $items_result = $items_stmt->get_result();
            ?>
            <table class="w-full mt-4 text-left border-collapse">
              <thead>
                <tr>
                  <th class="border-b p-2">Product</th>
                  <th class="border-b p-2">Quantity</th>
                  <th class="border-b p-2">Price</th>
                  <th class="border-b p-2">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $order_total = 0;
                while($item = $items_result->fetch_assoc()): 
                  $total = $item['quantity'] * $item['price'];
                  $order_total += $total;
                ?>
                <tr>
                  <td class="p-2 border-b"><?php echo htmlspecialchars($item['product_name']); ?></td>
                  <td class="p-2 border-b"><?php echo $item['quantity']; ?></td>
                  <td class="p-2 border-b">£<?php echo number_format($item['price'], 2); ?></td>
                  <td class="p-2 border-b">£<?php echo number_format($total, 2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="font-semibold">
                  <td colspan="3" class="p-2 border-t">Order Total</td>
                  <td class="p-2 border-t">£<?php echo number_format($order_total, 2); ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
  
  <!-- Footer -->
  <footer class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-center text-white">
      &copy; <?php echo date("Y"); ?> Online Grocery Store.
    </div>
  </footer>
</body>
</html>
