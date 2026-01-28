<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Edit Activity</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .main-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 30px 0;
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #d4a574;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .header-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .brand-title {
            color: #d4a574;
            font-size: 2.5em;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .main-nav {
            background: #1a1a1a;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            padding: 15px 25px;
            display: block;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #d4a574;
            border-bottom-color: #d4a574;
            background: rgba(212, 165, 116, 0.1);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-title {
            color: #fff;
            font-size: 2em;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #d4a574;
            box-shadow: 0 0 5px rgba(212, 165, 116, 0.3);
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #d4a574;
            color: #1a1a1a;
        }

        .btn-primary:hover {
            background: #c49558;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 165, 116, 0.3);
        }

        .btn-secondary {
            background: #2a5298;
            color: white;
        }

        .btn-secondary:hover {
            background: #1e3c72;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #d4a574;
        }

        .summary-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-card .count {
            font-size: 2.5em;
            color: #d4a574;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-card .last-edit {
            color: #666;
            font-size: 0.85em;
        }

        .edits-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #1a1a1a;
            font-size: 1.5em;
            margin-bottom: 20px;
            border-bottom: 2px solid #d4a574;
            padding-bottom: 10px;
        }

        .edits-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .edits-table thead {
            background: #f5f5f5;
        }

        .edits-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #d4a574;
        }

        .edits-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        .edits-table tbody tr:hover {
            background: #f9f9f9;
        }

        .action-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-create {
            background: #d4edda;
            color: #155724;
        }

        .action-update {
            background: #cce5ff;
            color: #004085;
        }

        .action-delete {
            background: #f8d7da;
            color: #721c24;
        }

        .entity-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
            background: #e2e3e5;
            color: #383d41;
        }

        .timestamp {
            color: #999;
            font-size: 0.9em;
        }

        .user-name {
            color: #d4a574;
            font-weight: 600;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #c3e6cb;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            border-color: #d4a574;
            background: #f9f9f9;
        }

        .pagination button.active {
            background: #d4a574;
            color: white;
            border-color: #d4a574;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 10px;
            }

            .brand-title {
                font-size: 1.8em;
            }

            .nav-menu {
                flex-direction: column;
            }

            .nav-link {
                padding: 12px 15px;
                border-bottom: none;
                border-left: 3px solid transparent;
            }

            .nav-link.active {
                border-left-color: #d4a574;
                border-bottom: none;
            }

            .filters-section {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-group select,
            .filter-group input {
                width: 100%;
            }

            .edits-table {
                font-size: 0.9em;
            }

            .edits-table th,
            .edits-table td {
                padding: 8px;
            }

            .summary-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="header-content">
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
            <h1 class="brand-title">BARBERSHOP ADMIN</h1>
            <img src="../images/image1.jpg" class="logo" alt="Barbershop Logo">
        </div>
    </header>


    <nav class="main-nav">
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="Booking.php" class="nav-link">Booking</a></li>
            <li><a href="View.php" class="nav-link">View Barbers</a></li>
            <li><a href="kontakti.php" class="nav-link">Contact</a></li>
            <li><a href="edit.php" class="nav-link">Edit / Cancel</a></li>
            <li><a href="admin_dashboard.php" class="nav-link active">Admin Dashboard</a></li>
        </ul>
    </nav>

   
    <div class="dashboard-container">
        <h1 class="dashboard-title">📊 Edit Activity Dashboard</h1>

       
        <div class="filters-section">
            <div class="filter-group">
                <label for="entityTypeFilter">Entity Type:</label>
                <select id="entityTypeFilter">
                    <option value="">All</option>
                    <option value="services">Services</option>
                    <option value="appointments">Appointments</option>
                    <option value="users">Users</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="actionFilter">Action:</label>
                <select id="actionFilter">
                    <option value="">All</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                </select>
            </div>
            <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            <button class="btn btn-secondary" onclick="refreshData()">Refresh</button>
        </div>

        <div class="summary-section" id="summarySection"></div>

        <div class="edits-section">
            <h2 class="section-title">Recent Edit Activity</h2>
            <div id="messageContainer"></div>
            <div id="loadingIndicator" class="loading" style="display: none;">Loading...</div>
            <table class="edits-table" id="editsTable" style="display: none;">
                <thead>
                    <tr>
                        <th>Edited By</th>
                        <th>Entity Type</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
            <div id="noDataMessage" class="no-data" style="display: none;">
                No edit activity found.
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const itemsPerPage = 20;
        let allEdits = [];

       
        document.addEventListener('DOMContentLoaded', () => {
            loadSummary();
            refreshData();
        });

        function loadSummary() {
            fetch('../BackEnd/edit_logs.php?action=getSummary')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.summary) {
                        displaySummary(data.summary);
                    }
                })
                .catch(error => console.error('Error loading summary:', error));
        }

        function displaySummary(summary) {
            const summarySection = document.getElementById('summarySection');
            summarySection.innerHTML = '';

            summary.forEach(item => {
                const lastEdit = new Date(item.last_edit).toLocaleString();
                const card = document.createElement('div');
                card.className = 'summary-card';
                card.innerHTML = `
                    <h3>${item.entity_type}</h3>
                    <div class="count">${item.edit_count}</div>
                    <div class="last-edit">Last edit: ${lastEdit}</div>
                `;
                summarySection.appendChild(card);
            });
        }

        function refreshData() {
            currentPage = 1;
            loadEdits();
        }

        function loadEdits() {
            const messageContainer = document.getElementById('messageContainer');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const editsTable = document.getElementById('editsTable');
            const noDataMessage = document.getElementById('noDataMessage');

            messageContainer.innerHTML = '';
            loadingIndicator.style.display = 'block';
            editsTable.style.display = 'none';
            noDataMessage.style.display = 'none';

            const entityType = document.getElementById('entityTypeFilter').value;
            const limit = 1000; 

            let url = `../BackEnd/edit_logs.php?action=getRecentEdits&limit=${limit}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.style.display = 'none';

                    if (data.success) {
                        allEdits = data.edits || [];
                        applyFilters();
                    } else {
                        messageContainer.innerHTML = `<div class="error-message">Error loading edits: ${data.message}</div>`;
                        noDataMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingIndicator.style.display = 'none';
                    messageContainer.innerHTML = `<div class="error-message">Error loading data</div>`;
                    noDataMessage.style.display = 'block';
                });
        }

        function applyFilters() {
            const entityTypeFilter = document.getElementById('entityTypeFilter').value;
            const actionFilter = document.getElementById('actionFilter').value;
            const editsTable = document.getElementById('editsTable');
            const noDataMessage = document.getElementById('noDataMessage');
            const tableBody = document.getElementById('tableBody');

        
            let filtered = allEdits.filter(edit => {
                const matchesType = !entityTypeFilter || edit.entity_type === entityTypeFilter;
                const matchesAction = !actionFilter || edit.action === actionFilter;
                return matchesType && matchesAction;
            });

            if (filtered.length === 0) {
                editsTable.style.display = 'none';
                noDataMessage.style.display = 'block';
                document.getElementById('pagination').innerHTML = '';
                return;
            }

        
            const totalPages = Math.ceil(filtered.length / itemsPerPage);
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedData = filtered.slice(start, end);

      
            tableBody.innerHTML = '';
            paginatedData.forEach(edit => {
                const row = document.createElement('tr');
                const actionClass = `action-${edit.action}`;
                const dateTime = new Date(edit.created_at).toLocaleString();

                row.innerHTML = `
                    <td><span class="user-name">${edit.edited_by}</span></td>
                    <td><span class="entity-badge">${edit.entity_type}</span></td>
                    <td><span class="action-badge ${actionClass}">${edit.action}</span></td>
                    <td>${edit.change_description || 'N/A'}</td>
                    <td><span class="timestamp">${dateTime}</span></td>
                `;
                tableBody.appendChild(row);
            });

            editsTable.style.display = 'table';
            noDataMessage.style.display = 'none';

            displayPagination(totalPages);
        }

        function displayPagination(totalPages) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    applyFilters();
                }
            };
            pagination.appendChild(prevBtn);

          
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className = currentPage === i ? 'active' : '';
                    btn.onclick = () => {
                        currentPage = i;
                        applyFilters();
                    };
                    pagination.appendChild(btn);
                } else if (i === 2 || i === totalPages - 1) {
                    const btn = document.createElement('button');
                    btn.textContent = '...';
                    btn.disabled = true;
                    pagination.appendChild(btn);
                }
            }

           
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    applyFilters();
                }
            };
            pagination.appendChild(nextBtn);
        }
    </script>
</body>
</html>
