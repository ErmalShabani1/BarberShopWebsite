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

        // Services table
        $servicesTable = "CREATE TABLE IF NOT EXISTS services (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2),
            duration INT(11) COMMENT 'Duration in minutes',
            icon VARCHAR(255) COMMENT 'Icon class or image path',
            image_url VARCHAR(255),
            display_order INT(11) DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            edited_by VARCHAR(100) DEFAULT NULL COMMENT 'Username of last editor',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

        if ($this->conn->query($servicesTable) === TRUE) {
            echo "✓ Services table created<br>";
        } else {
            echo "Error: " . $this->conn->error . "<br>";
        }

        // Edit Logs table
        $editLogsTable = "CREATE TABLE IF NOT EXISTS edit_logs (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            edited_by VARCHAR(100) NOT NULL,
            entity_type VARCHAR(50) NOT NULL COMMENT 'services, appointments, users, etc.',
            entity_id INT(11) NOT NULL,
            action VARCHAR(50) NOT NULL COMMENT 'create, update, delete',
            old_values JSON,
            new_values JSON,
            change_description TEXT,
            ip_address VARCHAR(45),
            user_agent VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX (entity_type, entity_id),
            INDEX (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        if ($this->conn->query($editLogsTable) === TRUE) {
            echo "✓ Edit Logs table created<br>";
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
