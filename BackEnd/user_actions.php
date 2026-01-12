<?php
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
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
