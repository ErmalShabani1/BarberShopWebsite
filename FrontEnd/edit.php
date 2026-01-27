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
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
            <h1 class="brand-title">BARBERSHOP</h1>
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
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
            
            <!-- Services Management Section -->
            <div class="services-management">
                <h3>Services Management</h3>
                
                <!-- Service Form -->
                <div class="add-service-form">
                    <h4 id="service-form-title">Add New Service</h4>

                    <form id="add-service-form">
                        <input type="hidden" id="service-id" value="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="service-name">Service Name</label>
                                <select id="service-name-selector" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; width: 100%; margin-bottom: 8px;">
                                    <option value="">-- Add New Service --</option>
                                </select>
                                <input type="text" id="service-name" placeholder="Enter service name" required>
                            </div>
                            <div class="form-group">
                                <label for="service-price">Price ($)</label>
                                <input type="number" id="service-price" step="0.01" min="0" required>
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
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="submit-btn" id="service-submit-btn">Add Service</button>
                            <button type="button" class="cancel-btn" id="service-cancel-btn" style="display: none;">Cancel Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Admin Panel - Services Management -->
        <div id="admin-services-panel" class="role-panel" style="display: none;">
            <h3>Services Management</h3>
            
            <!-- Service Form -->
            <div class="add-service-form">
                <h4 id="admin-service-form-title">Add New Service</h4>

                <form id="admin-service-form">
                    <input type="hidden" id="admin-service-id" value="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="admin-service-name">Service Name</label>
                            <select id="admin-service-name-selector" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; width: 100%; margin-bottom: 8px;">
                                <option value="">-- Add New Service --</option>
                            </select>
                            <input type="text" id="admin-service-name" placeholder="Enter service name" required>
                        </div>
                        <div class="form-group">
                            <label for="admin-service-price">Price ($)</label>
                            <input type="number" id="admin-service-price" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="admin-service-duration">Duration (minutes)</label>
                            <input type="number" id="admin-service-duration" min="5" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="admin-service-description">Description</label>
                        <textarea id="admin-service-description" rows="3" required></textarea>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="submit-btn" id="admin-service-submit-btn">Add Service</button>
                        <button type="button" class="cancel-btn" id="admin-service-cancel-btn" style="display: none;">Cancel Edit</button>
                    </div>
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

    <script src="login.js?v=<?php echo time(); ?>"></script>
    <script src="edit.js?v=<?php echo time(); ?>"></script>
</body>
</html>
