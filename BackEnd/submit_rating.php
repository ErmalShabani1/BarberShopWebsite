<?php
header('Content-Type: text/plain');
require_once 'User.php';
require_once 'db_config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$barber_id = isset($_POST['barber_id']) ? intval($_POST['barber_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

// Validate input
if ($barber_id <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo 'invalid_data';
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Verify barber exists and has barber role
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'barber'");
$stmt->bind_param("i", $barber_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    http_response_code(400);
    echo 'invalid_barber';
    exit();
}
$stmt->close();

// Check if user has already rated this barber
$user_id = $currentUser['id'];
$stmt = $conn->prepare("SELECT id FROM rating WHERE user_id = ? AND barber_id = ?");
$stmt->bind_param("ii", $user_id, $barber_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    $db->close();
    echo 'already_rated';
    exit();
}
$stmt->close();

// Insert rating
$stmt = $conn->prepare("INSERT INTO rating (user_id, barber_id, rating) VALUES (?, ?, ?)");

if (!$stmt) {
    http_response_code(500);
    echo 'db_error';
    exit();
}

$stmt->bind_param("iii", $user_id, $barber_id, $rating);

if ($stmt->execute()) {
    $stmt->close();
    $db->close();
    echo 'success';
} else {
    http_response_code(500);
    echo 'db_error';
}

?>
