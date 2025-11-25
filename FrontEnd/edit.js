// Edit/Manage Bookings System
class EditBookingSystem {
    constructor() {
        this.bookings = this.loadBookings();
        this.services = this.loadServices();
        this.init();
    }

    init() {
        // Check if user is logged in and has permission
        if (!auth.isLoggedIn()) {
            window.location.href = 'login.html';
            return;
        }

        // Show appropriate panel based on user role
        if (auth.isAdmin()) {
            this.showAdminPanel();
        } else if (auth.isBarber()) {
            this.showBarberPanel();
        } else if (auth.isUser()) {
            this.showUserPanel();
        }

        // Setup add service form for barbers
        if (auth.isBarber()) {
            this.setupAddServiceForm();
        }
    }

    loadBookings() {
        return JSON.parse(localStorage.getItem('bookings') || '[]');
    }

    loadServices() {
        return JSON.parse(localStorage.getItem('services') || JSON.stringify([
            { id: 1, name: 'High Fade', price: 20, duration: 30, description: 'Modern high fade haircut' },
            { id: 2, name: 'Low Fade', price: 15, duration: 25, description: 'Classic low fade' },
            { id: 3, name: 'Mid Fade', price: 17, duration: 25, description: 'Versatile mid fade' },
            { id: 4, name: 'Taper Fade', price: 18, duration: 28, description: 'Professional taper fade' }
        ]));
    }

    saveBookings() {
        localStorage.setItem('bookings', JSON.stringify(this.bookings));
    }

    saveServices() {
        localStorage.setItem('services', JSON.stringify(this.services));
    }

    showAdminPanel() {
        document.getElementById('admin-panel').classList.add('active');
        this.renderAdminBookings();
        this.renderAdminUsers();
    }

    showBarberPanel() {
        document.getElementById('barber-panel').classList.add('active');
        this.renderBarberBookings();
        this.renderBarberUsers();
    }

    showUserPanel() {
        document.getElementById('user-panel').classList.add('active');
        this.renderUserBookings();
    }

