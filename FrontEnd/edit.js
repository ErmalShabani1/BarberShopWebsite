// Edit/Manage Bookings System
class EditBookingSystem {
    constructor() {
        this.bookings = this.loadBookings();
        this.services = this.loadServices();
    }

    async init() {
        console.log('EditBookingSystem init called');
        console.log('window.authSystem:', window.authSystem);
        
        // Check if user is logged in and has permission
        if (!window.authSystem || !window.authSystem.isLoggedIn()) {
            console.log('User not logged in');
            const errorMsg = document.getElementById('error-message');
            if (errorMsg) {
                errorMsg.textContent = 'Please login to access this page.';
                errorMsg.style.display = 'block';
            }
            return;
        }

        console.log('User is logged in:', window.authSystem.getCurrentUser());

        // Show appropriate panel based on user role
        if (window.authSystem.isAdmin()) {
            console.log('Showing admin panel');
            await this.showAdminPanel();
            this.setupAdminServiceForm();
        } else if (window.authSystem.isBarber()) {
            console.log('Showing barber panel');
            await this.showBarberPanel();
            this.setupAddServiceForm();
        } else {
            console.log('Showing user panel');
            this.showUserPanel();
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

    async showAdminPanel() {
        console.log('Showing admin panel');
        document.getElementById('admin-panel').classList.add('active');
        document.getElementById('admin-services-panel').classList.add('active');
        console.log('Admin services panel:', document.getElementById('admin-services-panel'));
        this.renderAdminBookings();
        await this.renderAdminUsers();
        console.log('About to render service edit history');
        await this.renderServiceEditHistory();
        console.log('Service edit history rendered');
    }

    async showBarberPanel() {
        document.getElementById('barber-panel').classList.add('active');
        this.renderBarberBookings();
        await this.renderBarberUsers();
        await this.renderBarberMessages();
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
        const barberName = window.authSystem.getCurrentUser().fullName;
        
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
        const userBookings = this.bookings.filter(b => b.customer.username === window.authSystem.getCurrentUser().username);

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



    // User Management Methods
    async renderAdminUsers() {
        const container = document.getElementById('admin-users');
        
        if (!window.authSystem) {
            container.innerHTML = '<p>Loading...</p>';
            return;
        }
        
        const allUsers = await window.authSystem.getAllUsers();

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
                                <select class="role-select" data-username="${user.username}" ${user.username === 'admin' ? 'disabled' : ''}>
                                    <option value="client" ${user.role === 'client' ? 'selected' : ''}>Client</option>
                                    <option value="barber" ${user.role === 'barber' ? 'selected' : ''}>Barber</option>
                                    <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                </select>
                                ${user.username !== 'admin' ? `
                                    <button class="action-btn btn-cancel" data-username="${user.username}" data-action="delete" style="font-size: 80%; padding: 0.3em 0.6em;">Delete</button>
                                ` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        container.innerHTML = tableHTML;
        
        // Add event listeners for role change dropdowns
        const roleSelects = container.querySelectorAll('.role-select');
        roleSelects.forEach(select => {
            select.addEventListener('change', async (e) => {
                const username = e.target.dataset.username;
                const newRole = e.target.value;
                await this.changeUserRole(username, newRole);
            });
        });
        
        // Add event listeners for delete buttons
        const deleteButtons = container.querySelectorAll('button[data-action="delete"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                const username = e.target.dataset.username;
                await this.removeUser(username);
            });
        });
    }

    async renderBarberUsers() {
        const container = document.getElementById('barber-users');
        
        if (!window.authSystem) {
            container.innerHTML = '<p>Loading...</p>';
            return;
        }
        
        const allUsers = await window.authSystem.getAllUsers();

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

    async changeUserRole(username, newRole) {
        const success = await window.authSystem.updateUserRole(username, newRole);
        if (success) {
            console.log(`Role updated: ${username} -> ${newRole}`);
            await this.renderAdminUsers();
        } else {
            console.error(`Failed to update role for ${username}`);
        }
    }

    async removeUser(username) {
        const success = await window.authSystem.deleteUser(username);
        if (success) {
            console.log(`User deleted: ${username}`);
            await this.renderAdminUsers();
        } else {
            console.error(`Failed to delete user: ${username}`);
        }
    }

    // Service Management for Barbers
    setupAddServiceForm() {
        console.log('Setting up barber service form');
        const form = document.getElementById('add-service-form');
        const nameSelector = document.getElementById('service-name-selector');
        const cancelBtn = document.getElementById('service-cancel-btn');
        
        if (!form || !nameSelector) {
            console.error('Barber service form elements not found:', { form, nameSelector });
            return;
        }
        
        console.log('Form elements found, loading services');
        // Load services from database and populate dropdown
        this.loadServicesFromDatabase('barber');
        
        // Handle service selection from dropdown
        nameSelector.addEventListener('change', (e) => {
            const serviceId = e.target.value;
            const formFields = document.getElementById('service-form-fields');
            if (serviceId) {
                formFields.style.display = 'block';
                this.loadServiceForEdit(parseInt(serviceId), 'barber');
            } else {
                formFields.style.display = 'none';
                this.resetServiceForm('barber');
            }
        });
        
        // Handle cancel button
        cancelBtn.addEventListener('click', () => {
            this.resetServiceForm('barber');
        });
        
        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const serviceId = document.getElementById('service-id').value;
            
            // Only allow editing existing services
            if (!serviceId) {
                alert('Please select a service to edit.');
                return;
            }
            
            const formData = new FormData();
            
            formData.append('name', document.getElementById('service-name').value);
            formData.append('description', document.getElementById('service-description').value);
            formData.append('price', document.getElementById('service-price').value);
            formData.append('duration', document.getElementById('service-duration').value);
            formData.append('display_order', 0);
            formData.append('action', 'edit');
            formData.append('id', serviceId);
            
            try {
                const response = await fetch('../BackEnd/services.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.resetServiceForm('barber');
                    this.loadServicesFromDatabase('barber');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save service');
            }
        });
    }

    // Service Management for Admin
    setupAdminServiceForm() {
        console.log('Setting up admin service form');
        const form = document.getElementById('admin-service-form');
        const nameSelector = document.getElementById('admin-service-name-selector');
        const cancelBtn = document.getElementById('admin-service-cancel-btn');
        
        if (!form || !nameSelector) {
            console.error('Admin service form elements not found:', { form, nameSelector });
            return;
        }
        
        console.log('Form elements found, loading services');
        // Load services from database and populate dropdown
        this.loadServicesFromDatabase('admin');
        
        // Handle service selection from dropdown
        nameSelector.addEventListener('change', (e) => {
            const serviceId = e.target.value;
            const formFields = document.getElementById('admin-service-form-fields');
            if (serviceId) {
                formFields.style.display = 'block';
                this.loadServiceForEdit(parseInt(serviceId), 'admin');
            } else {
                formFields.style.display = 'none';
                this.resetServiceForm('admin');
            }
        });
        
        // Handle cancel button
        cancelBtn.addEventListener('click', () => {
            this.resetServiceForm('admin');
        });
        
        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const serviceId = document.getElementById('admin-service-id').value;
            
            // Only allow editing existing services
            if (!serviceId) {
                alert('Please select a service to edit.');
                return;
            }
            
            const formData = new FormData();
            
            formData.append('name', document.getElementById('admin-service-name').value);
            formData.append('description', document.getElementById('admin-service-description').value);
            formData.append('price', document.getElementById('admin-service-price').value);
            formData.append('duration', document.getElementById('admin-service-duration').value);
            formData.append('display_order', 0);
            formData.append('action', 'edit');
            formData.append('id', serviceId);
            
            try {
                const response = await fetch('../BackEnd/services.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    this.resetServiceForm('admin');
                    this.loadServicesFromDatabase('admin');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save service');
            }
        });
    }
    
