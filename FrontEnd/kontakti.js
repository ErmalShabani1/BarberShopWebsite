// Mobile menu toggle
function toggleMobileMenu() {
    const hamburger = document.getElementById('hamburger-btn');
    const navMenu = document.getElementById('nav-menu');
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('mobile-active');
}

console.log('kontakti.js: FILE LOADED');

function handleContactForm(event) {
    event.preventDefault();

    // Check if user is logged in
    if (typeof auth !== 'undefined' && !auth.isLoggedIn()) {
        alert('Please login to send a message or rate a barber.');
        openLoginModal();
        return;
    }

    const formData = new FormData(event.target);
    const message = formData.get('message');
    const subject = formData.get('subject');
    const rating = formData.get('rating');
    const barberId = formData.get('barber_id');

    // Check if at least message or rating is provided
    const hasMessage = message && message.trim() !== '';
    const hasRating = rating !== null && rating !== '';

    if (!hasMessage && !hasRating) {
        alert('Please provide either a message or a rating');
        return;
    }

    if (!barberId) {
        alert('Please select a barber');
        return;
    }

    const promises = [];
    const actions = [];

    // Send message if provided
    if (hasMessage) {
        const messageData = new FormData();
        messageData.append('barber_id', barberId);
        messageData.append('subject', subject || 'No subject');
        messageData.append('message', message);
        
        promises.push(
            fetch('../BackEnd/send_message.php', {
                method: 'POST',
                body: messageData,
                credentials: 'include'
            })
            .then(res => res.text())
            .then(data => ({ type: 'message', data }))
        );
        actions.push('message');
    }

    // Send rating if provided
    if (hasRating) {
        const ratingData = new FormData();
        ratingData.append('barber_id', barberId);
        ratingData.append('rating', rating);
        
        promises.push(
            fetch('../BackEnd/submit_rating.php', {
                method: 'POST',
                body: ratingData,
                credentials: 'include'
            })
            .then(res => res.text())
            .then(data => ({ type: 'rating', data }))
        );
        actions.push('rating');
    }

    Promise.all(promises)
        .then(results => {
            const messages = [];
            let hasError = false;

            results.forEach(result => {
                if (result.type === 'message') {
                    if (result.data === 'success') {
                        messages.push('Message sent successfully!');
                    } else {
                        messages.push('Error sending message: ' + result.data);
                        hasError = true;
                    }
                } else if (result.type === 'rating') {
                    if (result.data === 'success') {
                        messages.push('Rating submitted successfully!');
                    } else if (result.data === 'already_rated') {
                        messages.push('You have already rated this barber.');
                        hasError = true;
                    } else {
                        messages.push('Error submitting rating: ' + result.data);
                        hasError = true;
                    }
                }
            });

            alert(messages.join('\n'));
            
            if (!hasError) {
                event.target.reset();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to submit. Please try again.');
        });
}

// Modal Functions
function openLoginModal() {
    document.getElementById('login-modal').style.display = 'flex';
    showLoginForm();
}

function closeLoginModal() {
    document.getElementById('login-modal').style.display = 'none';
    showLoginForm(); // Reset to login form
}

function showLoginForm() {
    document.getElementById('login-form-container').style.display = 'block';
    document.getElementById('register-form-container').style.display = 'none';
}

function showRegisterForm() {
    document.getElementById('login-form-container').style.display = 'none';
    document.getElementById('register-form-container').style.display = 'block';
}

// Handle Register Form
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded: kontakti.js loaded');
    
    // Load barbers into dropdowns immediately
    console.log('DOMContentLoaded: calling loadBarbers()');
    loadBarbers();
    
    // Check if user is logged in and disable form if not
    const contactForm = document.querySelector('.contact-form');
    const contactSubmitBtn = contactForm ? contactForm.querySelector('.submit-btn') : null;
    
    console.log('DOMContentLoaded: contactForm', contactForm);
    
    function checkLoginStatus() {
        // Wait for auth to be defined and check session
        if (typeof auth === 'undefined') {
            setTimeout(checkLoginStatus, 100);
            return;
        }
        
        // Handle contact form
        if (contactForm) {
            if (!auth.isLoggedIn()) {
                const inputs = contactForm.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = true;
                });
                if (contactSubmitBtn) {
                    contactSubmitBtn.disabled = true;
                    contactSubmitBtn.textContent = 'Login Required';
                    contactSubmitBtn.style.cursor = 'not-allowed';
                }
            } else {
                const inputs = contactForm.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.disabled = false;
                });
                if (contactSubmitBtn) {
                    contactSubmitBtn.disabled = false;
                    contactSubmitBtn.textContent = 'Submit';
                    contactSubmitBtn.style.cursor = 'pointer';
                }
                loadBarbers(); // Reload barbers after login
            }
        }
    }
    
    // Check after a delay to allow auth to initialize session
    setTimeout(checkLoginStatus, 500);
    
    // Re-check after login modal closes
    const originalCloseModal = window.closeLoginModal;
    window.closeLoginModal = function() {
        if (originalCloseModal) originalCloseModal();
        setTimeout(checkLoginStatus, 100);
    };

    // Handle Login Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('modal-username').value;
            const password = document.getElementById('modal-password').value;
            const errorMsg = document.getElementById('modal-error-message');
            const successMsg = document.getElementById('modal-success-message');
            
            // Clear previous messages
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
            successMsg.textContent = '';
            successMsg.style.display = 'none';
            
            // Check if auth system is available
            if (typeof auth === 'undefined') {
                errorMsg.textContent = 'Authentication system not loaded!';
                errorMsg.style.display = 'block';
                return;
            }
            
            const result = await auth.login(username, password);
            
            if (result.success) {
                successMsg.textContent = 'Login successful!';
                successMsg.style.display = 'block';
                
                // Close modal and refresh page after short delay
                setTimeout(() => {
                    closeLoginModal();
                    window.location.reload();
                }, 1000);
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
            }
        });
    }

    // Handle Register Form
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('register-username').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            
            const errorMsg = document.getElementById('register-error-message');
            const successMsg = document.getElementById('register-success-message');
            
            // Clear previous messages
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
            successMsg.textContent = '';
            successMsg.style.display = 'none';
            
            // Validate passwords match
            if (password !== confirmPassword) {
                errorMsg.textContent = 'Passwords do not match!';
                errorMsg.style.display = 'block';
                return;
            }
            
            // Validate password length
            if (password.length < 6) {
                errorMsg.textContent = 'Password must be at least 6 characters!';
                errorMsg.style.display = 'block';
                return;
            }
            
            // Use auth system to register
            const result = await auth.register(username, email, password, username);
            
            if (result.success) {
                successMsg.textContent = 'Registration successful! Please login.';
                successMsg.style.display = 'block';
                
                setTimeout(() => {
                    showLoginForm();
                    registerForm.reset();
                }, 2000);
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
            }
        });
    }
});

// Load barbers into dropdown
async function loadBarbers() {
    console.log('loadBarbers: starting...');
    try {
        const response = await fetch('../BackEnd/get_users.php?role=barber');
        console.log('loadBarbers: response status', response.status);
        const result = await response.json();
        console.log('loadBarbers: result', result);
        
        if (result.success && result.barbers) {
            const barbers = result.barbers;
            console.log('loadBarbers: found', barbers.length, 'barbers');
            
            // Populate barber dropdown
            const barberSelect = document.getElementById('barber');
            console.log('loadBarbers: barberSelect element', barberSelect);
            if (barberSelect) {
                barberSelect.innerHTML = '<option value="">-- Select a Barber --</option>';
                barbers.forEach(barber => {
                    const option = document.createElement('option');
                    option.value = barber.id;
                    option.textContent = barber.fullName || barber.username;
                    barberSelect.appendChild(option);
                });
                console.log('loadBarbers: populated dropdown with', barbers.length, 'barbers');
            }
        } else {
            console.error('loadBarbers: no barbers in result or success=false', result);
        }
    } catch (error) {
        console.error('Error loading barbers:', error);
    }
}
