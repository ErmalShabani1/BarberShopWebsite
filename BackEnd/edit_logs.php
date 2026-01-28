<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php';

class EditLogger {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function logEdit($entityType, $entityId, $action, $oldValues = null, $newValues = null, $description = null) {
        if (!isset($_SESSION['user_id'])) {
            return ["success" => false, "message" => "Not logged in"];
        }

        $userId = $_SESSION['user_id'];
        $editedBy = $_SESSION['username'] ?? 'unknown';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $oldValuesJson = $oldValues ? json_encode($oldValues) : null;
        $newValuesJson = $newValues ? json_encode($newValues) : null;

        $stmt = $this->conn->prepare(
            "INSERT INTO edit_logs (user_id, edited_by, entity_type, entity_id, action, old_values, new_values, change_description, ip_address, user_agent) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issiisssss",
            $userId,
            $editedBy,
            $entityType,
            $entityId,
            $action,
            $oldValuesJson,
            $newValuesJson,
            $description,
            $ipAddress,
            $userAgent
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "Edit logged successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to log edit"];
        }
    }

    public function getEditLogs($entityType = null, $entityId = null, $limit = 100, $offset = 0) {
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

        $query = "SELECT id, edited_by, entity_type, entity_id, action, change_description, created_at FROM edit_logs WHERE 1=1";
        $types = "";
        $params = [];

        if ($entityType) {
            $query .= " AND entity_type = ?";
            $types .= "s";
            $params[] = $entityType;
        }

        if ($entityId) {
            $query .= " AND entity_id = ?";
            $types .= "i";
            $params[] = $entityId;
        }

        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);

        if ($types) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $logs = [];

        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        $stmt->close();
        return ["success" => true, "logs" => $logs];
    }

    public function getServiceEditHistory($serviceId) {
    
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

        $stmt = $this->conn->prepare(
            "SELECT id, edited_by, action, old_values, new_values, change_description, created_at 
             FROM edit_logs 
             WHERE entity_type = 'services' AND entity_id = ? 
             ORDER BY created_at DESC"
        );

        $stmt->bind_param("i", $serviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];

        while ($row = $result->fetch_assoc()) {
          
            if ($row['old_values']) {
                $row['old_values'] = json_decode($row['old_values'], true);
            }
            if ($row['new_values']) {
                $row['new_values'] = json_decode($row['new_values'], true);
            }
            $history[] = $row;
        }

        $stmt->close();
        return ["success" => true, "history" => $history];
    }

    public function getAllEditSummary() {
     
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

       
        $stmt = $this->conn->prepare(
            "SELECT entity_type, COUNT(*) as edit_count, MAX(created_at) as last_edit 
             FROM edit_logs 
             GROUP BY entity_type 
             ORDER BY last_edit DESC"
        );

        $stmt->execute();
        $result = $stmt->get_result();
        $summary = [];

        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }

        $stmt->close();
        return ["success" => true, "summary" => $summary];
    }

    public function getRecentEdits($limit = 50) {
      
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

        $stmt = $this->conn->prepare(
            "SELECT id, edited_by, entity_type, entity_id, action, change_description, created_at 
             FROM edit_logs 
             ORDER BY created_at DESC 
             LIMIT ?"
        );

        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $edits = [];

        while ($row = $result->fetch_assoc()) {
            $edits[] = $row;
        }

        $stmt->close();
        return ["success" => true, "edits" => $edits];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $logger = new EditLogger();

    switch ($action) {
        case 'getRecentEdits':
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
            $result = $logger->getRecentEdits($limit);
            echo json_encode($result);
            break;

        case 'getEditLogs':
            $entityType = isset($_GET['entityType']) ? $_GET['entityType'] : null;
            $entityId = isset($_GET['entityId']) ? intval($_GET['entityId']) : null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            $result = $logger->getEditLogs($entityType, $entityId, $limit, $offset);
            echo json_encode($result);
            break;

        case 'getServiceHistory':
            if (isset($_GET['serviceId'])) {
                $result = $logger->getServiceEditHistory(intval($_GET['serviceId']));
                echo json_encode($result);
            } else {
                echo json_encode(["success" => false, "message" => "Service ID required"]);
            }
            break;

        case 'getSummary':
            $result = $logger->getAllEditSummary();
            echo json_encode($result);
            break;

        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $logger = new EditLogger();

    if (isset($data['action']) && $data['action'] === 'logEdit') {
        if (isset($data['entityType'], $data['entityId'], $data['action'])) {
            $result = $logger->logEdit(
                $data['entityType'],
                $data['entityId'],
                $data['action'],
                $data['oldValues'] ?? null,
                $data['newValues'] ?? null,
                $data['description'] ?? null
            );
            echo json_encode($result);
        } else {
            echo json_encode(["success" => false, "message" => "Missing required parameters"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
