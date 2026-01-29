<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once 'User.php';
require_once 'db_config.php';

$response = array('success' => false, 'message' => '');

// Check if user is logged in and is barber or admin
$user = new User();
$userResult = $user->getCurrentUser();

if (!$userResult['success'] || !in_array($userResult['user']['role'], ['admin', 'barber'])) {
    http_response_code(403);
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit();
}

// Database connection
$db = new Database();
$conn = $db->connect();

// Handle POST request - update message status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['messageId']) || !isset($input['status'])) {
        $response['message'] = 'Missing required parameters';
        echo json_encode($response);
        exit();
    }
    
    $messageId = intval($input['messageId']);
    $status = $input['status'];
    
    // Validate status
    if (!in_array($status, ['read', 'unread'])) {
        $response['message'] = 'Invalid status';
        echo json_encode($response);
        exit();
    }
    
    // Update message status
    $stmt = $conn->prepare("UPDATE messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $messageId);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Message status updated successfully';
    } else {
        $response['message'] = 'Failed to update message status';
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
    exit();
}

// Handle GET request - fetch all messages
$stmt = $conn->prepare("SELECT m.*, u.phone FROM messages m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$messages = array();

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

$response['success'] = true;
$response['messages'] = $messages;
echo json_encode($response);
