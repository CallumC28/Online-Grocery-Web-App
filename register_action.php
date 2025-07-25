<?php

header('Content-Type: application/json');
include 'connect.php';

// Get the JSON input data from the request
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$phone = $data['phone'];
$email = $data['email'];
// Hash the password for security
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Validate input values on the server side
if(!preg_match("/^[A-Za-z\s]+$/", $name)){
    echo json_encode(["success" => false, "message" => "Invalid name."]);
    exit;
}
if(!preg_match("/^\d{10}$/", $phone)){
    echo json_encode(["success" => false, "message" => "Invalid phone number."]);
    exit;
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo json_encode(["success" => false, "message" => "Invalid email."]);
    exit;
}

// New users are stored as 'customer', did have this set to 'manager' initially just to create the manager account.
$role = 'customer';

// Insert the new user into the users table
$newuser = $conn->prepare("INSERT INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)");
$newuser->bind_param("sssss", $name, $phone, $email, $password, $role);
if($newuser->execute()){
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Email may already be registered."]);
}
?>
