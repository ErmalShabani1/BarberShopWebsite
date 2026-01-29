<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">	<link rel="stylesheet" href="kontakti.css">
	<title>Contact - Barbershop</title>
</head>
<body>
    <script>
        // Force reload on back/forward navigation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (performance.navigation && performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
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
			<li><a href="kontakti.php" class="nav-link active">Contact</a></li>
			<li><a href="edit.php" class="nav-link">Edit / Cancel</a></li>
			<li><button id="auth-btn" class="nav-link auth-btn" onclick="openLoginModal()">Login</button></li>
		</ul>
	</nav>

	<!-- Login/Register Modal -->
	<div id="login-modal" class="login-modal">
		<div class="login-modal-content">
			<button class="close-modal" onclick="closeLoginModal()">&times;</button>
			
			<!-- Login Form -->
			<div id="login-form-container" class="auth-form-container">
				<h2>Login</h2>
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
				
				<!-- Demo Credentials -->
				<div class="demo-credentials">
					<h4>Demo Logins:</h4>
					<p><strong>Admin:</strong> admin / admin123</p>
					<p><strong>Barber:</strong> barber / barber123</p>
					<p><strong>User:</strong> user / user123</p>
				</div>
			</div>

			<!-- Register Form -->
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

	<!-- Contact Section -->
	<section class="contact-container">
		<h2 class="contact-title">Contact Us</h2>

		<div class="contact-grid">
			<!-- Contact Information -->
			<div class="contact-info">
				<h3>Get in Touch</h3>
				<div class="info-item">
					<div>
						<h4>Address</h4>
						<p>123 Barbershop Street<br>Prishtina, Kosovo</p>
					</div>
				</div>
				<div class="info-item">
					<div>
						<h4>Phone</h4>
						<p>+383 44 123 456</p>
					</div>
				</div>
				<div class="info-item">
					<div>
						<h4>Email</h4>
						<p>info@barbershop.com</p>
					</div>
				</div>
				<div class="info-item">
					<div>
						<h4>Working Hours</h4>
						<p>Monday - Friday: 9:00 AM - 8:00 PM<br>
						Saturday: 10:00 AM - 6:00 PM<br>
						Sunday: Closed</p>
					</div>
				</div>
			</div>

			<!-- Contact Form -->
			<div class="contact-form-section">
				<h3>Send us a Message</h3>
				<form class="contact-form" onsubmit="handleContactForm(event)">
					<div class="form-group">
						<label for="name">Name</label>
						<input type="text" id="name" name="name" required>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" id="email" name="email" required>
					</div>
					<div class="form-group">
						<label for="subject">Subject</label>
						<input type="text" id="subject" name="subject" required>
					</div>
					<div class="form-group">
						<label for="message">Message</label>
						<textarea id="message" name="message" required></textarea>
					</div>
					<button type="submit" class="submit-btn">Send Message</button>
				</form>
			</div>
		</div>

		<!-- Map Section -->
		<div class="map-section">
			<h3>Find Us</h3>
			<div class="map-container">
				<iframe 
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2934.6357!2d21.1655!3d42.6629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDLCsDM5JzQ2LjQiTiAyMcKwMDknNTUuOCJF!5e0!3m2!1sen!2s!4v1234567890"
					width="100%" 
					height="450" 
					style="border:0; border-radius: 15px;" 
					allowfullscreen="" 
					loading="lazy">
				</iframe>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="main-footer">
		<p>Copyright © 2025 Barbershop. All rights reserved.</p>
	</footer>

	<script src="login.js"></script>
	<script src="kontakti.js"></script>
</body>
</html>