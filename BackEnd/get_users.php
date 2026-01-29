<?php
header('Content-Type: application/json');
require_once 'db_config.php';

session_start();

$db = new Database();
$conn = $db->connect();

// Check if requesting barbers only (public access)
if (isset($_GET['role']) && $_GET['role'] === 'barber') {
    // detect optional columns
    $dbName = $conn->query("SELECT DATABASE() as db")->fetch_assoc()['db'];
    $hasDesc = false; $hasImage = false;
    $q = $conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users'");
    $q->bind_param('s', $dbName);
    $q->execute();
    $colsRes = $q->get_result();
    while ($c = $colsRes->fetch_assoc()) {
        if ($c['COLUMN_NAME'] === 'description') $hasDesc = true;
        if ($c['COLUMN_NAME'] === 'image_url') $hasImage = true;
    }
    $q->close();

    $select = 'id, username, email, full_name, created_at';
    if ($hasDesc) $select .= ', description';
    if ($hasImage) $select .= ', image_url';

    $stmt = $conn->prepare("SELECT {$select} FROM users WHERE role = 'barber' ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $barbers = [];
    while ($row = $result->fetch_assoc()) {
        $b = [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'fullName' => $row['full_name'],
            'createdAt' => $row['created_at']
        ];
        if ($hasDesc) $b['description'] = $row['description'];
        if ($hasImage) $b['imageUrl'] = $row['image_url'];
        $barbers[] = $b;
    }
    
    $stmt->close();
    $db->close();
    
    echo json_encode(["success" => true, "barbers" => $barbers]);
    exit;
}

// Check if user is logged in and is admin or barber for all users
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'barber')) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

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
