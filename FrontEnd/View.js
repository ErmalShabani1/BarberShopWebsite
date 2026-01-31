// Barber Slider Functionality
let currentSlide = 0;

function toggleMobileMenu() {
    const hamburger = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('mobile-active');
}

document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const hamburger = document.getElementById('hamburger-btn');
            const navMenu = document.getElementById('nav-menu');
            hamburger.classList.remove('active');
            navMenu.classList.remove('mobile-active');
        });
    });
});

// Load barbers from database
async function loadBarbers() {
    try {
        const response = await fetch('../BackEnd/get_users.php?role=barber');
        const data = await response.json();
        
        if (data.success && data.barbers && data.barbers.length > 0) {
            const track = document.getElementById('barber-track');
            track.innerHTML = ''; // Clear existing static content
            
            data.barbers.forEach(barber => {
                const barberCard = document.createElement('div');
                barberCard.className = 'barber-card';
                const imgSrc = barber.imageUrl ? barber.imageUrl : '../images/image1.jpg';
                const desc = barber.description ? barber.description : 'Professional Barber';
                const avg = barber.avgRating ? parseFloat(barber.avgRating) : 0.0;
                const ratingCount = barber.ratingCount ? parseInt(barber.ratingCount, 10) : 0;
                const percent = (avg / 5) * 100;
                barberCard.innerHTML = `
                    <img src="${imgSrc}" alt="${barber.fullName || barber.username}">
                    <h3>${barber.fullName || barber.username}</h3>
                    <div class="rating">
                        <div class="stars-outer"><div class="stars-inner" style="width: ${percent}%"></div></div>
                        <span class="rating-value">${ratingCount === 0 ? 'New' : avg.toFixed(1)}</span>
                        <span class="rating-count">(${ratingCount})</span>
                    </div>
                    <p>${desc}</p>
                    <p>Email: ${barber.email}</p>
                `;
                track.appendChild(barberCard);
            });
            // reset slider position after loading
            currentSlide = 0;
            track.style.transform = 'translateX(0px)';
            track.style.willChange = 'transform';
            // start auto-slide if desired (ensure only one interval)
            if (!window._barberAutoSlideInterval) {
                window._barberAutoSlideInterval = setInterval(() => {
                    slideBarbers(1);
                }, 5000);
            }
        } else {
            const track = document.getElementById('barber-track');
            track.innerHTML = '<p style="color: white; text-align: center; width: 100%;">No barbers available at the moment.</p>';
        }
    } catch (error) {
        console.error('Error loading barbers:', error);
        const track = document.getElementById('barber-track');
        track.innerHTML = '<p style="color: white; text-align: center; width: 100%;">Error loading barbers. Please try again later.</p>';
    }
}

function slideBarbers(direction) {
    const track = document.getElementById('barber-track');
    const cards = document.querySelectorAll('.barber-card');
    
    if (cards.length === 0) return; // No cards to slide
    
    const cardWidth = cards[0].getBoundingClientRect().width;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;
    const totalCards = cards.length;
    
    // Calculate how many cards are visible
    const containerWidth = track.parentElement.getBoundingClientRect().width;
    const visibleCards = Math.max(1, Math.floor(containerWidth / (cardWidth + gap)));
    const maxSlide = totalCards - visibleCards;
    
    currentSlide += direction;
    
    // Loop back to start/end
    if (currentSlide < 0) {
        currentSlide = maxSlide > 0 ? maxSlide : 0;
    } else if (currentSlide > maxSlide) {
        currentSlide = 0;
    }
    
    const offset = currentSlide * (cardWidth + gap);
    track.style.transition = 'transform 400ms ease';
    track.style.transform = `translateX(-${offset}px)`;
}

// Recalculate slider on resize to avoid clipping issues
window.addEventListener('resize', () => {
    const track = document.getElementById('barber-track');
    if (!track) return;
    // reset to valid slide index
    currentSlide = 0;
    track.style.transition = 'none';
    track.style.transform = 'translateX(0px)';
});

// Load barbers when page loads
document.addEventListener('DOMContentLoaded', loadBarbers);

// Auto-slide every 5 seconds
setInterval(() => {
    slideBarbers(1);
}, 5000);

