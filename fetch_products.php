<?php
// Returns list of products for a given category

include 'connect.php';

if(isset($_POST['category'])){
    $category = $_POST['category'];
    $item = $conn->prepare("SELECT id, name FROM products WHERE category=?");
    $item->bind_param("s", $category);
    $item->execute();
    $result = $item->get_result();
    $products = [];
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
    // Return products as JSON
    echo json_encode($products);
}
?>
