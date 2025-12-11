let track = document.getElementById('barber-track');
let position = 0;

function slideBarbers(direction) {
    const cards = document.querySelectorAll('.barber-card');
    const cardWidth = cards[0].offsetWidth + 16; // including gap approx
    const trackWidth = track.offsetWidth;
    const visibleCards = Math.floor(trackWidth / cardWidth);
    const maxPosition = -(cards.length - visibleCards) * cardWidth;

    position += direction * cardWidth;

    if(position > 0) position = 0;
    if(position < maxPosition) position = maxPosition;

    track.style.transform = `translateX(${position}px)`;
}
