console.log('Booking.js loaded');


function toggleMobileMenu() {
    const hamburger = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('mobile-active');
}

document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-menu a, .nav-menu button');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            const hamburger = document.getElementById('hamburger-btn');
            const navMenu = document.getElementById('nav-menu');
            hamburger.classList.remove('active');
            navMenu.classList.remove('mobile-active');
        });
    });
});

// ===== LOGIN MODAL FUNCTIONS =====
function openLoginModal() {
    console.log('openLoginModal called');
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.style.display = 'flex';
        console.log('Modal opened');
    } else {
        console.error('Modal not found');
    }
    showLoginForm();
}

function closeLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.style.display = 'none';
    }
    clearModalForms();
    
    // Show error message if user closes without logging in
    if (!window.authSystem || !window.authSystem.isLoggedIn()) {
        showLoginRequiredMessage();
    }
}

function showLoginForm() {
    const loginContainer = document.getElementById('login-form-container');
    const registerContainer = document.getElementById('register-form-container');
    if (loginContainer) loginContainer.style.display = 'block';
    if (registerContainer) registerContainer.style.display = 'none';
}

function showRegisterForm() {
    const loginContainer = document.getElementById('login-form-container');
    const registerContainer = document.getElementById('register-form-container');
    if (loginContainer) loginContainer.style.display = 'none';
    if (registerContainer) registerContainer.style.display = 'block';
}

function clearModalForms() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    if (loginForm) loginForm.reset();
    if (registerForm) registerForm.reset();
    
    const errorMessages = document.querySelectorAll('.modal-error-message');
    const successMessages = document.querySelectorAll('.modal-success-message');
    errorMessages.forEach(msg => msg.style.display = 'none');
    successMessages.forEach(msg => msg.style.display = 'none');
}

// ===== BOOKING SYSTEM =====
// Booking Sistem
class BookingSystem {
    constructor() {
        this.selectedBarber = null;
        this.selectedService = null;
        this.selectedDate = null;
        this.selectedTime = null;
        this.currentSlide = 0;
        
        // Merr te dhenat e berberit
        this.barbers = [
            { id: 1, name: 'John Smith', specialty: 'Expert in Fades & Classic Cuts' },
            { id: 2, name: 'Mike Johnson', specialty: 'Specialist in Modern Styles' },
            { id: 3, name: 'David Brown', specialty: 'Master of Beard Grooming' },
            { id: 4, name: 'Chris Wilson', specialty: 'Contemporary Hair Styling Expert' }
        ];

        this.services = [];

        // Available time slots
        this.timeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
        ];

        // Set minimum date to today
        this.setMinDate();
        
