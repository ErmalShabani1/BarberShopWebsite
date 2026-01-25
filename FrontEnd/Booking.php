<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Booking.css">
    <title>Book Appointment - Barbershop</title>
</head>
<body>
    <!-- Pjesa e Headerit -->
    <header class="main-header">
        <div class="header-content">
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
            <h1 class="brand-title">BARBERSHOP</h1>
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
        </div>
    </header>

    <!-- navigimi -->
    <nav class="main-nav">
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="Booking.php" class="nav-link active">Booking</a></li>
            <li><a href="View.php" class="nav-link">View Barbers</a></li>
            <li><a href="kontakti.php" class="nav-link">Contact</a></li>
            <li><a href="edit.php" class="nav-link">Edit / Cancel</a></li>
            <li><button id="auth-btn" class="nav-link auth-btn" onclick="openLoginModal()">Login</button></li>
        </ul>
    </nav>

    <!-- modaliteti loginit -->
    <div id="login-modal" class="login-modal">
        <div class="login-modal-content">
            <button class="close-modal" onclick="closeLoginModal()">&times;</button>
            
            <!-- pjesa login -->
            <div id="login-form-container" class="auth-form-container">
                <h2>Login to Book Appointment</h2>
                <form id="login-form">
                    <div class="login-form-group">
                        <label for="modal-username">Username</label>
                        <input type="text" id="modal-username" name="username" required>
                    </div>
                    <div class="login-form-group">
                        <label for="modal-password">Password</label>
                        <input type="password" id="modal-password" name="password" required>
                    </div>
                    <button type="submit" class="login-btn">Login</button>
                    <div class="modal-error-message" id="modal-error-message"></div>
                    <div class="modal-success-message" id="modal-success-message"></div>
                </form>
                <p class="toggle-form">Don't have an account? <a href="#" onclick="showRegisterForm(); return false;">Register</a></p>

                <!-- logina demo -->
                <div class="demo-credentials">
                    <h4>Demo Logins:</h4>
                    <p><strong>Admin:</strong> admin / admin123</p>
                    <p><strong>Barber:</strong> barber / barber123</p>
                    <p><strong>User:</strong> user / user123</p>
                </div>
            </div>

            <!-- regjistrohu -->
            <div id="register-form-container" class="auth-form-container" style="display: none;">
                <h2>Register</h2>
                <form id="register-form">
                    <div class="login-form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" name="username" required>
                    </div>
                    <div class="login-form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="login-form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="login-form-group">
                        <label for="register-confirm-password">Confirm Password</label>
                        <input type="password" id="register-confirm-password" name="confirm-password" required>
                    </div>
                    <button type="submit" class="login-btn">Register</button>
                    <div class="modal-error-message" id="register-error-message"></div>
                    <div class="modal-success-message" id="register-success-message"></div>
                </form>
                <p class="toggle-form">Already have an account? <a href="#" onclick="showLoginForm(); return false;">Login</a></p>
            </div>
        </div>
    </div>

    <!-- pjesa e booking -->
    <section class="booking-container">
        <h2 class="booking-title">Book Your Appointment</h2>

        <div class="error-message" id="error-message"></div>
        <div class="success-message" id="success-message"></div>

        <!-- zgjedhja berberit -->
        <div class="booking-step">
            <h3 class="step-title">Step 1: Choose Your Barber</h3>
            <div class="barber-slider">
                <button class="slider-btn prev" onclick="slideBarbers(-1)">‹</button>
                <div class="barber-track" id="barber-track">
                    <div class="barber-card" data-barber-id="1" onclick="selectBarber(1)">
                        <img src="../images/image1.jpg" alt="Barber 1">
                        <h3>John Smith</h3>
                        <p>Expert in Fades & Classic Cuts</p>
                        <p>⭐ 4.9 (150 reviews)</p>
                    </div>
                    <div class="barber-card" data-barber-id="2" onclick="selectBarber(2)">
                        <img src="../images/image1.jpg" alt="Barber 2">
                        <h3>Mike Johnson</h3>
                        <p>Specialist in Modern Styles</p>
                        <p>⭐ 4.8 (120 reviews)</p>
                    </div>
                    <div class="barber-card" data-barber-id="3" onclick="selectBarber(3)">
                        <img src="../images/image1.jpg" alt="Barber 3">
                        <h3>David Brown</h3>
                        <p>Master of Beard Grooming</p>
                        <p>⭐ 4.9 (180 reviews)</p>
                    </div>
                    <div class="barber-card" data-barber-id="4" onclick="selectBarber(4)">
                        <img src="../images/image1.jpg" alt="Barber 4">
                        <h3>Chris Wilson</h3>
                        <p>Contemporary Hair Styling Expert</p>
                        <p>⭐ 4.7 (95 reviews)</p>
                    </div>
                </div>
                <button class="slider-btn next" onclick="slideBarbers(1)">›</button>
            </div>
        </div>

        <!-- zgjedhja sherbimit -->
        <div class="booking-step">
            <h3 class="step-title">Step 2: Choose Your Service</h3>
            <div class="service-grid">
                <div class="service-option" data-service-id="1" data-price="20" data-duration="30" onclick="selectService(1)">
                    <h3>High Fade</h3>
                    <p class="price">$20</p>
                    <p class="duration">⏱ 30 minutes</p>
                </div>
                <div class="service-option" data-service-id="2" data-price="15" data-duration="25" onclick="selectService(2)">
                    <h3>Low Fade</h3>
                    <p class="price">$15</p>
                    <p class="duration">⏱ 25 minutes</p>
                </div>
                <div class="service-option" data-service-id="3" data-price="17" data-duration="25" onclick="selectService(3)">
                    <h3>Mid Fade</h3>
                    <p class="price">$17</p>
                    <p class="duration">⏱ 25 minutes</p>
                </div>
                <div class="service-option" data-service-id="4" data-price="18" data-duration="28" onclick="selectService(4)">
                    <h3>Taper Fade</h3>
                    <p class="price">$18</p>
                    <p class="duration">⏱ 28 minutes</p>
                </div>
            </div>
        </div>

        <!-- Zgjedhja e dates dhe orarit -->
        <div class="booking-step">
            <h3 class="step-title">Step 3: Select Date & Time</h3>
            <div class="datetime-grid">
                <div class="form-group">
                    <label for="booking-date">Date</label>
                    <input type="date" id="booking-date" required onchange="loadTimeSlots()">
                </div>
                <div class="form-group">
                    <label>Available Time Slots</label>
                    <div class="time-slots" id="time-slots">
                        <p style="grid-column: 1/-1; text-align: center; color: #999;">Please select a date first</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faturimi -->
        <div class="booking-summary">
            <h3>Booking Summary</h3>
            <div class="summary-item">
                <span>Barber:</span>
                <span id="summary-barber">Not selected</span>
            </div>
            <div class="summary-item">
                <span>Service:</span>
                <span id="summary-service">Not selected</span>
            </div>
            <div class="summary-item">
                <span>Date:</span>
                <span id="summary-date">Not selected</span>
            </div>
            <div class="summary-item">
                <span>Time:</span>
                <span id="summary-time">Not selected</span>
            </div>
            <div class="summary-item">
                <span>Total:</span>
                <span id="summary-total">$0</span>
            </div>
        </div>

        <button class="submit-booking" id="submit-booking" disabled onclick="submitBooking()">
            Confirm Booking
        </button>
    </section>

    <!-- Footeri -->
    <footer class="main-footer">
        <p>Copyright © 2025 Barbershop. All rights reserved.</p>
    </footer>

    <script src="login.js"></script>
    <script src="booking.js"></script>
</body>
</html>
