<?php
header('Content-Type: application/json');
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['username']) && isset($data['password'])) {
        $user = new User();
        $result = $user->login($data['username'], $data['password']);
        echo json_encode($result);
    } else {
        echo json_encode(["success" => false, "message" => "Username and password required"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
