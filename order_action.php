<?php
session_start();
if(!isset($_SESSION['user_id'])){
    echo "You must be logged in to place an order.";
    exit;
}
include 'connect.php';

$user_id = $_SESSION['user_id'];
$cart_json = $_POST['cart'];
$cart = json_decode($cart_json, true);

if(empty($cart)) {
    echo "Cart is empty.";
    exit;
}

// Begin a database transaction
$conn->begin_transaction();

// Insert a new order into grocery_db_orders
$order_stmt = $conn->prepare("INSERT INTO orders (user_id) VALUES (?)");
$order_stmt->bind_param("i", $user_id);
if(!$order_stmt->execute()){
    $conn->rollback();
    echo "Failed to place order.";
    exit;
}

$order_id = $conn->insert_id;  //Retrieve the newly created order ID

// Prepare a statement for inserting each order item into order_items
$order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach($cart as $item){
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $order_item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    if(!$order_item_stmt->execute()){
        $conn->rollback();
        echo "Failed to place order item.";
        exit;
    }
}

// Commit the transaction if everything was successful
$conn->commit();

echo "Order placed successfully. Your order ID is " . $order_id;
?>
