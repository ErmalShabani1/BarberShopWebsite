<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .services-list {
            margin-top: 30px;
        }

        .services-list h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .service-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-info {
            flex: 1;
        }

        .service-name {
            font-weight: bold;
            color: #333;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .service-details {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .service-actions {
            display: flex;
            gap: 10px;
            margin-left: 20px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 15px;
            font-size: 12px;
        }

        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background-color: #138496;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .service-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .service-actions {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Services Management</h1>

        <!-- Messages -->
        <div id="message" class="message"></div>

        <!-- Add/Edit Service Form -->
        <div class="form-section">
            <h2 style="margin-bottom: 20px; color: #333;" id="formTitle">Add New Service</h2>
            <form id="serviceForm">
                <input type="hidden" id="serviceId" value="">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Service Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" min="0">
                    </div>
                    <div class="form-group">
                        <label for="displayOrder">Display Order</label>
                        <input type="number" id="displayOrder" name="display_order" value="0" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="icon">Icon Class (e.g., fa-scissors)</label>
                        <input type="text" id="icon" name="icon" placeholder="e.g., fa-scissors">
                    </div>
                    <div class="form-group">
                        <label for="imageUrl">Image URL</label>
                        <input type="text" id="imageUrl" name="image_url">
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary" id="submitBtn">Add Service</button>
                    <button type="button" class="btn-secondary" id="cancelBtn" style="display: none;">Cancel Edit</button>
                </div>
            </form>
        </div>

        <!-- Services List -->
        <div class="services-list">
            <h2>Services List</h2>
            <div id="servicesList" class="loading">
                <div class="spinner"></div>
                <p>Loading services...</p>
            </div>
        </div>
    </div>

    <script>
        // API endpoint
        const apiUrl = '../BackEnd/services.php';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadServices();
            document.getElementById('serviceForm').addEventListener('submit', handleFormSubmit);
            document.getElementById('cancelBtn').addEventListener('click', resetForm);
        });

        // Load all services
        function loadServices() {
            fetch(`${apiUrl}?action=getAll`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.services) {
                        displayServices(data.services);
                    } else {
                        showMessage('No services found', 'error');
                        document.getElementById('servicesList').innerHTML = '<p>No services found. Create one to get started!</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading services:', error);
                    showMessage('Failed to load services', 'error');
                });
        }

        // Display services in list
        function displayServices(services) {
            const container = document.getElementById('servicesList');
            container.innerHTML = '';

            if (services.length === 0) {
                container.innerHTML = '<p>No services found. Create one to get started!</p>';
                return;
            }

            services.forEach(service => {
                const serviceDiv = document.createElement('div');
                serviceDiv.className = 'service-item';
                serviceDiv.innerHTML = `
                    <div class="service-info">
                        <div class="service-name">${service.name}</div>
                        <div class="service-details">
                            ${service.description ? `<p><strong>Description:</strong> ${service.description}</p>` : ''}
                            ${service.price ? `<p><strong>Price:</strong> $${parseFloat(service.price).toFixed(2)}</p>` : ''}
                            ${service.duration ? `<p><strong>Duration:</strong> ${service.duration} minutes</p>` : ''}
                            <p><strong>Display Order:</strong> ${service.display_order}</p>
                        </div>
                    </div>
                    <div class="service-actions">
                        <button class="btn-edit" onclick="editService(${service.id})">Edit</button>
                        <button class="btn-delete" onclick="deleteService(${service.id})">Delete</button>
                    </div>
                `;
                container.appendChild(serviceDiv);
            });
        }

        // Handle form submission
        function handleFormSubmit(e) {
            e.preventDefault();

            const serviceId = document.getElementById('serviceId').value;
            const formData = new FormData(document.getElementById('serviceForm'));
            
            // Add action to form data
            if (serviceId) {
                formData.append('action', 'edit');
                formData.append('id', serviceId);
            } else {
                formData.append('action', 'add');
            }

            fetch(apiUrl, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    resetForm();
                    loadServices();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Failed to save service', 'error');
            });
        }

        // Edit service
        function editService(id) {
            fetch(`${apiUrl}?action=getById&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.service) {
                        const service = data.service;
                        document.getElementById('serviceId').value = service.id;
                        document.getElementById('name').value = service.name;
                        document.getElementById('description').value = service.description || '';
                        document.getElementById('price').value = service.price || '';
                        document.getElementById('duration').value = service.duration || '';
                        document.getElementById('icon').value = service.icon || '';
                        document.getElementById('imageUrl').value = service.image_url || '';
                        document.getElementById('displayOrder').value = service.display_order || 0;

                        document.getElementById('formTitle').textContent = 'Edit Service';
                        document.getElementById('submitBtn').textContent = 'Update Service';
                        document.getElementById('cancelBtn').style.display = 'inline-block';

                        // Scroll to form
                        document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to load service details', 'error');
                });
        }

        // Delete service
        function deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                fetch(apiUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadServices();
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to delete service', 'error');
                });
            }
        }

        // Reset form
        function resetForm() {
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceId').value = '';
            document.getElementById('formTitle').textContent = 'Add New Service';
            document.getElementById('submitBtn').textContent = 'Add Service';
            document.getElementById('cancelBtn').style.display = 'none';
        }

        // Show message
        function showMessage(text, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.textContent = text;
            msgDiv.className = 'message ' + type;
            
            setTimeout(() => {
                msgDiv.className = 'message';
            }, 5000);
        }
    </script>
</body>
</html>
