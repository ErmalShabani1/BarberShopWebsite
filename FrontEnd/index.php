<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="styles.css">
	<title>Barbershop - Professional Grooming Services</title>
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
			<li><a href="index.php" class="nav-link active">Home</a></li>
			<li><a href="Booking.php" class="nav-link">Booking</a></li>
			<li><a href="View.php" class="nav-link">View Barbers</a></li>
			<li><a href="kontakti.php" class="nav-link">Contact</a></li>
			<li><a href="edit.php" class="nav-link">Edit / Cancel</a></li>
		</ul>
	</nav>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-content">
			<h2>Welcome to Premium Grooming</h2>
			<p>Where Style Meets Professionalism</p>
			<a href="Booking.php" class="cta-button">Book Now</a>
		</div>
	</section>

	<!-- Services Overview Section -->
	<section class="services-overview">
		<h2 class="section-title">Our Styles</h2>
		<div class="styles-grid" id="styles-grid">
			<!-- Styles will be loaded dynamically from database -->
		</div>
	</section>

	<!-- Services Detail Section -->
	<section class="services-detail">
		<h2 class="section-title">Services & Pricing</h2>
		<div class="service-cards" id="service-cards">
			<!-- Services will be loaded dynamically from database -->
		</div>
	</section>

	<!-- About Us Section -->
	<section class="about-section">
		<h2 class="section-title">About Us</h2>
		<div class="about-content">
			<p>At our barbershop, grooming is more than just a service – it's an experience. We are a modern barbershop built on passion for style, precision, and respect for every client who walks through our doors.</p>
			<p>With a team of skilled and experienced barbers, we offer classic cuts, modern fades, beard shaping, and personalized grooming services tailored to every style and preference.</p>
			<p>Welcome to the place where style meets professionalism!</p>
		</div>
	</section>

	<!-- Footer -->
	<footer class="main-footer">
		<p>Copyright © 2025 Barbershop. All rights reserved.</p>
	</footer>

	<script src="login.js"></script>
	<script>
		// Load services from database
		async function loadServices() {
			try {
				const response = await fetch('../BackEnd/services.php?action=getAll');
				const data = await response.json();
				
				if (data.success && data.services) {
					displayServicesOverview(data.services);
					displayServicesDetail(data.services);
				}
			} catch (error) {
				console.error('Error loading services:', error);
			}
		}

		function displayServicesOverview(services) {
			const container = document.getElementById('styles-grid');
			const images = ['image3.jpg', 'image2.jpg', 'image4.jpg', 'image5.jpg'];
			
			container.innerHTML = services.map((service, index) => `
				<div class="style-card">
					<img src="../images/${images[index % images.length]}" alt="${service.name}">
					<h3>${service.name}</h3>
				</div>
			`).join('');
		}

		function displayServicesDetail(services) {
			const container = document.getElementById('service-cards');
			
			container.innerHTML = services.map(service => `
				<div class="service-card">
					<h3>${service.name}</h3>
					<p class="price">$${parseFloat(service.price).toFixed(2)}</p>
					<p class="description">${service.description}</p>
					<p class="duration">⏱ ${service.duration} minutes</p>
				</div>
			`).join('');
		}

		// Load services when page loads
		document.addEventListener('DOMContentLoaded', loadServices);
	</script>
</body>
</html>
