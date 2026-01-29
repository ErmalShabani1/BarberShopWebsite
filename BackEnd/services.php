<?php
session_start();
header('Content-Type: application/json');
require_once 'User.php';
require_once 'db_config.php';
require_once 'edit_logs.php';

// Initialize response
$response = array('success' => false, 'message' => '');

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Database connection
$db = new Database();
$conn = $db->connect();

// Handle GET requests - fetch services
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($action) {
        case 'getAll':
            // Get all active services ordered by display_order
            $stmt = $conn->prepare("SELECT id, name, description, price, duration, display_order, created_at, updated_at FROM services WHERE is_active = TRUE ORDER BY display_order ASC");
            $stmt->execute();
            $result = $stmt->get_result();
            $services = array();
            
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
            
            $stmt->close();
            $response['success'] = true;
            $response['services'] = $services;
            break;

        case 'getById':
            // Get specific service by ID
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $response['success'] = true;
                    $response['service'] = $result->fetch_assoc();
                } else {
                    $response['message'] = 'Service not found';
                }
                
                $stmt->close();
            } else {
                $response['message'] = 'Service ID is required';
            }
            break;

        default:
            $response['message'] = 'Invalid action';
    }
} 
// Handle POST requests - add, edit, delete services (admin or barber)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in and is admin or barber
    $user = new User();
    $userResult = $user->getCurrentUser();
    
    if (!$userResult['success'] || !in_array($userResult['user']['role'], ['admin', 'barber'])) {
        http_response_code(403);
        $response['message'] = 'Unauthorized. Admin or Barber access required.';
        echo json_encode($response);
        exit();
    }
    
    switch ($action) {
        case 'add':
            // Add new service
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
            $duration = isset($_POST['duration']) ? intval($_POST['duration']) : null;
            $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
            
            if (empty($name)) {
                $response['message'] = 'Service name is required';
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO services (name, description, price, duration, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdii", $name, $description, $price, $duration, $display_order);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service added successfully';
                $serviceId = $stmt->insert_id;
                $response['service_id'] = $serviceId;
                
                // Log the edit
                $logger = new EditLogger();
                $newValues = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'duration' => $duration,
                    'display_order' => $display_order
                ];
                $logger->logEdit('services', $serviceId, 'create', null, $newValues, "Created new service: $name");
            } else {
                $response['message'] = 'Failed to add service: ' . $conn->error;
            }
            
            $stmt->close();
            break;

        case 'edit':
            // Edit existing service
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
            $duration = isset($_POST['duration']) ? intval($_POST['duration']) : null;
            $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
            
            if ($id === 0 || empty($name)) {
                $response['message'] = 'Service ID and name are required';
                break;
            }
            
            // Get old values for logging
            $stmtOld = $conn->prepare("SELECT name, description, price, duration, display_order FROM services WHERE id = ?");
            $stmtOld->bind_param("i", $id);
            $stmtOld->execute();
            $resultOld = $stmtOld->get_result();
            $oldService = $resultOld->fetch_assoc();
            $stmtOld->close();
            
            // Get current username
            $editedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'unknown';
            
            $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration = ?, display_order = ?, edited_by = ? WHERE id = ?");
            $stmt->bind_param("ssdissi", $name, $description, $price, $duration, $display_order, $editedBy, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service updated successfully';
                
                // Log the edit
                $logger = new EditLogger();
                $newValues = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'duration' => $duration,
                    'display_order' => $display_order
                ];
                $changes = [];
                if ($oldService['name'] !== $name) $changes[] = "name: '{$oldService['name']}' → '$name'";
                if ($oldService['description'] !== $description) $changes[] = "description updated";
                if ($oldService['price'] != $price) $changes[] = "price: {$oldService['price']} → $price";
                if ($oldService['duration'] != $duration) $changes[] = "duration: {$oldService['duration']} → $duration";
                if ($oldService['display_order'] != $display_order) $changes[] = "display_order: {$oldService['display_order']} → $display_order";
                
                $changeDesc = "Updated service: " . implode(", ", $changes);
                $logger->logEdit('services', $id, 'update', $oldService, $newValues, $changeDesc);
            } else {
                $response['message'] = 'Failed to update service: ' . $conn->error;
            }
            
            $stmt->close();
            break;

        case 'delete':
            // Delete service (soft delete by setting is_active to FALSE)
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            if ($id === 0) {
                $response['message'] = 'Service ID is required';
                break;
            }
            
            // Get service name for logging
            $stmtName = $conn->prepare("SELECT name FROM services WHERE id = ?");
            $stmtName->bind_param("i", $id);
            $stmtName->execute();
            $resultName = $stmtName->get_result();
            $serviceRow = $resultName->fetch_assoc();
            $serviceName = $serviceRow['name'] ?? 'Unknown';
            $stmtName->close();
            
            $stmt = $conn->prepare("UPDATE services SET is_active = FALSE WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service deleted successfully';
                
                // Log the edit
                $logger = new EditLogger();
                $logger->logEdit('services', $id, 'delete', null, null, "Deleted service: $serviceName");
            } else {
                $response['message'] = 'Failed to delete service: ' . $conn->error;
            }
            
            $stmt->close();
            break;

        default:
            $response['message'] = 'Invalid action';
    }
}

$db->close();
echo json_encode($response);
?>
