<?php

class DatabaseSetup {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "barbershopdb";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function createDatabase() {
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->database;
        
        if ($this->conn->query($sql) === TRUE) {
            echo "✓ Database '{$this->database}' created successfully<br>";
            $this->conn->select_db($this->database);
        } else {
            die("Error creating database: " . $this->conn->error);
        }
    }

    public function createTables() {
        // Users table
        $usersTable = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role ENUM('client', 'admin', 'barber') DEFAULT 'client',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        // Appointments table
        $appointmentsTable = "CREATE TABLE IF NOT EXISTS appointments (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            service_type VARCHAR(100) NOT NULL,
            appointment_date DATE NOT NULL,
            appointment_time TIME NOT NULL,
            status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        // Messages table
        $messagesTable = "CREATE TABLE IF NOT EXISTS messages (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11),
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(200),
            message TEXT NOT NULL,
            status ENUM('unread', 'read') DEFAULT 'unread',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        // Execute table creation
        if ($this->conn->query($usersTable) === TRUE) {
            echo "✓ Users table created<br>";
        } else {
            echo "Error: " . $this->conn->error . "<br>";
        }

        if ($this->conn->query($appointmentsTable) === TRUE) {
            echo "✓ Appointments table created<br>";
        } else {
            echo "Error: " . $this->conn->error . "<br>";
        }

        if ($this->conn->query($messagesTable) === TRUE) {
            echo "✓ Messages table created<br>";
        } else {
            echo "Error: " . $this->conn->error . "<br>";
        }
    }

    public function setup() {
        echo "<h2>Tables Setup</h2>";
        $this->createTables();
        echo "<br><h3>✓ Tables Created Successfully!</h3>";
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Run the setup
$setup = new DatabaseSetup();
$setup->setup();

?>
