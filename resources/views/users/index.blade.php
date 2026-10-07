<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Lists - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/users/index.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/users/index.js',
    ])
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

    {{-- Values for resources/js/pages/users/index.js --}}
    <script>
        window.pageData = {
            users: @json($users),
            updateIcon: '{{ asset('images/icons/update-icon.png') }}',
        };
    </script>
</body>
</html>
