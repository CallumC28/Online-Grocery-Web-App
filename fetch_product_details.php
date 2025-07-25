<?php
//Returns detailed information for a specific product

session_start();
include 'connect.php';

if(isset($_POST['product_id'])){
    $product_id = $_POST['product_id'];
    // Query the products table for product details
    $details = $conn->prepare("SELECT name, image, price FROM products WHERE id=?");
    $details->bind_param("i", $product_id);
    $details->execute();
    $result = $details->get_result();
    $product = $result->fetch_assoc();
    echo json_encode($product);
}
?>