    renderAdminBookings() {
        const container = document.getElementById('admin-bookings');
        
        if (this.bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No appointments yet</h3>
                    <p>All bookings will appear here</p>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Barber</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${this.bookings.map(booking => `
                        <tr>
                            <td>#${booking.id}</td>
                            <td>${booking.customer.fullName}</td>
                            <td>${booking.barber.name}</td>
                            <td>${booking.service.name}</td>
                            <td>${new Date(booking.date + 'T00:00:00').toLocaleDateString()}</td>
                            <td>${booking.time}</td>
                            <td>$${booking.service.price}</td>
                            <td><span class="status-badge status-${booking.status}">${booking.status}</span></td>
                            <td>
                                ${booking.status === 'pending' ? `
                                    <button class="action-btn btn-confirm" onclick="editSystem.updateStatus(${booking.id}, 'confirmed')">Confirm</button>
                                    <button class="action-btn btn-deny" onclick="editSystem.updateStatus(${booking.id}, 'cancelled')">Deny</button>
                                ` : ''}
                                ${booking.status === 'confirmed' ? `
                                    <button class="action-btn btn-complete" onclick="editSystem.updateStatus(${booking.id}, 'completed')">Complete</button>
                                ` : ''}
                                <button class="action-btn btn-cancel" onclick="editSystem.deleteBooking(${booking.id})">Delete</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
    }

    renderBarberBookings() {
        const container = document.getElementById('barber-bookings');
        const barberName = auth.currentUser.fullName;
        
        // Filter bookings for current barber (for demo, show all)
        const barberBookings = this.bookings;

        if (barberBookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No appointments assigned</h3>
                    <p>Your appointments will appear here</p>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${barberBookings.map(booking => `
                        <tr>
                            <td>#${booking.id}</td>
                            <td>${booking.customer.fullName}</td>
                            <td>${booking.service.name}</td>
                            <td>${new Date(booking.date + 'T00:00:00').toLocaleDateString()}</td>
                            <td>${booking.time}</td>
                            <td><span class="status-badge status-${booking.status}">${booking.status}</span></td>
                            <td>
                                ${booking.status === 'pending' ? `
                                    <button class="action-btn btn-confirm" onclick="editSystem.updateStatus(${booking.id}, 'confirmed')">Accept</button>
                                    <button class="action-btn btn-deny" onclick="editSystem.updateStatus(${booking.id}, 'cancelled')">Deny</button>
                                ` : ''}
                                ${booking.status === 'confirmed' ? `
                                    <button class="action-btn btn-complete" onclick="editSystem.updateStatus(${booking.id}, 'completed')">Finish</button>
                                    <button class="action-btn btn-cancel" onclick="editSystem.updateStatus(${booking.id}, 'cancelled')">Cancel</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
    }

    renderUserBookings() {
        const container = document.getElementById('user-bookings');
        const userBookings = this.bookings.filter(b => b.customer.username === auth.currentUser.username);

        if (userBookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No appointments booked</h3>
                    <p><a href="Booking.html" style="color: #d4af37;">Book your first appointment</a></p>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Barber</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${userBookings.map(booking => `
                        <tr>
                            <td>#${booking.id}</td>
                            <td>${booking.barber.name}</td>
                            <td>${booking.service.name}</td>
                            <td>${new Date(booking.date + 'T00:00:00').toLocaleDateString()}</td>
                            <td>${booking.time}</td>
                            <td>$${booking.service.price}</td>
                            <td><span class="status-badge status-${booking.status}">${booking.status}</span></td>
                            <td>
                                ${booking.status === 'pending' || booking.status === 'confirmed' ? `
                                    <button class="action-btn btn-cancel" onclick="editSystem.cancelBooking(${booking.id})">Cancel</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
    }

    updateStatus(bookingId, newStatus) {
        const booking = this.bookings.find(b => b.id === bookingId);
        if (booking) {
            booking.status = newStatus;
            this.saveBookings();
            this.init(); // Refresh display
            alert(`Appointment #${bookingId} status updated to ${newStatus}`);
        }
    }

    deleteBooking(bookingId) {
        if (confirm('Are you sure you want to delete this booking?')) {
            this.bookings = this.bookings.filter(b => b.id !== bookingId);
            this.saveBookings();
            this.init();
            alert('Booking deleted successfully');
        }
    }

    cancelBooking(bookingId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            this.updateStatus(bookingId, 'cancelled');
        }
    }

    setupAddServiceForm() {
        const form = document.getElementById('add-service-form');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const newService = {
                id: this.services.length + 1,
                name: document.getElementById('service-name').value,
                price: parseFloat(document.getElementById('service-price').value),
                duration: parseInt(document.getElementById('service-duration').value),
                description: document.getElementById('service-description').value
            };

            this.services.push(newService);
            this.saveServices();
            
            alert('Service added successfully!');
            form.reset();
        });
    }

    // User Management Methods
    renderAdminUsers() {
        const container = document.getElementById('admin-users');
        const allUsers = auth.getAllUsers();

        if (allUsers.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No users found</h3>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${allUsers.map(user => `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.fullName}</td>
                            <td>${user.email}</td>
                            <td><span class="role-badge role-${user.role}">${user.role}</span></td>
                            <td>${new Date(user.createdAt).toLocaleDateString()}</td>
                            <td>
                                <select class="role-select" onchange="editSystem.changeUserRole('${user.username}', this.value)" ${user.username === 'admin' ? 'disabled' : ''}>
                                    <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
                                    <option value="barber" ${user.role === 'barber' ? 'selected' : ''}>Barber</option>
                                    <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                </select>
                                ${user.username !== 'admin' ? `
                                    <button class="action-btn btn-cancel" onclick="editSystem.removeUser('${user.username}')" style="font-size: 80%; padding: 0.3em 0.6em;">Delete</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
    }

    renderBarberUsers() {
        const container = document.getElementById('barber-users');
        const allUsers = auth.getAllUsers();

        if (allUsers.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <h3>No users found</h3>
                </div>
            `;
            return;
        }

        const tableHTML = `
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    ${allUsers.map(user => `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.fullName}</td>
                            <td>${user.email}</td>
                            <td><span class="role-badge role-${user.role}">${user.role}</span></td>
                            <td>${new Date(user.createdAt).toLocaleDateString()}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
    }

    changeUserRole(username, newRole) {
        if (confirm(`Change ${username}'s role to ${newRole}?`)) {
            if (auth.updateUserRole(username, newRole)) {
                alert('Role updated successfully!');
                this.renderAdminUsers();
            } else {
                alert('Failed to update role');
            }
        }
    }

    removeUser(username) {
        if (confirm(`Are you sure you want to delete user ${username}?`)) {
            if (auth.deleteUser(username)) {
                alert('User deleted successfully!');
                this.renderAdminUsers();
            } else {
                alert('Failed to delete user. Cannot delete admin account.');
            }
        }
    }
}

// Initialize edit system
const editSystem = new EditBookingSystem();
