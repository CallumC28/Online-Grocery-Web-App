<?php
session_start();
// Set headers to prevent caching and show image content type
header("Content-Type: image/jpg");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache"); //added incase users are on older version of http/1.0 (unlikely but there just incase)
header("Expires: 0");

// Array of available CAPTCHA images and their corresponding text
$captchas = [
    [ 'captcha' => 'image1.jpg', 'text' => 'Aeik2' ],
    [ 'captcha' => 'image2.jpg', 'text' => 'ecb4f' ],
    [ 'captcha' => 'image3.jpg', 'text' => '7PLBJ8' ],
    [ 'captcha' => 'image4.jpg', 'text' => '24qu3' ]
];

// Randomly choose one CAPTCHA from the array
$index = array_rand($captchas);
$selectedCaptcha = $captchas[$index];

// Store the correct CAPTCHA text in the session
$_SESSION['captcha_text'] = $selectedCaptcha['text'];

// Build the full file path to the CAPTCHA image
$filePath = __DIR__ . '/CaptchaImages/' . $selectedCaptcha['captcha'];

// If the image file doesn't exist, output an error
if (!file_exists($filePath)) {
    die("CAPTCHA image not found.");
}

// Output the image file to the browser
readfile($filePath);
exit;
?>
