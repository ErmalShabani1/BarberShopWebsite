// Barber Slider Functionality
let currentSlide = 0;

function slideBarbers(direction) {
    const track = document.getElementById('barber-track');
    const cards = document.querySelectorAll('.barber-card');
    const cardWidth = cards[0].offsetWidth;
    const gap = parseFloat(getComputedStyle(track).gap);
    const totalCards = cards.length;
    
    // Calculate how many cards are visible
    const containerWidth = track.parentElement.offsetWidth;
    const visibleCards = Math.floor(containerWidth / (cardWidth + gap));
    const maxSlide = totalCards - visibleCards;
    
    currentSlide += direction;
    
    // Loop back to start/end
    if (currentSlide < 0) {
        currentSlide = maxSlide;
    } else if (currentSlide > maxSlide) {
        currentSlide = 0;
    }
    
    const offset = currentSlide * (cardWidth + gap);
    track.style.transform = `translateX(-${offset}px)`;
}

// Auto-slide every 5 seconds
setInterval(() => {
    slideBarbers(1);
}, 5000);
