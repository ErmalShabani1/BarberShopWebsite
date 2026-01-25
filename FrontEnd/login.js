// Authentication System
class AuthSystem {
    constructor() {
        this.currentUser = null;
        this.checkSession();
    }

    async checkSession() {
        try {
            const response = await fetch('../BackEnd/user_actions.php?action=getCurrentUser');
            const result = await response.json();
            if (result.success) {
                this.currentUser = result.user;
                this.updateNavigation();
            }
        } catch (error) {
            console.error('Session check failed:', error);
        }
    }

    async login(username, password) {
        try {
            const response = await fetch('../BackEnd/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();
            
            if (result.success) {
                this.currentUser = result.user;
                this.updateNavigation();
            }
            
            return result;
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'Connection error. Please try again.' };
        }
    }

    async register(username, email, password, fullName, phone) {
        try {
            const response = await fetch('../BackEnd/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, email, password, fullName, phone })
            });

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Registration error:', error);
            return { success: false, message: 'Connection error. Please try again.' };
        }
    }

    async logout() {
        try {
            const response = await fetch('../BackEnd/user_actions.php?action=logout');
            const result = await response.json();
            
            if (result.success) {
                this.currentUser = null;
                window.location.href = 'index.php';
            }
        } catch (error) {
            console.error('Logout error:', error);
        }
    }

    getCurrentUser() {
        return this.currentUser;
    }

    isLoggedIn() {
        return this.currentUser !== null;
    }

    isAdmin() {
        return this.currentUser && this.currentUser.role === 'admin';
    }

    isBarber() {
        return this.currentUser && this.currentUser.role === 'barber';
    }

    isClient() {
        return this.currentUser && this.currentUser.role === 'client';
    }

    requireLogin() {
        if (!this.isLoggedIn()) {
            alert('Please login to access this page');
            return false;
        }
        return true;
    }

    updateNavigation() {
        const editCancelLink = document.querySelector('a[href="edit.php"]');
        
        if (editCancelLink) {
            const listItem = editCancelLink.closest('li');
            
            if (this.isClient() || !this.isLoggedIn()) {
                if (listItem) listItem.style.display = 'none';
            } else {
                if (listItem) listItem.style.display = 'block';
            }
        }

        const authBtn = document.getElementById('auth-btn');
        if (authBtn) {
            const authListItem = authBtn.closest('li');
            if (this.isLoggedIn()) {
                if (authListItem) authListItem.style.display = 'none';
            } else {
                if (authListItem) authListItem.style.display = 'block';
            }
        }

        this.addUserInfo();
    }

    addUserInfo() {
        if (this.isLoggedIn()) {
            const nav = document.querySelector('.nav-menu');
            if (nav) {
                const existingUserInfo = document.getElementById('user-info');
                if (existingUserInfo) existingUserInfo.remove();
                const existingLogoutBtn = document.getElementById('logout-btn-item');
                if (existingLogoutBtn) existingLogoutBtn.remove();

                const userInfo = document.createElement('li');
                userInfo.id = 'user-info';
                userInfo.style.position = 'absolute';
                userInfo.style.right = '150px';
                userInfo.style.top = '50%';
                userInfo.style.transform = 'translateY(-50%)';
                userInfo.innerHTML = `
                    <span class="nav-link" style="cursor: default; padding: 10px 15px; font-size: 14px;">
                        ${this.currentUser.fullName} (${this.currentUser.role})
                    </span>
                `;
                nav.appendChild(userInfo);

                const logoutBtn = document.createElement('li');
                logoutBtn.id = 'logout-btn-item';
                logoutBtn.style.position = 'absolute';
                logoutBtn.style.right = '5%';
                logoutBtn.style.top = '50%';
                logoutBtn.style.transform = 'translateY(-50%)';
                logoutBtn.innerHTML = `
                    <a href="#" class="nav-link" id="logout-btn" style="font-size: 14px; padding: 10px 15px;">Logout</a>
                `;
                nav.appendChild(logoutBtn);

                document.getElementById('logout-btn').addEventListener('click', (e) => {
                    e.preventDefault();
                    this.logout();
                });
            }
        }
    }
}

// Initialize auth system
const auth = new AuthSystem();
window.authSystem = auth; // Make it globally accessible

// Update navigation on page load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        auth.updateNavigation();
    }, 100);
});

// Login Modal Functions
function openLoginModal() {
    document.getElementById('login-modal').style.display = 'flex';
    showLoginForm();
}

function closeLoginModal() {
    document.getElementById('login-modal').style.display = 'none';
    clearForms();
}

function showLoginForm() {
    document.getElementById('login-form-container').style.display = 'block';
    document.getElementById('register-form-container').style.display = 'none';
}

function showRegisterForm() {
    document.getElementById('login-form-container').style.display = 'none';
    document.getElementById('register-form-container').style.display = 'block';
}

function clearForms() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    if (loginForm) loginForm.reset();
    if (registerForm) registerForm.reset();
    
    const errorMessages = document.querySelectorAll('.modal-error-message');
    const successMessages = document.querySelectorAll('.modal-success-message');
    errorMessages.forEach(msg => msg.style.display = 'none');
    successMessages.forEach(msg => msg.style.display = 'none');
}

// Login form submission
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('modal-username').value;
            const password = document.getElementById('modal-password').value;
            const errorMsg = document.getElementById('modal-error-message');
            const successMsg = document.getElementById('modal-success-message');
            
            const result = await auth.login(username, password);
            
            if (result.success) {
                successMsg.textContent = 'Login successful! Redirecting...';
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
                
                setTimeout(() => {
                    closeLoginModal();
                    window.location.reload();
                }, 1000);
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            }
        });
    }

    // Register form submission
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('reg-username').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            const confirmPassword = document.getElementById('reg-confirm-password').value;
            const fullName = document.getElementById('reg-fullname').value;
            const phone = document.getElementById('reg-phone').value;
            const errorMsg = document.getElementById('reg-error-message');
            const successMsg = document.getElementById('reg-success-message');
            
            if (password !== confirmPassword) {
                errorMsg.textContent = 'Passwords do not match';
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
                return;
            }
            
            const result = await auth.register(username, email, password, fullName, phone);
            
            if (result.success) {
                successMsg.textContent = 'Registration successful! Please login.';
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
                
                setTimeout(() => {
                    showLoginForm();
                    registerForm.reset();
                }, 2000);
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            }
        });
    }
});

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    const modal = document.getElementById('login-modal');
    if (e.target === modal) {
        closeLoginModal();
    }
});
