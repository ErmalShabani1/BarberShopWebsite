// Barber Slider Functionality
let currentSlide = 0;

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
                barberCard.innerHTML = `
                    <img src="../images/image1.jpg" alt="${barber.fullName || barber.username}">
                    <h3>${barber.fullName || barber.username}</h3>
                    <p>Professional Barber</p>
                    <p>Email: ${barber.email}</p>
                `;
                track.appendChild(barberCard);
            });
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
    
    const cardWidth = cards[0].offsetWidth;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;
    const totalCards = cards.length;
    
    // Calculate how many cards are visible
    const containerWidth = track.parentElement.offsetWidth;
    const visibleCards = Math.floor(containerWidth / (cardWidth + gap));
    const maxSlide = totalCards - visibleCards;
    
    currentSlide += direction;
    
    // Loop back to start/end
    if (currentSlide < 0) {
        currentSlide = maxSlide > 0 ? maxSlide : 0;
    } else if (currentSlide > maxSlide) {
        currentSlide = 0;
    }
    
    const offset = currentSlide * (cardWidth + gap);
    track.style.transform = `translateX(-${offset}px)`;
}

// Load barbers when page loads
document.addEventListener('DOMContentLoaded', loadBarbers);

// Auto-slide every 5 seconds
setInterval(() => {
    slideBarbers(1);
}, 5000);

