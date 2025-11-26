// Authentication System
class AuthSystem {
    constructor() {
        // Initialize users from localStorage or use defaults
        this.initializeUsers();
        this.currentUser = this.getCurrentUser();
    }

    initializeUsers() {
        const storedUsers = localStorage.getItem('allUsers');
        if (storedUsers) {
            this.users = JSON.parse(storedUsers);
        } else {
            // Default users
            this.users = {
                admin: {
                    username: 'admin',
                    password: 'admin123',
                    role: 'admin',
                    fullName: 'Admin User',
                    email: 'admin@barbershop.com',
                    createdAt: new Date().toISOString()
                },
                barber: {
                    username: 'barber',
                    password: 'barber123',
                    role: 'barber',
                    fullName: 'John Barber',
                    email: 'barber@barbershop.com',
                    createdAt: new Date().toISOString()
                },
                user: {
                    username: 'user',
                    password: 'user123',
                    role: 'user',
                    fullName: 'Regular User',
                    email: 'user@barbershop.com',
                    createdAt: new Date().toISOString()
                },
                user2: {
                    username: 'user2',
                    password: 'user123',
                    role: 'user',
                    fullName: 'Jane Doe',
                    email: 'jane@barbershop.com',
                    createdAt: new Date().toISOString()
                },
                barber2: {
                    username: 'barber2',
                    password: 'barber123',
                    role: 'barber',
                    fullName: 'Mike Barber',
                    email: 'mike@barbershop.com',
                    createdAt: new Date().toISOString()
                }
            };
            this.saveUsers();
        }
    }

    saveUsers() {
        localStorage.setItem('allUsers', JSON.stringify(this.users));
    }

    getAllUsers() {
        return Object.values(this.users);
    }

    updateUserRole(username, newRole) {
        if (this.users[username]) {
            this.users[username].role = newRole;
            this.saveUsers();
            return true;
        }
        return false;
    }

    deleteUser(username) {
        if (this.users[username] && username !== 'admin') {
            delete this.users[username];
            this.saveUsers();
            return true;
        }
        return false;
    }

    // Login method
    login(username, password) {
        const user = Object.values(this.users).find(
            u => u.username === username && u.password === password
        );

        if (user) {
            const userData = {
                username: user.username,
                role: user.role,
                fullName: user.fullName
            };
            localStorage.setItem('currentUser', JSON.stringify(userData));
            this.currentUser = userData;
            return { success: true, user: userData };
        }
        
        return { success: false, message: 'Invalid username or password' };
    }

    // Logout method
    logout() {
        localStorage.removeItem('currentUser');
        this.currentUser = null;
        window.location.href = 'index.html';
    }

    // Get current logged in user
    getCurrentUser() {
        const userData = localStorage.getItem('currentUser');
        return userData ? JSON.parse(userData) : null;
    }

    // Check if user is logged in
    isLoggedIn() {
        return this.currentUser !== null;
    }

    // Check user role
    isAdmin() {
        return this.currentUser && this.currentUser.role === 'admin';
    }

    isBarber() {
        return this.currentUser && this.currentUser.role === 'barber';
    }

    isUser() {
        return this.currentUser && this.currentUser.role === 'user';
    }

    // Require login for page access
    requireLogin(redirectTo = 'login.html') {
        if (!this.isLoggedIn()) {
            window.location.href = redirectTo;
            return false;
        }
        return true;
    }

    // Update navigation based on user role
    updateNavigation() {
        const editCancelLink = document.querySelector('a[href="edit.html"]');
        
        if (editCancelLink) {
            const listItem = editCancelLink.closest('li');
            
            if (this.isUser() || !this.isLoggedIn()) {
                // Hide edit/cancel for regular users and non-logged users
                if (listItem) listItem.style.display = 'none';
            } else {
                // Show for admin and barber
                if (listItem) listItem.style.display = 'block';
            }
        }

        // Add logout button if logged in
        this.addUserInfo();
    }

    // Add user info and logout button to navigation
    addUserInfo() {
        if (this.isLoggedIn()) {
            const nav = document.querySelector('.nav-menu');
            if (nav) {
                // Remove existing user info if present
                const existingUserInfo = document.getElementById('user-info');
                if (existingUserInfo) existingUserInfo.remove();

                // Create user info element
                const userInfo = document.createElement('li');
                userInfo.id = 'user-info';
                userInfo.style.marginLeft = 'auto';
                userInfo.innerHTML = `
                    <span class="nav-link" style="cursor: default;">
                        ${this.currentUser.fullName} (${this.currentUser.role})
                    </span>
                `;
                nav.appendChild(userInfo);

                // Add logout button
                const logoutBtn = document.createElement('li');
                logoutBtn.innerHTML = `
                    <a href="#" class="nav-link" id="logout-btn">Logout</a>
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

// Update navigation on page load
document.addEventListener('DOMContentLoaded', () => {
    auth.updateNavigation();
});

// Login form submission handler
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMsg = document.getElementById('error-message');
            const successMsg = document.getElementById('success-message');
            
            const result = auth.login(username, password);
            
            if (result.success) {
                successMsg.textContent = 'Login successful! Redirecting...';
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
                
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
            } else {
                errorMsg.textContent = result.message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            }
        });
    }
});
