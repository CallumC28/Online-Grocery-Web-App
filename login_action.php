<?php
// login_action.php - Processes login form data

session_start();
include 'connect.php';

$email = $_POST['email'];
$password = $_POST['password'];
$userCaptcha = $_POST['captcha'];

// Check if CAPTCHA input matches the stored CAPTCHA (case-insensitive)
if (!isset($_SESSION['captcha_text']) || strtolower($userCaptcha) !== strtolower($_SESSION['captcha_text'])) {
    echo "Captcha incorrect. <a href='login.php'>Try again</a>";
    exit;
}

// Prepare SQL statement to fetch user details from users table
$login = $conn->prepare("SELECT id, password, role FROM users WHERE email=?");
$login->bind_param("s", $email);
$login->execute();
$result = $login->get_result();

// Check if the user exists and verify the password
if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    if(password_verify($password, $user['password'])){
        // Save user id and role in session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        echo "Invalid credentials. <a href='login.php'>Try again</a>";
    }
} else {
    echo "User not found. <a href='login.php'>Try again</a>";
}
?>
