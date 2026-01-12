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

    public function createAppointment($userId, $serviceType, $appointmentDate, $appointmentTime, $notes = null) {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return ["success" => false, "message" => "Please login to book an appointment"];
        }

        // Check if the slot is available
        $stmt = $this->conn->prepare("SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $stmt->bind_param("ss", $appointmentDate, $appointmentTime);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return ["success" => false, "message" => "This time slot is already booked"];
        }
        $stmt->close();

        // Create appointment
        $stmt = $this->conn->prepare("INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $serviceType, $appointmentDate, $appointmentTime, $notes);
        
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
        $stmt = $this->conn->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
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
        $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $status, $appointmentId, $userId);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "Appointment updated successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to update appointment"];
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

    public function __destruct() {
        $this->db->close();
    }
}

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking = new Booking();
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'create':
                if (isset($data['serviceType']) && isset($data['appointmentDate']) && isset($data['appointmentTime'])) {
                    $userId = $_SESSION['user_id'];
                    $notes = isset($data['notes']) ? $data['notes'] : null;
                    $result = $booking->createAppointment($userId, $data['serviceType'], $data['appointmentDate'], $data['appointmentTime'], $notes);
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
