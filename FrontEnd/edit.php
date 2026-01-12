<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit.css">
    <title>Edit / Cancel - Barbershop</title>
</head>
<body>
    <!-- Header Section -->
    <header class="main-header">
        <div class="header-content">
            <img src="C:\xampp\htdocs\BarberShopWebsite\Dizajn-ZhvillimWeb\images/image1.jpg" class="logo" alt="Barbershop Logo">
            <h1 class="brand-title">BARBERSHOP</h1>
            <img src="C:\xampp\htdocs\BarberShopWebsite\Dizajn-ZhvillimWeb\images/image1.jpg" class="logo" alt="Barbershop Logo">
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav">
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="Booking.php" class="nav-link">Booking</a></li>
            <li><a href="View.php" class="nav-link">View Barbers</a></li>
            <li><a href="kontakti.php" class="nav-link">Contact</a></li>
            <li><a href="edit.php" class="nav-link active">Edit / Cancel</a></li>
        </ul>
    </nav>

    <!-- Edit/Cancel Section -->
    <section class="edit-container">
        <h2 class="edit-title">Manage Appointments</h2>

        <!-- Admin Panel -->
        <div id="admin-panel" class="role-panel">
            <h3>Admin Dashboard - All Appointments</h3>
            <div id="admin-bookings"></div>

            <!-- User Management Dashboard -->
            <div class="user-dashboard">
                <h3>User Management Dashboard</h3>
                <div id="admin-users"></div>
            </div>
        </div>

        <!-- Barber Panel -->
        <div id="barber-panel" class="role-panel">
            <h3>Barber Dashboard</h3>
            <div id="barber-bookings"></div>

            <!-- User Dashboard - View Only -->
            <div class="user-dashboard">
                <h3>Users Dashboard (View Only)</h3>
                <div id="barber-users"></div>
            </div>
            
            <!-- Add Service Form -->
            <div class="add-service-form">
                <h3>Add New Service</h3>
                <form id="add-service-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="service-name">Service Name</label>
                            <input type="text" id="service-name" required>
                        </div>
                        <div class="form-group">
                            <label for="service-price">Price ($)</label>
                            <input type="number" id="service-price" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="service-duration">Duration (minutes)</label>
                            <input type="number" id="service-duration" min="5" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="service-description">Description</label>
                        <textarea id="service-description" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Add Service</button>
                </form>
            </div>
        </div>

        <!-- User Panel -->
        <div id="user-panel" class="role-panel">
            <h3>My Appointments</h3>
            <div id="user-bookings"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <p>Copyright © 2025 Barbershop. All rights reserved.</p>
    </footer>

    <script src="login.js"></script>
    <script src="edit.js"></script>
</body>
</html>
