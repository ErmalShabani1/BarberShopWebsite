<?php
header('Content-Type: text/plain');
require_once 'User.php';
require_once 'db_config.php';

// Check if user is logged in
$user = new User();
$userResult = $user->getCurrentUser();

if (!$userResult['success']) {
    http_response_code(401);
    echo 'unauthorized';
    exit();
}

$currentUser = $userResult['user'];

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo 'invalid_method';
    exit();
}

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo 'incomplete_data';
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'invalid_email';
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Prepare and execute query
$stmt = $conn->prepare("INSERT INTO messages (user_id, name, email, subject, message, status) VALUES (?, ?, ?, ?, ?, 'unread')");

if (!$stmt) {
    http_response_code(500);
    echo 'db_error';
    exit();
}

$user_id = $currentUser['id'];
$stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);

if ($stmt->execute()) {
    $stmt->close();
    $db->close();
    echo 'success';
} else {
    http_response_code(500);
    echo 'db_error';
}

?>
