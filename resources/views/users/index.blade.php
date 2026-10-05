<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Lists - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .user-role {
            font-size: 13px;
            color: #7f8c8d;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #d0d0d0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .page-content {
            padding: 40px;
            flex: 1;
        }

        .search-container {
            margin-bottom: 30px;
            max-width: 500px;
        }

        .search-box {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 10px 50px 15px 20px;
            border: 2px solid #d0d0d0;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #003f5c;
        }

        .search-input::placeholder {
            color: #a0a0a0;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 35px;
            height: 35px;
            color: #7f8c8d;
            pointer-events: none;
        }

        .table-container {
            background: white;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: white;
        }

        th {
            padding: 25px 20px;
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 25px 20px;
            text-align: center;
            color: #2c3e50;
            font-size: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .status {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
        }

        .status.active {
            background: #e5f5f0;
            color: #0c7a5a;
        }

        .status.inactive {
            background: #ffe5e5;
            color: #c0392b;
        }

        .action-btn {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            opacity: 0.7;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .action-icon.update {
            color: #3498db;
        }

        .action-label {
            font-size: 11px;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .action-icon img {
		    width: 100%;
		    height: 100%;
		    object-fit: contain;
		    display: block; /* removes default inline gap */
		}

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            font-size: 16px;
        }

        .success-message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #80cbc4;
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            opacity: 0;
            display: block;
        }
        
        .success-message.show {
            animation: slideDown 0.4s ease forwards;
        }
        
        .success-message.hide {
            animation: slideUpFade 0.4s ease forwards;
        }
        
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideUpFade {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            to {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .user-details-modal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            background: white;
            padding: 30px 40px 20px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }

        .modal-title {
            font-size: 28px;
            font-weight: 700;
            color: #ff8c42;
            text-align: center;
            margin-bottom: 10px;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 32px;
            color: #7f8c8d;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            color: #2c3e50;
        }

        .modal-body {
            padding: 30px 40px;
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-picture-container {
            position: relative;
            margin-bottom: 10px;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #d0d0d0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: #7f8c8d;
            overflow: hidden;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-input {
            padding: 12px 16px;
            border: 2px solid #d0d0d0;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #2c3e50;
            background: #f5f5f5;
            transition: all 0.3s ease;
        }

        .form-input:disabled {
            background: #e8e8e8;
            cursor: not-allowed;
            color: #666;
        }

        .form-input:not(:disabled) {
            background: white;
        }

        .form-input:not(:disabled):focus {
            outline: none;
            border-color: #003f5c;
        }

        .form-select {
            padding: 12px 16px;
            border: 2px solid #d0d0d0;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #2c3e50;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: #003f5c;
        }
        
        .form-select:disabled {
            background: #e8e8e8;
            cursor: not-allowed;
            color: #666;
        }

        .file-upload-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .file-upload-btn {
            display: inline-block;
            width: 100%;
            padding: 8px 15px;
            text-align: left;
            background: white;
            color: #2c3e50;
            border-radius: 8px;
            border: 2px solid #d0d0d0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .file-upload-btn:hover {
            border-color: #003f5c;
            background: #f8f9fa;
        }

        .file-upload-btn.disabled {
            background: #d0d0d0;
            cursor: not-allowed;
            color: #7f8c8d;
        }

        .file-input {
            display: none;
        }

        .file-name {
            margin-top: 8px;
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .confirmation-modal {
		    display: none;
		    position: fixed;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(0, 0, 0, 0.5);
		    z-index: 10000;
		    justify-content: center;
		    align-items: center;
		}
		
		.confirmation-modal.active {
		    display: flex;
		}
		
		.confirmation-content {
		    background: white;
		    padding: 30px 40px;
		    border-radius: 12px;
		    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
		    text-align: center;
		    min-width: 400px;
		}
		
		.confirmation-icon {
		    font-size: 60px;
		    margin-bottom: 20px;
		}
		
		.confirmation-message {
		    font-size: 18px;
		    color: #2c3e50;
		    margin-bottom: 30px;
		    font-weight: 600;
		}
		
		.confirmation-buttons {
		    display: flex;
		    gap: 15px;
		    justify-content: center;
		}
		
		.confirmation-btn {
		    padding: 12px 40px;
		    border: none;
		    border-radius: 8px;
		    font-size: 15px;
		    font-weight: 600;
		    cursor: pointer;
		    font-family: 'Inter', sans-serif;
		}
		
		.confirmation-btn-no {
		    background: #95a5a6;
		    color: white;
		}
		
		.confirmation-btn-yes {
		    background: #003f5c;
		    color: white;
		}

        .update-btn {
            display: block;
            width: 200px;
            margin: 30px auto 0;
            padding: 15px 40px;
            background: #003f5c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .update-btn:hover {
            background: #002d42;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 200px;
            }

            .page-content {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 15px 10px;
                font-size: 14px;
            }
        }
        
        .search-and-filter {
		    display: flex;
		    gap: 20px;
		    margin-bottom: 30px;
		    align-items: center;
		}
		
		.search-container {
		    flex: 1;
		    max-width: 500px;
		    margin-bottom: 0;
		}
		
		.filter-container {
		    display: flex;
		    align-items: center;
		    gap: 10px;
		}
		
		.filter-label {
		    font-size: 15px;
		    font-weight: 500;
		    color: #2c3e50;
		    white-space: nowrap;
		}
		
		.filter-select {
		    padding: 10px 30px 10px 15px;
		    border: 2px solid #d0d0d0;
		    border-radius: 10px;
		    font-size: 15px;
		    font-family: 'Inter', sans-serif;
		    background: white;
		    color: #2c3e50;
		    cursor: pointer;
		    transition: all 0.3s ease;
		    min-width: 150px;
		}
		
		.filter-select:focus {
		    outline: none;
		    border-color: #003f5c;
		}
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'User List'])

        <div class="page-content">
		    <div class="search-and-filter">
		        <div class="search-container">
		            <div class="search-box">
		                <input type="text" class="search-input" id="searchInput" placeholder="Search" onkeyup="filterUsers()">
		                <img src="{{ asset('images/icons/search-icon.png') }}" alt="search" class="search-icon">
		            </div>
		        </div>
		        
		        <div class="filter-container">
		            <label class="filter-label">Status:</label>
		            <select class="filter-select" id="statusFilter" onchange="filterUsers()">
		                <option value="all">All</option>
		                <option value="active">Active</option>
		                <option value="inactive">Inactive</option>
		            </select>
		        </div>
		    </div>
		
		    <div class="table-container">
		        <table>
		            <thead>
		                <tr>
		                    <th>Staff ID</th>
		                    <th>Name</th>
		                    <th>Roles</th>
		                    <th>Status</th>
		                    <th>Manage By</th>
		                    <th>Action</th>
		                </tr>
		            </thead>
		            <tbody id="userTableBody"></tbody>
		        </table>
		    </div>
		</div>
    </div>

    <div class="success-message" id="successMessage"></div>

    <!-- User Details Modal -->
    <div class="modal-overlay" id="userDetailsModal">
        <div class="user-details-modal">
            <div class="modal-header">
                <h2 class="modal-title">Update User Information Details</h2>
                <button type="button" class="close-btn" onclick="closeUserDetailsModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="profile-section">
                    <div class="profile-picture-container">
                        <div class="profile-picture" id="profilePicture">👤</div>
                    </div>
                    <div class="profile-label">Profile Picture</div>
                </div>

                <form id="userDetailsForm" action="{{ route('users.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-input" id="userName" name="editName" disabled>
                        </div>
                        <input type="hidden" id="hiddenStaffId" name="editStaffId" value="">
                        <div class="form-group">
                            <label class="form-label">Staff ID</label>
                            <input type="text" class="form-input" id="userStaffId" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Roles</label>
                            <!-- Role field is DISABLED - managers cannot change roles -->
                            <select class="form-select" id="userRole" name="editRole" disabled>
                                <option value="Senior Finance Manager">Senior Finance Manager</option>
                                <option value="Finance Executive">Finance Executive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" id="userEmail" name="editEmail" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Number Phone</label>
                            <input type="text" class="form-input" id="userPhone" name="editPhone" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="userStatus" name="editStatus" onchange="toggleReasonField()">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Manage By</label>
                            <input type="text" class="form-input" id="userManageBy" name="editManageBy" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-input" id="userPassword" value="********" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reason</label>
                            <div class="file-upload-container">
                                <label for="reasonFile" class="file-upload-btn" id="fileUploadBtn">
                                    Choose File
                                    <span style="font-size: 11px; display: block; margin-top: 2px;">JPEG, PNG, PDF</span>
                                </label>
                                <input type="file" id="reasonFile" name="editReasonFile" class="file-input" accept=".jpg,.jpeg,.png,.pdf" onchange="displayFileName()">
                                <div class="file-name" id="fileName"></div>
                                <input type="hidden" id="reasonText" name="editReason">
                            </div>
                            <div id="reasonError" style="display:none; color:#c0392b; font-size:13px; margin-top:4px;">This field is required.</div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" id="userAddress" name="editAddress" disabled>
                        </div>
                    </div>

                    <button type="button" class="update-btn" onclick="showConfirmation()">Update</button>
                  
                </form>
            </div>
        </div>
    </div>
    
    <div class="confirmation-modal" id="confirmationModal">
    <div class="confirmation-content">
        <div class="confirmation-icon">⚠️</div>
        <div class="confirmation-message">
            Are you sure you want to update this user information?
        </div>
        <div class="confirmation-buttons">
            <button class="confirmation-btn confirmation-btn-no" onclick="closeConfirmation()">No</button>
            <button class="confirmation-btn confirmation-btn-yes" onclick="confirmUpdate()">Yes</button>
        </div>
    </div>
	</div>

    <script>
        // Load users from database
        const users = @json($users);
        
        console.log("========================================");
        console.log("📊 Loaded " + users.length + " users from database");
        if (users.length > 0) {
            console.log("✅ First user:", users[0]);
        } else {
            console.error("❌ No users loaded!");
        }
        console.log("========================================");

        document.addEventListener('DOMContentLoaded', function() {
            loadUsers(users);
        });

        function loadUsers(userList) {
            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = '';

            if (userList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="no-data">No users found.</td></tr>';
                return;
            }

            userList.forEach(user => {
                const row = document.createElement('tr');
                const statusClass = user.status.toLowerCase();

                row.innerHTML = 
                    '<td>' + escapeHtml(user.staffId) + '</td>' +
                    '<td>' + escapeHtml(user.name) + '</td>' +
                    '<td>' + escapeHtml(user.role) + '</td>' +
                    '<td><span class="status ' + escapeHtml(statusClass) + '">' + escapeHtml(user.status) + '</span></td>' +
                    '<td>' + escapeHtml(user.manageBy) + '</td>' +
                    '<td>' +
	                    '<div class="action-buttons">' +
	                    '<div class="action-btn" onclick="updateUser(\'' + user.staffId + '\')">' +
	                        '<div class="action-icon update">' +
	                            '<img src="{{ asset('images/icons/update-icon.png') }}" alt="Update" style="width:100px; height:90px;">' +
	                        '</div>' +
	                        '<div class="action-label">Update</div>' +
	                    '</div>' +
                '</div>' +
                    '</td>';
                tbody.appendChild(row);
            });
            
            console.log("✅ Table populated with " + userList.length + " rows");
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : text;
            return div.innerHTML;
        }

        function searchUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filtered = users.filter(user => {
                return user.staffId.toString().toLowerCase().includes(searchTerm) ||
                       user.name.toLowerCase().includes(searchTerm) ||
                       user.role.toLowerCase().includes(searchTerm) ||
                       user.status.toLowerCase().includes(searchTerm) ||
                       user.manageBy.toLowerCase().includes(searchTerm);
            });
            loadUsers(filtered);
        }
        
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            
            const filtered = users.filter(user => {
                // Search filter
                const matchesSearch = user.staffId.toString().toLowerCase().includes(searchTerm) ||
                                     user.name.toLowerCase().includes(searchTerm) ||
                                     user.role.toLowerCase().includes(searchTerm) ||
                                     user.status.toLowerCase().includes(searchTerm) ||
                                     user.manageBy.toLowerCase().includes(searchTerm);
                
                // Status filter
                const matchesStatus = statusFilter === 'all' || user.status.toLowerCase() === statusFilter;
                
                return matchesSearch && matchesStatus;
            });
            
            loadUsers(filtered);
            
            console.log("🔍 Filter applied - Search: '" + searchTerm + "', Status: '" + statusFilter + "', Results: " + filtered.length);
        }

        function updateUser(staffId) {
            const user = users.find(u => u.staffId == staffId);
            if (!user) return;
            
            console.log("Opening modal for user:", user.name);
            
            // Store current user
            window.currentUpdateUser = user;
            
            // Populate modal fields
            document.getElementById('userName').value = user.name;
            document.getElementById('userStaffId').value = user.staffId;
            document.getElementById("hiddenStaffId").value = user.numericId; // Send numeric ID to servlet
            document.getElementById('userRole').value = user.role;
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userPhone').value = user.phone || '';
            document.getElementById('userStatus').value = user.status;
            document.getElementById('userManageBy').value = user.manageBy;
            document.getElementById('userAddress').value = user.address || '';
            
            // Set reason if exists
            if (user.reason) {
                document.getElementById('fileName').textContent = user.reason;
                document.getElementById('reasonText').value = user.reason;
            } else {
                document.getElementById('fileName').textContent = '';
                document.getElementById('reasonText').value = '';
            }
            
            // Clear file input
            document.getElementById('reasonFile').value = '';
            
            // Toggle reason field based on status
            toggleReasonField();
            
            // Show modal
            document.getElementById('userDetailsModal').classList.add('active');
        }

        function closeUserDetailsModal() {
            document.getElementById('userDetailsModal').classList.remove('active');
            window.currentUpdateUser = null;
        }

        function toggleReasonField() {
            const status = document.getElementById('userStatus').value;
            const fileInput = document.getElementById('reasonFile');
            const fileUploadBtn = document.getElementById('fileUploadBtn');
            
            if (status === 'Inactive') {
                fileInput.disabled = false;
                fileUploadBtn.classList.remove('disabled');
                fileUploadBtn.style.pointerEvents = 'auto';
            } else {
                fileInput.disabled = true;
                fileInput.value = '';
                document.getElementById('fileName').textContent = '';
                fileUploadBtn.classList.add('disabled');
                fileUploadBtn.style.pointerEvents = 'none';
            }
        }

        function displayFileName() {
            const fileInput = document.getElementById('reasonFile');
            const fileName = document.getElementById('fileName');
            
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                document.getElementById('reasonText').value = fileInput.files[0].name;
            } else {
                fileName.textContent = '';
                document.getElementById('reasonText').value = '';
            }
        }

        function showSuccessMessage(message) {
            const successMsg = document.getElementById('successMessage');
            successMsg.textContent = message;
            
            successMsg.classList.add('show');
            successMsg.classList.remove('hide');

            setTimeout(function() {
                successMsg.classList.remove('show');
                successMsg.classList.add('hide');
                
                setTimeout(function() {
                    successMsg.classList.remove('hide');
                }, 400);
            }, 3000);
        }
        
        // Check for success parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success') === 'true') {
            const numericStaffId = urlParams.get('staffId'); const matchedUser = users ? users.find(u => String(u.numericId) === String(numericStaffId)) : null; const displayId = matchedUser ? matchedUser.staffId : (numericStaffId || 'Staff'); showSuccessMessage(displayId + ' has been updated!');
        }
        
        function showConfirmation() {
            const staffId = document.getElementById('hiddenStaffId').value;
            const status = document.getElementById('userStatus').value;
            
            // Clear previous errors
            clearUserError();

            if (!staffId) {
                showUserError('Error: Staff ID is missing. Please select a user first.');
                return;
            }
            
            if (status === 'Inactive') {
                const reasonFile = document.getElementById('reasonFile').files[0];
                const reasonText = document.getElementById('reasonText').value;
                
                if (!reasonFile && !reasonText) {
                    document.getElementById('reasonError').style.display = 'block';
                    document.getElementById('fileUploadBtn').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            }
            
            document.getElementById('confirmationModal').classList.add('active');
        }

        function showUserError(message) {
            let errEl = document.getElementById('userFormError');
            if (!errEl) {
                errEl = document.createElement('div');
                errEl.id = 'userFormError';
                errEl.style.cssText = 'color:#c0392b; background:#fdecea; padding:10px 15px; border-radius:8px; font-size:14px; margin-bottom:10px;';
                const form = document.getElementById('userDetailsForm');
                if (form) form.insertBefore(errEl, form.firstChild);
            }
            errEl.textContent = message;
            errEl.style.display = 'block';
        }

        function clearUserError() {
            const errEl = document.getElementById('userFormError');
            if (errEl) errEl.style.display = 'none';
            const reasonErr = document.getElementById('reasonError');
            if (reasonErr) reasonErr.style.display = 'none';
        }

        function closeConfirmation() {
            document.getElementById('confirmationModal').classList.remove('active');
        }

        function confirmUpdate() {
            closeConfirmation();
            document.getElementById('userDetailsForm').submit();
        }

        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmation();
            }
        });
    </script>
</body>
</html>