        // Load services from database
        this.loadServices();
    }

    async loadServices() {
        try {
            const response = await fetch('../BackEnd/services.php?action=getAll');
            const data = await response.json();
            
            if (data.success && data.services) {
                this.services = data.services;
                this.displayServices();
            }
        } catch (error) {
            console.error('Error loading services:', error);
        }
    }

    displayServices() {
        const container = document.getElementById('service-grid');
        if (!container) return;
        
        container.innerHTML = this.services.map(service => `
            <div class="service-option" data-service-id="${service.id}" data-price="${service.price}" data-duration="${service.duration}" onclick="selectService(${service.id})">
                <h3>${service.name}</h3>
                <p class="price">$${parseFloat(service.price).toFixed(2)}</p>
                <p class="duration">⏱ ${service.duration} minutes</p>
            </div>
        `).join('');
    }

    setMinDate() {
        const dateInput = document.getElementById('booking-date');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }
    }

    selectBarber(barberId) {
        // Remove previous selection
        document.querySelectorAll('.barber-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selection to clicked card
        const selectedCard = document.querySelector(`[data-barber-id="${barberId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
            this.selectedBarber = this.barbers.find(b => b.id === barberId);
            this.updateSummary();
        }
    }

    selectService(serviceId) {
        // Remove previous selection
        document.querySelectorAll('.service-option').forEach(option => {
            option.classList.remove('selected');
        });

        // Add selection to clicked option
        const selectedOption = document.querySelector(`[data-service-id="${serviceId}"]`);
        if (selectedOption) {
            selectedOption.classList.add('selected');
            this.selectedService = this.services.find(s => s.id === serviceId);
            this.updateSummary();
        }
    }

    loadTimeSlots() {
        const dateInput = document.getElementById('booking-date');
        const timeSlotsContainer = document.getElementById('time-slots');
        
        if (!dateInput.value) {
            return;
        }

        this.selectedDate = dateInput.value;
        timeSlotsContainer.innerHTML = '';

        // Generate time slots
        this.timeSlots.forEach(time => {
            const slot = document.createElement('div');
            slot.className = 'time-slot';
            slot.textContent = time;
            slot.onclick = () => this.selectTime(time);

            // Randomly mark some slots as unavailable (for demo purposes)
            if (Math.random() > 0.7) {
                slot.classList.add('unavailable');
                slot.onclick = null;
            }

            timeSlotsContainer.appendChild(slot);
        });

        this.updateSummary();
    }

    selectTime(time) {
        // Remove previous selection
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Add selection to clicked slot
        event.target.classList.add('selected');
        this.selectedTime = time;
        this.updateSummary();
        this.checkFormValidity();
    }

    updateSummary() {
        // Update barber
        document.getElementById('summary-barber').textContent = 
            this.selectedBarber ? this.selectedBarber.name : 'Not selected';

        // Update service
        document.getElementById('summary-service').textContent = 
            this.selectedService ? this.selectedService.name : 'Not selected';

        // Update date
        document.getElementById('summary-date').textContent = 
            this.selectedDate ? new Date(this.selectedDate + 'T00:00:00').toLocaleDateString() : 'Not selected';

        // Update time
        document.getElementById('summary-time').textContent = 
            this.selectedTime ? this.selectedTime : 'Not selected';

        // Update total
        document.getElementById('summary-total').textContent = 
            this.selectedService ? `$${this.selectedService.price}` : '$0';

        this.checkFormValidity();
    }

    checkFormValidity() {
        const submitBtn = document.getElementById('submit-booking');
        const isValid = this.selectedBarber && this.selectedService && 
                       this.selectedDate && this.selectedTime;
        
        submitBtn.disabled = !isValid;
    }

    slideBarbers(direction) {
        const track = document.getElementById('barber-track');
        const cards = document.querySelectorAll('.barber-card');
        const cardWidth = cards[0].offsetWidth;
        const gap = parseFloat(getComputedStyle(track).gap);
        
        this.currentSlide += direction;
        
        // Limit slides
        if (this.currentSlide < 0) this.currentSlide = 0;
        if (this.currentSlide > cards.length - 3) this.currentSlide = cards.length - 3;
        
        const offset = -(this.currentSlide * (cardWidth + gap));
        track.style.transform = `translateX(${offset}px)`;
    }

    async submitBooking() {
        // Check if user is logged in
        if (!window.authSystem || !window.authSystem.isLoggedIn()) {
            openLoginModal();
            return;
        }

        // Get all booking data
        const bookingData = {
            action: 'create',
            serviceType: this.selectedService.name,
            appointmentDate: this.selectedDate,
            appointmentTime: this.selectedTime,
            notes: `Barber: ${this.selectedBarber.name}, Service: ${this.selectedService.name} ($${this.selectedService.price})`
        };

        try {
            const response = await fetch('../BackEnd/booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bookingData)
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('success-message').textContent = 
                    'Booking confirmed! You will receive a confirmation shortly.';
                document.getElementById('success-message').style.display = 'block';
                document.getElementById('error-message').style.display = 'none';

                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            } else {
                document.getElementById('error-message').textContent = result.message;
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('success-message').style.display = 'none';
            }
        } catch (error) {
            console.error('Booking error:', error);
            document.getElementById('error-message').textContent = 'Connection error. Please try again.';
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('success-message').style.display = 'none';
        }
    }
}

// Initialize booking system
const bookingSystem = new BookingSystem();

// Global functions for onclick handlers
function selectBarber(barberId) {
    bookingSystem.selectBarber(barberId);
}

function selectService(serviceId) {
    bookingSystem.selectService(serviceId);
}

function loadTimeSlots() {
    bookingSystem.loadTimeSlots();
}

function slideBarbers(direction) {
    bookingSystem.slideBarbers(direction);
}

function selectService(serviceId) {
    bookingSystem.selectService(serviceId);
}

function submitBooking() {
    bookingSystem.submitBooking();
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    const modal = document.getElementById('login-modal');
    if (e.target === modal) {
        closeLoginModal();
        // Show error message if user closes without logging in
        if (!window.authSystem || !window.authSystem.isLoggedIn()) {
            showLoginRequiredMessage();
        }
    }
});

function showLoginRequiredMessage() {
    const errorMsg = document.getElementById('error-message');
    if (errorMsg) {
        errorMsg.textContent = 'Please login to book an appointment.';
        errorMsg.style.display = 'block';
    }
    const submitBtn = document.getElementById('submit-booking');
    if (submitBtn) {
        submitBtn.disabled = true;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('Booking page ready');
    
    // Wait for auth system to be ready
    setTimeout(() => {
        if (window.authSystem && window.authSystem.isLoggedIn()) {
            // User is logged in - don't show popup, enable booking
            console.log('User is logged in');
            const submitBtn = document.getElementById('submit-booking');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        } else {
            // User is NOT logged in - show popup automatically
            console.log('User not logged in - showing popup');
            openLoginModal();
        }
    }, 200);
});


