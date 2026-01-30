<?php
header('Content-Type: application/json');
require_once 'db_config.php';

session_start();

$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please login"]);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'client';

// Build query based on role
if ($userRole === 'admin') {
    // Admins see all appointments
    $sql = "SELECT 
                a.id, 
                a.user_id, 
                a.barber_id,
                a.service_type, 
                a.appointment_date, 
                a.appointment_time, 
                a.status,
                u.username AS customer_username, 
                u.full_name AS customer_fullname,
                b.full_name AS barber_name,
                s.name AS service_name, 
                s.price AS service_price
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN users b ON a.barber_id = b.id
            LEFT JOIN services s ON s.name = a.service_type
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($sql);
} elseif ($userRole === 'barber') {
    // Barbers see only their appointments
    $sql = "SELECT 
                a.id, 
                a.user_id, 
                a.barber_id,
                a.service_type, 
                a.appointment_date, 
                a.appointment_time, 
                a.status,
                u.username AS customer_username, 
                u.full_name AS customer_fullname,
                b.full_name AS barber_name,
                s.name AS service_name, 
                s.price AS service_price
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN users b ON a.barber_id = b.id
            LEFT JOIN services s ON s.name = a.service_type
            WHERE a.barber_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
} else {
    // Clients see only their own appointments
    $sql = "SELECT 
                a.id, 
                a.user_id, 
                a.barber_id,
                a.service_type, 
                a.appointment_date, 
                a.appointment_time, 
                a.status,
                u.username AS customer_username, 
                u.full_name AS customer_fullname,
                b.full_name AS barber_name,
                s.name AS service_name, 
                s.price AS service_price
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN users b ON a.barber_id = b.id
            LEFT JOIN services s ON s.name = a.service_type
            WHERE a.user_id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$res = $stmt->get_result();
$appointments = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $appointments[] = [
            'id' => (int)$row['id'],
            'customer' => [
                'username' => $row['customer_username'] ?? null,
                'fullName' => $row['customer_fullname'] ?? null
            ],
            'barber' => [
                'name' => $row['barber_name'] ?? 'N/A'
            ],
            'service' => [
                'name' => $row['service_name'] ?? $row['service_type'],
                'price' => $row['service_price'] !== null ? (float)$row['service_price'] : null
            ],
            'date' => $row['appointment_date'],
            'time' => substr($row['appointment_time'],0,5),
            'status' => $row['status']
        ];
    }
}

$db->close();

echo json_encode(["success" => true, "appointments" => $appointments]);
exit;
?>
