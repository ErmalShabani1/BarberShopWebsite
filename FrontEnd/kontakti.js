function handleContactForm(event) {
    event.preventDefault();
    
    // Check if user is logged in
    if (typeof auth === 'undefined' || !auth.isLoggedIn()) {
        alert('Please login to send a message.');
        openLoginModal();
        return;
    }
    
    alert('Thank you for your message! We will get back to you soon.');
    event.target.reset();
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
    // Check if user is logged in and disable form if not
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = contactForm ? contactForm.querySelector('.submit-btn') : null;
    
    function checkLoginStatus() {
        if (typeof auth !== 'undefined' && contactForm) {
            if (!auth.isLoggedIn()) {
                // Disable form inputs
                const inputs = contactForm.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.placeholder = 'Please login to use contact form';
                });
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Login Required';
                    submitBtn.style.cursor = 'not-allowed';
                }
            } else {
                // Enable form inputs
                const inputs = contactForm.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                    input.placeholder = '';
                });
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Message';
                    submitBtn.style.cursor = 'pointer';
                }
            }
        }
    }
    
    // Check on page load
    checkLoginStatus();
    
    // Re-check after login modal closes
    const originalCloseModal = window.closeLoginModal;
    window.closeLoginModal = function() {
        if (originalCloseModal) originalCloseModal();
        setTimeout(checkLoginStatus, 100);
    };

    // Handle Login Form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
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
            
            const result = auth.login(username, password);
            
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
        registerForm.addEventListener('submit', function(e) {
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
            
            // Store user in localStorage (in real app, this would be a backend call)
            const users = JSON.parse(localStorage.getItem('registeredUsers') || '[]');
            
            // Check if username already exists
            if (users.find(u => u.username === username)) {
                errorMsg.textContent = 'Username already exists!';
                errorMsg.style.display = 'block';
                return;
            }
            
            // Add new user
            users.push({
                username: username,
                email: email,
                password: password,
                role: 'user'
            });
            
            localStorage.setItem('registeredUsers', JSON.stringify(users));
            
            successMsg.textContent = 'Registration successful! You can now login.';
            successMsg.style.display = 'block';
            
            // Switch to login form after 2 seconds
            setTimeout(() => {
                showLoginForm();
                document.getElementById('modal-username').value = username;
            }, 2000);
        });
    }
});

