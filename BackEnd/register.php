<?php
header('Content-Type: application/json');
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['username']) && isset($data['email']) && isset($data['password']) && isset($data['fullName'])) {
        $user = new User();
        $phone = isset($data['phone']) ? $data['phone'] : null;
        $result = $user->register($data['username'], $data['email'], $data['password'], $data['fullName'], $phone);
        echo json_encode($result);
    } else {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
