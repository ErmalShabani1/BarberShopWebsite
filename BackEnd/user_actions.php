<?php
session_start();
header('Content-Type: application/json');
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        $user = new User();
        
        switch ($_GET['action']) {
            case 'getCurrentUser':
                $result = $user->getCurrentUser();
                echo json_encode($result);
                break;
                
            case 'logout':
                $result = $user->logout();
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No action specified"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        $user = new User();
        
        switch ($data['action']) {
            case 'updateRole':
                if (isset($data['username']) && isset($data['role'])) {
                    $result = $user->updateUserRole($data['username'], $data['role']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Missing parameters"]);
                }
                break;
                
            case 'deleteUser':
                if (isset($data['username'])) {
                    $result = $user->deleteUser($data['username']);
                    echo json_encode($result);
                } else {
                    echo json_encode(["success" => false, "message" => "Missing username"]);
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
