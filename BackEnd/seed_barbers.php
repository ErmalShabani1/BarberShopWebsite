<?php
// Seed static barbers into the users table (role = 'barber')
// Run from browser or CLI: http://localhost/.../BackEnd/seed_barbers.php

require_once 'db_config.php';

$db = new Database();
$conn = $db->connect();

// Ensure columns exist
function ensureColumn($conn, $table, $column, $definition) {
    $check = $conn->prepare("SELECT COUNT(*) as cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $dbName = $conn->query("SELECT DATABASE() as db")->fetch_assoc()['db'];
    $check->bind_param('sss', $dbName, $table, $column);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    $check->close();
    if (intval($res['cnt']) === 0) {
        $conn->query("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

ensureColumn($conn, 'users', 'description', 'TEXT NULL');
ensureColumn($conn, 'users', 'image_url', 'VARCHAR(255) NULL');

$barbers = [
    [
        'username' => 'johnsmith',
        'email' => 'john.smith@barbershop.local',
        'password' => 'barber123',
        'full_name' => 'John Smith',
        'description' => 'Expert in Fades & Classic Cuts',
        'image_url' => '../images/image1.jpg'
    ],
    [
        'username' => 'mikejohnson',
        'email' => 'mike.johnson@barbershop.local',
        'password' => 'barber123',
        'full_name' => 'Mike Johnson',
        'description' => 'Specialist in Modern Styles',
        'image_url' => '../images/image1.jpg'
    ],
    [
        'username' => 'davidbrown',
        'email' => 'david.brown@barbershop.local',
        'password' => 'barber123',
        'full_name' => 'David Brown',
        'description' => 'Master of Beard Grooming',
        'image_url' => '../images/image1.jpg'
    ],
    [
        'username' => 'chriswilson',
        'email' => 'chris.wilson@barbershop.local',
        'password' => 'barber123',
        'full_name' => 'Chris Wilson',
        'description' => 'Contemporary Hair Styling Expert',
        'image_url' => '../images/image1.jpg'
    ]
];

$inserted = 0;
foreach ($barbers as $b) {
    // skip if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $b['username'], $b['email']);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    if ($res->num_rows > 0) continue;

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, description, image_url) VALUES (?, ?, ?, ?, 'barber', ?, ?)");
    $stmt->bind_param('ssssss', $b['username'], $b['email'], $b['password'], $b['full_name'], $b['description'], $b['image_url']);
    if ($stmt->execute()) $inserted++;
    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode(["success" => true, "inserted" => $inserted]);

?>
