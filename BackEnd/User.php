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

        // Check if phone exists (must be unique)
        if ($phone !== null && $phone !== '') {
            // normalize phone to digits only and validate length
            $phoneDigits = preg_replace('/\D+/', '', $phone);
            if (strlen($phoneDigits) < 9) {
                return ["success" => false, "message" => "Phone number must be at least 9 digits"];
            }

            $stmt = $this->conn->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $stmt->close();
                return ["success" => false, "message" => "Phone number already exists"];
            }
            $stmt->close();
        }

        // Insert new user (no password hashing)
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password, $fullName, $phone);
        
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
            
            // Direct password comparison (no hashing)
            if ($password === $user['password']) {
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
        // Session already started in user_actions.php
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
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

    public function updateUserRole($username, $newRole) {
        // Check if user is admin
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

        // Validate role
        $validRoles = ['client', 'barber', 'admin'];
        if (!in_array($newRole, $validRoles)) {
            return ["success" => false, "message" => "Invalid role"];
        }

        $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE username = ?");
        $stmt->bind_param("ss", $newRole, $username);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "Role updated successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to update role"];
        }
    }

    public function deleteUser($username) {
        // Check if user is admin
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            return ["success" => false, "message" => "Unauthorized"];
        }

        // Prevent deleting yourself
        if ($_SESSION['username'] === $username) {
            return ["success" => false, "message" => "Cannot delete your own account"];
        }

        $stmt = $this->conn->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ["success" => true, "message" => "User deleted successfully"];
        } else {
            $stmt->close();
            return ["success" => false, "message" => "Failed to delete user"];
        }
    }

    public function __destruct() {
        $this->db->close();
    }
}