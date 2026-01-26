<?php
header('Content-Type: application/json');
require_once 'User.php';
require_once 'db_config.php';

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
            $stmt = $conn->prepare("SELECT id, name, description, price, duration, icon, image_url, display_order FROM services WHERE is_active = TRUE ORDER BY display_order ASC");
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
// Handle POST requests - add, edit, delete services (admin only)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in and is admin
    $user = new User();
    $userResult = $user->getCurrentUser();
    
    if (!$userResult['success'] || $userResult['user']['role'] !== 'admin') {
        http_response_code(403);
        $response['message'] = 'Unauthorized. Admin access required.';
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
            $icon = isset($_POST['icon']) ? trim($_POST['icon']) : '';
            $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
            $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
            
            if (empty($name)) {
                $response['message'] = 'Service name is required';
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO services (name, description, price, duration, icon, image_url, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiiss", $name, $description, $price, $duration, $icon, $image_url, $display_order);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service added successfully';
                $response['service_id'] = $stmt->insert_id;
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
            $icon = isset($_POST['icon']) ? trim($_POST['icon']) : '';
            $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
            $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
            
            if ($id === 0 || empty($name)) {
                $response['message'] = 'Service ID and name are required';
                break;
            }
            
            $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration = ?, icon = ?, image_url = ?, display_order = ? WHERE id = ?");
            $stmt->bind_param("ssdiissi", $name, $description, $price, $duration, $icon, $image_url, $display_order, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service updated successfully';
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
            
            $stmt = $conn->prepare("UPDATE services SET is_active = FALSE WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Service deleted successfully';
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
