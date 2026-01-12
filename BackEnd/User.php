<?php
require_once 'db_config.php';

class User {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    public function register($username, $email, $password, $fullName, $phone = null) {
        // Check if username exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return ["success" => false, "message" => "Username already exists"];
        }
        $stmt->close();

        // Check if email exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            return ["success" => false, "message" => "Email already exists"];
        }
        $stmt->close();

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashedPassword, $fullName, $phone);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "Registration successful"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Registration failed"];
        }
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, email, password, full_name, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Start session and store user data
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                $stmt->close();
                return [
                    "success" => true,
                    "user" => [
                        "id" => $user['id'],
                        "username" => $user['username'],
                        "email" => $user['email'],
                        "fullName" => $user['full_name'],
                        "role" => $user['role']
                    ]
                ];
            }
        }
        
        $stmt->close();
        return ["success" => false, "message" => "Invalid username or password"];
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return ["success" => true, "message" => "Logged out successfully"];
    }

    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            return [
                "success" => true,
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "username" => $_SESSION['username'],
                    "role" => $_SESSION['role'],
                    "fullName" => $_SESSION['full_name']
                ]
            ];
        }
        
        return ["success" => false, "message" => "Not logged in"];
    }

    public function __destruct() {
        $this->db->close();
    }
}
?>