    async loadServicesFromDatabase(panel) {
        try {
            console.log('Loading services for panel:', panel);
            const response = await fetch('../BackEnd/services.php?action=getAll');
            const text = await response.text();
            console.log('Raw response:', text);
            
            const data = JSON.parse(text);
            console.log('Services response:', data);
            
            if (data.success && data.services) {
                console.log('Populating dropdown with services:', data.services);
                this.populateServiceDropdown(data.services, panel);
            } else {
                console.log('No services found or error:', data.message);
            }
        } catch (error) {
            console.error('Error loading services:', error);
        }
    }
    
    populateServiceDropdown(services, panel) {
        const prefix = panel === 'admin' ? 'admin-' : '';
        const selector = document.getElementById(`${prefix}service-name-selector`);
        console.log('Selector element:', selector);
        console.log('Services to populate:', services);
        
        if (!selector) {
            console.error('Service selector not found for panel:', panel);
            return;
        }
        
        selector.innerHTML = '<option value="">-- Select a Service to Edit --</option>';
        
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.name;
            selector.appendChild(option);
        });
        
        console.log('Dropdown populated with', services.length, 'services');
    }
    
    async renderServiceEditHistory() {
        console.log('renderServiceEditHistory called');
        const container = document.getElementById('service-edit-history');
        console.log('Container element:', container);
        
        try {
            const response = await fetch('../BackEnd/services.php?action=getAll');
            const text = await response.text();
            console.log('History response:', text);
            const data = JSON.parse(text);
            
            if (!data.success || !data.services || data.services.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <p>No services found</p>
                    </div>
                `;
                return;
            }
            
            const tableHTML = `
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Created</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.services.map(service => {
                            const created = new Date(service.created_at);
                            const updated = new Date(service.updated_at);
                            const formattedCreated = created.toLocaleDateString() + ' ' + created.toLocaleTimeString();
                            const formattedUpdated = updated.toLocaleDateString() + ' ' + updated.toLocaleTimeString();
                            
                            return `
                                <tr>
                                    <td><strong>${service.name}</strong></td>
                                    <td>${formattedCreated}</td>
                                    <td>${formattedUpdated}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
        } catch (error) {
            console.error('Error loading service edit history:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <p style="color: red;">Error loading edit history</p>
                </div>
            `;
        }
    }    
    async renderBarberMessages() {
        const container = document.getElementById('barber-messages');
        
        try {
            const response = await fetch('../BackEnd/messages.php');
            const data = await response.json();
            
            if (!data.success || !data.messages || data.messages.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <p>No messages yet</p>
                    </div>
                `;
                return;
            }
            
            const tableHTML = `
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.messages.map(msg => {
                            const date = new Date(msg.created_at);
                            const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                            const statusClass = msg.status === 'read' ? 'status-completed' : 'status-pending';
                            
                            return `
                                <tr>
                                    <td><strong>${msg.name}</strong></td>
                                    <td>${msg.phone || 'N/A'}</td>
                                    <td>${msg.email}</td>
                                    <td>${msg.subject}</td>
                                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">${msg.message}</td>
                                    <td>${formattedDate}</td>
                                    <td><span class="status-badge ${statusClass}">${msg.status}</span></td>
                                    <td>
                                        <button class="action-btn" onclick="window.editSystem.toggleMessageStatus(${msg.id}, '${msg.status}')">
                                            ${msg.status === 'read' ? 'Mark Unread' : 'Mark Read'}
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
        } catch (error) {
            console.error('Error loading messages:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <p style="color: red;">Error loading messages</p>
                </div>
            `;
        }
    }
    
    async toggleMessageStatus(messageId, currentStatus) {
        const newStatus = currentStatus === 'read' ? 'unread' : 'read';
        
        try {
            const response = await fetch('../BackEnd/messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    messageId: messageId,
                    status: newStatus
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                await this.renderBarberMessages();
            } else {
                alert('Failed to update message status');
            }
        } catch (error) {
            console.error('Error updating message status:', error);
            alert('Error updating message status');
        }
    }    
    async loadServiceForEdit(serviceId, panel) {
        const prefix = panel === 'admin' ? 'admin-' : '';
        
        try {
            const response = await fetch(`../BackEnd/services.php?action=getById&id=${serviceId}`);
            const data = await response.json();
            
            if (data.success && data.service) {
                const service = data.service;
                document.getElementById(`${prefix}service-id`).value = service.id;
                document.getElementById(`${prefix}service-name-selector`).value = service.id;
                document.getElementById(`${prefix}service-name`).value = service.name;
                document.getElementById(`${prefix}service-description`).value = service.description || '';
                document.getElementById(`${prefix}service-price`).value = service.price || '';
                document.getElementById(`${prefix}service-duration`).value = service.duration || '';
                
                // Update UI
                document.getElementById(`${prefix}service-form-title`).textContent = 'Edit Service';
                document.getElementById(`${prefix}service-submit-btn`).textContent = 'Update Service';
                document.getElementById(`${prefix}service-cancel-btn`).style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Error loading service:', error);
            alert('Failed to load service details');
        }
    }
    
    resetServiceForm(panel) {
        const prefix = panel === 'admin' ? 'admin-' : '';
        const formId = panel === 'admin' ? 'admin-service-form' : 'add-service-form';
        const formFieldsId = panel === 'admin' ? 'admin-service-form-fields' : 'service-form-fields';
        
        const form = document.getElementById(formId);
        if (form) form.reset();
        
        const formFields = document.getElementById(formFieldsId);
        if (formFields) formFields.style.display = 'none';
        
        const serviceId = document.getElementById(`${prefix}service-id`);
        if (serviceId) serviceId.value = '';
        
        const selector = document.getElementById(`${prefix}service-name-selector`);
        if (selector) selector.value = '';
        
        const title = document.getElementById(`${prefix}service-form-title`);
        if (title) title.textContent = 'Edit Service';
        
        const submitBtn = document.getElementById(`${prefix}service-submit-btn`);
        if (submitBtn) submitBtn.textContent = 'Update Service';
        
        const cancelBtn = document.getElementById(`${prefix}service-cancel-btn`);
        if (cancelBtn) cancelBtn.style.display = 'none';
    }
}

// Initialize edit system after DOM and auth are ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded for edit page');
    
    // Wait for authSystem to be ready
    let attempts = 0;
    const checkAuth = setInterval(async () => {
        attempts++;
        console.log('Checking for authSystem, attempt:', attempts);
        
        if (window.authSystem) {
            console.log('AuthSystem found, initializing EditBookingSystem');
            clearInterval(checkAuth);
            const editSystem = new EditBookingSystem();
            window.editSystem = editSystem; // Make globally accessible
            await editSystem.init();
        } else if (attempts > 20) {
            console.error('AuthSystem not loaded after 20 attempts');
            clearInterval(checkAuth);
            alert('Authentication system failed to load. Please refresh the page.');
        }
    }, 100);
});
