<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Slider</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .services-section {
            width: 100%;
            max-width: 1200px;
            background-color: white;
            border-radius: 15px;
            padding: 50px 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .services-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .services-header h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 15px;
        }

        .services-header p {
            font-size: 16px;
            color: #666;
        }

        .services-slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .services-slider {
            display: flex;
            transition: transform 0.5s ease-in-out;
            gap: 20px;
        }

        .service-card {
            min-width: calc(33.333% - 14px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 280px;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .service-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: inline-block;
        }

        .service-name {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .service-description {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 15px;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-price {
            font-size: 18px;
            font-weight: bold;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .service-duration {
            font-size: 12px;
            opacity: 0.7;
        }

        /* Slider Controls */
        .slider-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }

        .slider-btn {
            background-color: #667eea;
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slider-btn:hover {
            background-color: #764ba2;
        }

        .slider-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .slider-dots {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ddd;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dot.active {
            background-color: #667eea;
        }

        .loading {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #666;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .service-card {
                min-width: calc(50% - 10px);
            }
        }

        @media (max-width: 768px) {
            .services-header h2 {
                font-size: 28px;
            }

            .service-card {
                min-width: 100%;
                min-height: 250px;
            }

            .slider-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .services-section {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="services-section">
        <div class="services-header">
            <h2>Our Services</h2>
            <p>Explore our premium barbershop services</p>
        </div>

        <div class="services-slider-container">
            <div class="services-slider" id="servicesSlider">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading services...</p>
                </div>
            </div>
        </div>

        <div class="slider-controls">
            <button class="slider-btn" id="prevBtn" onclick="previousSlide()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-dots" id="sliderDots"></div>
            <button class="slider-btn" id="nextBtn" onclick="nextSlide()">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <script>
        const apiUrl = '../BackEnd/services.php';
        let services = [];
        let currentSlide = 0;
        let itemsPerView = 3;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadServices();
            updateItemsPerView();
            window.addEventListener('resize', updateItemsPerView);
        });

        // Load services from backend
        function loadServices() {
            fetch(`${apiUrl}?action=getAll`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.services) {
                        services = data.services;
                        renderServices();
                        createDots();
                        updateSliderPosition();
                    } else {
                        document.getElementById('servicesSlider').innerHTML = '<p>No services available</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading services:', error);
                    document.getElementById('servicesSlider').innerHTML = '<p>Error loading services</p>';
                });
        }

        // Render service cards
        function renderServices() {
            const slider = document.getElementById('servicesSlider');
            slider.innerHTML = '';

            services.forEach((service, index) => {
                const card = document.createElement('div');
                card.className = 'service-card';
                
                let iconHtml = '';
                if (service.icon) {
                    iconHtml = `<div class="service-icon"><i class="fas ${service.icon}"></i></div>`;
                } else if (service.image_url) {
                    iconHtml = `<div class="service-icon"><img src="${service.image_url}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;"></div>`;
                } else {
                    iconHtml = `<div class="service-icon"><i class="fas fa-cut"></i></div>`;
                }

                card.innerHTML = `
                    ${iconHtml}
                    <div class="service-name">${service.name}</div>
                    ${service.description ? `<div class="service-description">${service.description}</div>` : '<div class="service-description"></div>'}
                    ${service.price ? `<div class="service-price">$${parseFloat(service.price).toFixed(2)}</div>` : ''}
                    ${service.duration ? `<div class="service-duration">${service.duration} minutes</div>` : ''}
                `;
                
                slider.appendChild(card);
            });
        }

        // Create pagination dots
        function createDots() {
            const dotsContainer = document.getElementById('sliderDots');
            dotsContainer.innerHTML = '';
            
            const totalPages = Math.ceil(services.length / itemsPerView);
            
            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('div');
                dot.className = 'dot';
                if (i === 0) dot.classList.add('active');
                dot.onclick = () => goToSlide(i);
                dotsContainer.appendChild(dot);
            }
        }

        // Update items per view based on screen size
        function updateItemsPerView() {
            if (window.innerWidth <= 768) {
                itemsPerView = 1;
            } else if (window.innerWidth <= 1024) {
                itemsPerView = 2;
            } else {
                itemsPerView = 3;
            }
            updateSliderPosition();
        }

        // Update slider position
        function updateSliderPosition() {
            const slider = document.getElementById('servicesSlider');
            const itemWidth = 100 / itemsPerView;
            const translateX = -(currentSlide * itemWidth);
            slider.style.transform = `translateX(${translateX}%)`;
            
            updateDots();
            updateButtonStates();
        }

        // Update dots active state
        function updateDots() {
            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                dot.classList.remove('active');
                if (index === currentSlide) {
                    dot.classList.add('active');
                }
            });
        }

        // Update button states
        function updateButtonStates() {
            const totalPages = Math.ceil(services.length / itemsPerView);
            document.getElementById('prevBtn').disabled = currentSlide === 0;
            document.getElementById('nextBtn').disabled = currentSlide >= totalPages - 1;
        }

        // Next slide
        function nextSlide() {
            const totalPages = Math.ceil(services.length / itemsPerView);
            if (currentSlide < totalPages - 1) {
                currentSlide++;
                updateSliderPosition();
            }
        }

        // Previous slide
        function previousSlide() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSliderPosition();
            }
        }

        // Go to specific slide
        function goToSlide(index) {
            currentSlide = index;
            updateSliderPosition();
        }
    </script>
</body>
</html>
