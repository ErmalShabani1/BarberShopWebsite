<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>View Barbers</title>
    <link rel="stylesheet" href="View.css">
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
    <header class="main-header">
        <div class="header-content">
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
            <h1 class="brand-title">BARBERSHOP</h1>
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
        </div>
    </header>

     <nav class="main-nav">
        <button class="hamburger-menu" id="hamburger-btn" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-menu" id="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="Booking.php" class="nav-link">Booking</a></li>
            <li><a href="View.php" class="nav-link active">View Barbers</a></li>
            <li><a href="kontakti.php" class="nav-link">Contact</a></li>
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'barber')): ?>
            <li><a href="edit.php" class="nav-link">Edit / Cancel</a></li>
            <?php endif; ?>
        </ul>
    </nav>

     <section class="barber-view-container">
        <h2 class="section-title">Our Expert Barbers</h2>
        <div class="barber-slider">
            <button class="slider-btn prev" onclick="slideBarbers(-1)">‹</button>
            <div class="barber-track" id="barber-track">
                <!-- Barbers will be loaded dynamically from database -->
            </div>
            <button class="slider-btn next" onclick="slideBarbers(1)">›</button>
        </div>
    </section>

    <footer class="main-footer">
        <p>Copyright © 2025 Barbershop. All rights reserved.</p>
    </footer>

    <script src="login.js"></script>
    <script src="View.js"></script>
</body>
</html>