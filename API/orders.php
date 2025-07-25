<?php
session_start();
include '../connect.php';

// Ensure only managers can access the API
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'manager'){
    http_response_code(403);
    echo json_encode(["error" => "Access denied. You need to be a manager to access this API."]);
    exit;
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Fetch order header details from orders joined with users
    $header_stmt = $conn->prepare("SELECT o.id AS order_id, o.order_date, u.name AS customer_name, u.email, u.phone 
                                   FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   WHERE o.id = ?");
    $header_stmt->bind_param("i", $order_id);
    $header_stmt->execute();
    $header_result = $header_stmt->get_result();
    $header = $header_result->fetch_assoc();
    
    if (!$header) {
        http_response_code(404);
        echo json_encode(["error" => "Order not found."]);
        exit;
    }
    
    // Fetch order items from order_items joined with products
    $items_stmt = $conn->prepare("SELECT oi.quantity, oi.price, p.name AS product_name 
                                  FROM order_items oi 
                                  JOIN products p ON oi.product_id = p.id 
                                  WHERE oi.order_id = ?");
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    $order_total = 0;
    while ($row = $items_result->fetch_assoc()) {
        // Calculate line total for each item
        $row['total'] = $row['quantity'] * $row['price'];
        $order_total += $row['total'];
        $items[] = $row;
    }
    
    // Build the response array
    $response = [
        "order_id"      => $header['order_id'],
        "order_date"    => $header['order_date'],
        "customer_name" => $header['customer_name'],
        "email"         => $header['email'],
        "phone"         => $header['phone'],
        "items"         => $items,
        "order_total"   => $order_total
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing order_id parameter."]);
}
?>
