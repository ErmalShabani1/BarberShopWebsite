<?php
header('Content-Type: application/json');
require_once 'db_config.php';

session_start();

class Booking {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function createAppointment($userId, $barberId, $serviceType, $appointmentDate, $appointmentTime) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return ["success" => false, "message" => "Please login to book an appointment"];
        }

        // Validate barber ID
        if (empty($barberId) || $barberId <= 0) {
            return ["success" => false, "message" => "Invalid barber selection"];
        }

        // Verify that the barber exists and has barber role
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'barber'");
        $stmt->bind_param("i", $barberId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $stmt->close();
            return ["success" => false, "message" => "Selected barber is not available"];
        }
        $stmt->close();

        // Check if the slot is available for this specific barber
        $stmt = $this->conn->prepare("SELECT id FROM appointments WHERE barber_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $stmt->bind_param("iss", $barberId, $appointmentDate, $appointmentTime);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return ["success" => false, "message" => "This time slot is already booked for this barber"];
        }
        $stmt->close();

        // Create appointment
        $stmt = $this->conn->prepare("INSERT INTO appointments (user_id, barber_id, service_type, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $userId, $barberId, $serviceType, $appointmentDate, $appointmentTime);
        
        if ($stmt->execute()) {
            $appointmentId = $stmt->insert_id;
            $stmt->close();
            return [
                "success" => true,
                "message" => "Appointment booked successfully",
                "appointmentId" => $appointmentId
            ];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to book appointment"];
        }
    }

    public function getUserAppointments($userId) {
        $stmt = $this->conn->prepare("
            SELECT a.*, u.full_name as barber_name, u.email as barber_email 
            FROM appointments a 
            JOIN users u ON a.barber_id = u.id 
            WHERE a.user_id = ? 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        $stmt->close();
        return ["success" => true, "appointments" => $appointments];
    }

    public function updateAppointment($appointmentId, $userId, $status) {
        // Get user role to determine permissions
        $userRole = $_SESSION['role'] ?? 'client';
        
        if ($userRole === 'admin') {
            // Admin can update any appointment
            $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $appointmentId);
        } elseif ($userRole === 'barber') {
            // Barber can update only their appointments
            $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND barber_id = ?");
            $stmt->bind_param("sii", $status, $appointmentId, $userId);
        } else {
            // Client can update only their own appointments
            $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $status, $appointmentId, $userId);
        }
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            return ["success" => true, "message" => "Appointment updated successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to update appointment or no permission"];
        }
    }

    public function deleteAppointment($appointmentId, $userId) {
        $stmt = $this->conn->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $appointmentId, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "Appointment deleted successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to delete appointment"];
        }
    }

    public function getAvailableSlots($appointmentDate, $barberId = null) {
        // Get all booked time slots for the given date and barber (excluding cancelled)
        if ($barberId) {
            $stmt = $this->conn->prepare("SELECT TIME_FORMAT(appointment_time, '%H:%i') as appointment_time FROM appointments WHERE barber_id = ? AND appointment_date = ? AND status != 'cancelled'");
            $stmt->bind_param("is", $barberId, $appointmentDate);
        } else {
            $stmt = $this->conn->prepare("SELECT TIME_FORMAT(appointment_time, '%H:%i') as appointment_time FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
            $stmt->bind_param("s", $appointmentDate);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSlots[] = $row['appointment_time'];
        }
        
        $stmt->close();
        return ["success" => true, "bookedSlots" => $bookedSlots];
    }

    public function __destruct() {
        $this->db->close();
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $booking = new Booking();
    
    if (isset($_GET['action']) && $_GET['action'] === 'getAvailableSlots') {
        if (isset($_GET['date'])) {
            $barberId = isset($_GET['barberId']) ? intval($_GET['barberId']) : null;
            $result = $booking->getAvailableSlots($_GET['date'], $barberId);
            echo json_encode($result);
        } else {
            echo json_encode(["success" => false, "message" => "Date is required"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking = new Booking();
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'create':
                if (isset($data['barberId']) && isset($data['serviceType']) && isset($data['appointmentDate']) && isset($data['appointmentTime'])) {
                    $userId = $_SESSION['user_id'];
                    $result = $booking->createAppointment($userId, $data['barberId'], $data['serviceType'], $data['appointmentDate'], $data['appointmentTime']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Missing required fields"]);
                }
                break;
                
            case 'getAppointments':
                if (isset($_SESSION['user_id'])) {
                    $result = $booking->getUserAppointments($_SESSION['user_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Please login first"]);
                }
                break;
                
            case 'update':
                if (isset($data['appointmentId']) && isset($data['status']) && isset($_SESSION['user_id'])) {
                    $result = $booking->updateAppointment($data['appointmentId'], $_SESSION['user_id'], $data['status']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Missing required fields"]);
                }
                break;
                
            case 'delete':
                if (isset($data['appointmentId']) && isset($_SESSION['user_id'])) {
                    $result = $booking->deleteAppointment($data['appointmentId'], $_SESSION['user_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Missing required fields"]);
                }
                break;
                
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No action specified"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
