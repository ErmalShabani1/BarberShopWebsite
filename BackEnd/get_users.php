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

    $select = 'u.id, u.username, u.email, u.full_name, u.created_at';
    if ($hasDesc) $select .= ', u.description';
    if ($hasImage) $select .= ', u.image_url';

    // Include average rating and rating count per barber using a left-join subquery
    $stmt = $conn->prepare("SELECT {$select}, IFNULL(r.avgRating, 0) AS avgRating, IFNULL(r.ratingCount, 0) AS ratingCount
        FROM users u
        LEFT JOIN (
            SELECT barber_id, AVG(rating) AS avgRating, COUNT(*) AS ratingCount
            FROM rating
            GROUP BY barber_id
        ) r ON u.id = r.barber_id
        WHERE u.role = 'barber'
        ORDER BY u.created_at DESC");
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

        // round avgRating to one decimal and ensure count is an integer
        $b['avgRating'] = isset($row['avgRating']) ? round(floatval($row['avgRating']), 1) : 0.0;
        $b['ratingCount'] = isset($row['ratingCount']) ? intval($row['ratingCount']) : 0;

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
