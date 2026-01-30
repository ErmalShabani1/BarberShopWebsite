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

// Only admins can request all appointments
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Fetch appointments with user (customer) info and service price (if available)
$sql = "SELECT a.id, a.user_id, a.service_type, a.appointment_date, a.appointment_time, a.status, a.notes, u.username AS customer_username, u.full_name AS customer_fullname, s.name AS service_name, s.price AS service_price
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.id
        LEFT JOIN services s ON s.name = a.service_type
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$res = $conn->query($sql);
$appointments = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        // try to extract barber name from notes: 'Barber: NAME, Service: ...'
        $barberName = null;
        if (!empty($row['notes'])) {
            if (preg_match('/Barber:\s*([^,]+)/i', $row['notes'], $m)) {
                $barberName = trim($m[1]);
            }
        }

        $appointments[] = [
            'id' => (int)$row['id'],
            'customer' => [
                'username' => $row['customer_username'] ?? null,
                'fullName' => $row['customer_fullname'] ?? null
            ],
            'barber' => [
                'name' => $barberName
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
