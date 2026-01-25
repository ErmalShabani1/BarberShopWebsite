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

        this.services = [
            { id: 1, name: 'High Fade', price: 20, duration: 30 },
            { id: 2, name: 'Low Fade', price: 15, duration: 25 },
            { id: 3, name: 'Mid Fade', price: 17, duration: 25 },
            { id: 4, name: 'Taper Fade', price: 18, duration: 28 }
        ];

        // Available time slots
        this.timeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'
        ];

        // Set minimum date to today
        this.setMinDate();
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
        if (!auth.isLoggedIn()) {
            showLoginModal();
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

function submitBooking() {
    bookingSystem.submitBooking();
}

// Login Modal Functions
function showLoginModal() {
    document.getElementById('login-modal').classList.add('show');
}

function closeLoginModal() {
    document.getElementById('login-modal').classList.remove('show');
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('login-modal');
    if (event.target === modal) {
        closeLoginModal();
    }
}

// Authentication UI Handler Class
class AuthUI {
    constructor(authSystem) {
        this.authSystem = authSystem;
        this.init();
    }

    init() {
        this.setupLoginForm();
        this.setupRegisterForm();
        this.checkLoginStatus();
    }

    setupLoginForm() {
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleLogin();
            });
        }
    }

    async handleLogin() {
        const username = document.getElementById('modal-username').value;
        const password = document.getElementById('modal-password').value;
        const errorMsg = document.getElementById('modal-error-message');
        const successMsg = document.getElementById('modal-success-message');
        
        this.clearMessages(errorMsg, successMsg);
        
        if (!this.authSystem) {
            this.showError(errorMsg, 'Authentication system not loaded');
            return;
        }

        const result = await this.authSystem.login(username, password);
        
        if (result.success) {
            this.showSuccess(successMsg, 'Login successful!');
            setTimeout(() => {
                closeLoginModal();
                window.location.reload();
            }, 1000);
        } else {
            this.showError(errorMsg, result.message);
        }
    }

    setupRegisterForm() {
        const registerForm = document.getElementById('register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleRegister();
            });
        }
    }

    async handleRegister() {
        const username = document.getElementById('register-username').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;
        const fullName = document.getElementById('register-fullname')?.value || username;
        const phone = document.getElementById('register-phone')?.value || null;
        
        const errorMsg = document.getElementById('register-error-message');
        const successMsg = document.getElementById('register-success-message');
        
        this.clearMessages(errorMsg, successMsg);
        
        // Validate passwords match
        if (password !== confirmPassword) {
            this.showError(errorMsg, 'Passwords do not match!');
            return;
        }
        
        // Validate password length
        if (password.length < 6) {
            this.showError(errorMsg, 'Password must be at least 6 characters!');
            return;
        }
        
        if (!this.authSystem) {
            this.showError(errorMsg, 'Authentication system not loaded');
            return;
        }

        const result = await this.authSystem.register(username, email, password, fullName, phone);
        
        if (result.success) {
            this.showSuccess(successMsg, 'Registration successful! You can now login.');
            setTimeout(() => {
                showLoginForm();
                document.getElementById('modal-username').value = username;
            }, 2000);
        } else {
            this.showError(errorMsg, result.message || 'Registration failed');
        }
    }

    checkLoginStatus() {
        if (!this.authSystem || !this.authSystem.isLoggedIn()) {
            const errorMsgElement = document.getElementById('error-message');
            if (errorMsgElement) {
                errorMsgElement.textContent = 'Please login to book an appointment.';
                errorMsgElement.style.display = 'block';
            }
            const submitBtn = document.getElementById('submit-booking');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        }
    }

    clearMessages(errorMsg, successMsg) {
        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
        if (successMsg) {
            successMsg.textContent = '';
            successMsg.style.display = 'none';
        }
    }

    showError(errorMsg, message) {
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }

    showSuccess(successMsg, message) {
        if (successMsg) {
            successMsg.textContent = message;
            successMsg.style.display = 'block';
        }
    }
}

// Initialize Auth UI when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (window.authSystem) {
        const authUI = new AuthUI(window.authSystem);
        window.authUI = authUI; // Make it globally accessible if needed
    }
});

// Toggle between login and register forms
function showLoginForm() {
    document.getElementById('login-form-container').style.display = 'block';
    document.getElementById('register-form-container').style.display = 'none';
}

function showRegisterForm() {
    document.getElementById('login-form-container').style.display = 'none';
    document.getElementById('register-form-container').style.display = 'block';
}
