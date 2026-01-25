<?php
header('Content-Type: application/json');
require_once 'db_config.php';

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

// Get all users
$stmt = $conn->prepare("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'email' => $row['email'],
        'fullName' => $row['full_name'],
        'role' => $row['role'],
        'createdAt' => $row['created_at']
    ];
}

$stmt->close();
$db->close();

echo json_encode(["success" => true, "users" => $users]);
?>
