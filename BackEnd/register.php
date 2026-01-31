<?php
session_start();
header('Content-Type: application/json');
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only accept JSON requests from the AJAX client to avoid duplicate native form submissions
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
    if (stripos($contentType, 'application/json') === false) {
        echo json_encode(["success" => false, "message" => "Invalid request format"]);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate presence and non-empty values for required fields
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? trim($data['password']) : '';
    $fullName = isset($data['fullName']) ? trim($data['fullName']) : '';
    $phone = isset($data['phone']) ? trim($data['phone']) : '';

    if ($username === '' || $email === '' || $password === '' || $fullName === '' || $phone === '') {
        echo json_encode(["success" => false, "message" => "Please complete the registration form"]);
        exit();
    }

    $user = new User();
    $result = $user->register($username, $email, $password, $fullName, $phone);
    echo json_encode($result);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
