function handleContactForm(event) {
    event.preventDefault();

    // Check if user is logged in before sending
    if (typeof auth !== 'undefined' && !auth.isLoggedIn()) {
        alert('Please login to send a message.');
        openLoginModal();
        return;
    }

    const formData = new FormData(event.target);

    fetch('../BackEnd/send_message.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
    })
    .then(res => {
        if (res.status === 401) {
            alert('Please login to send a message.');
            openLoginModal();
            return null;
        }
        if (!res.ok) {
            throw new Error('Failed to send message (Status: ' + res.status + ')');
        }
        return res.text();
    })
    .then(data => {
        if (!data) return;
        if (data === 'success') {
            alert('Thank you for your message! We will get back to you soon.');
            event.target.reset();
        } else {
            console.error('Error response:', data);
            alert('Error: ' + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send message. Please try again.');
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
    // Check if user is logged in and disable form if not
    const contactForm = document.querySelector('.contact-form');
    const submitBtn = contactForm ? contactForm.querySelector('.submit-btn') : null;
    
    function checkLoginStatus() {
        // Wait for auth to be defined and check session
        if (typeof auth === 'undefined') {
            setTimeout(checkLoginStatus, 100);
            return;
        }
        
        if (!contactForm) return;
        
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
