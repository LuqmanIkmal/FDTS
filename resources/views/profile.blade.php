<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fixed Deposit Tracking System</title>
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
            max-height: 100vh;
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
            font-size: 1.8rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info { text-align: right; }
        .user-name { font-weight: 600; color: #2c3e50; font-size: 16px; }
        .user-role { font-size: 13px; color: #7f8c8d; }

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
            display: flex;
            gap: 1px;
            margin-top: -18px;
        }

        .profile-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 60px;
            margin-top: 140px;
            gap: 10px;
        }

        .profile-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: #d0d0d0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-greeting {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 15px;
        }

        .profile-right {
            flex: 1;
            margin-left: 80px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .info-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
        }

        .info-group.full-width {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .info-value {
            padding: 12px 16px;
            border: 2px solid #d0d0d0;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #2c3e50;
            background: white;
            transition: all 0.3s ease;
        }

        .info-value:focus {
            outline: none;
            border-color: #003f5c;
        }

        .info-value:read-only {
            background: #f5f5f5;
        }

        /* Password field with toggle */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper .info-value {
            flex: 1;
            padding-right: 45px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
        }

        .password-toggle img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            transition: opacity 0.3s;
        }

        .password-toggle:hover img {
            opacity: 0.7;
        }

        /* Change Password Link */
        .password-actions {
            display: flex;
            gap: 15px;
            margin-top: 8px;
        }

        .change-password-link {
            display: inline-block;
            color: #003f5c;
            font-size: 14px;
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.3s;
        }

        .change-password-link:hover {
            color: #002d42;
        }

        .cancel-password-link {
            display: none;
            color: #003f5c;
            font-size: 14px;
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.3s;
        }

        .cancel-password-link.show {
            display: inline-block;
        }

        .cancel-password-link:hover {
            color: #002d42;
        }

        /* Hidden Re-confirm Password Field */
        .reconfirm-password-group {
            display: none;
            animation: slideDown 0.3s ease;
        }

        .reconfirm-password-group.show {
            display: flex;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Error message for password mismatch */
        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .file-upload-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .file-upload-btn {
            padding: 6px 16px;
            background: white;
            color: #2c3e50;
            border-radius: 8px;
            border: 2px solid #d0d0d0;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .file-upload-btn:hover {
            border-color: #003f5c;
            background: #f8f9fa;
        }

        .file-icon {
            font-size: 24px;
        }

        .file-info {
            font-size: 13px;
            color: #7f8c8d;
        }

        .file-input {
            display: none;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .btn-update {
            padding: 10px 30px;
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

        .btn-update:hover {
            background: #002d42;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 63, 92, 0.3);
        }

        .btn-update:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
        }

        /* Success Message */
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
            margin-left: 100px;
            margin-top: -50px;
            opacity: 0;
            display: block;
        }
        
        .success-message.show {
            animation: slideDownMsg 0.4s ease forwards;
        }
        
        .success-message.hide {
            animation: slideUpFade 0.4s ease forwards;
        }
        
        @keyframes slideDownMsg {
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

        /* Modal Styles */
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

        .modal-content {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            min-width: 400px;
        }

        .modal-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .modal-message {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .modal-btn-no {
            background: #95a5a6;
            color: white;
        }

        .modal-btn-no:hover {
            background: #7f8c8d;
        }

        .modal-btn-yes {
            background: #003f5c;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #002d42;
        }

        @media (max-width: 1024px) {
            .page-content {
                flex-direction: column;
                align-items: center;
            }

            .profile-left {
                margin: 0;
            }

            .profile-right {
                margin: 40px 0 0 0;
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
   		@include('partials.header', ['pageTitle' => 'Profile'])

        <div class="page-content">
            <div class="profile-left">
                <div class="profile-avatar" id="profileAvatar">
                    @if ($user->staff_picture)
                        <img src="{{ route('profile.picture', ['staffId' => $user->staff_id]) }}" alt="User Avatar">
                    @else
                        <img src="{{ asset('images/icons/user.jpg') }}" alt="User Avatar">
                    @endif
                </div>
                <div class="profile-greeting">Hi, {{ $user->staff_name }} !</div>
            </div>

            <div class="profile-right">
                <div class="info-card">
                    <h2 class="info-title">Your Information Details</h2>

                    <!-- FORM SUBMITS TO ProfileController -->
                    <form id="profileForm" action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" novalidate>
                        @csrf
                        <!-- Hidden field for Staff ID -->
                        <input type="hidden" name="staffId" value="{{ $user->staff_id }}">
                        
                        <div class="info-grid">
                            <div class="info-group">
                                <label class="info-label">Name</label>
                                <input type="text" class="info-value" id="name" name="name" value="{{ $user->staff_name }}" required>
                            </div>

                            <div class="info-group">
                                <label class="info-label">Staff ID</label>
                                <input type="text" class="info-value" id="displayStaffId" value="{{ $user->formatted_staff_id }}" readonly style="cursor: not-allowed;">
                            </div>

                            <div class="info-group">
                                <label class="info-label">Email</label>
                                <input type="email" class="info-value" id="email" value="{{ $user->staff_email }}" readonly style="cursor: not-allowed;">
                            </div>

                            <div class="info-group">
                                <label class="info-label">Number Phone</label>
                                <input type="text" class="info-value" id="phone" name="phone" value="{{ $user->staff_phone }}" required maxlength="15" title="Phone number">
                            </div>
							
							<!-- Only show "Managed By" for non-managers (Finance Executive) -->
                            @if ($showManagerField)
                            <div class="info-group">
                                <label class="info-label">Managed By</label>
                                <input type="text" class="info-value" id="managerName" value="{{ $managerName }}" readonly style="cursor: not-allowed;">
                            </div>
                            @endif
                            
                            <!-- ADD THIS: Role field -->
							<div class="info-group">
							    <label class="info-label">Role</label>
							    <input type="text" class="info-value" id="role"
       								name="role" value="{{ $user->staff_role }}" readonly>
							</div>
                            
                            <div class="info-group">
                                <label class="info-label">Password</label>
                                <div class="password-wrapper">
                                    <input type="password" class="info-value" id="password" name="password" value="{{ $passwordMask }}" readonly>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                                        <img src="{{ asset('images/icons/eyeDisable.png') }}" alt="Show password" id="password-toggle-icon">
                                    </button>
                                </div>
                                <div class="password-actions">
                                    <span class="change-password-link" id="changePasswordLink" onclick="showChangePassword()">Change Password</span>
                                    <span class="cancel-password-link" id="cancelPasswordLink" onclick="cancelChangePassword()">Cancel</span>
                                </div>
                            </div>

                            <div class="info-group">
                                <label class="info-label">Profile Picture</label>
                                <div class="file-upload-group">
                                    <label for="profilePicture" class="file-upload-btn">
                                        <span class="file-icon">📁</span>
                                        <span id="fileNameDisplay">Choose File</span>
                                    </label>
                                    <input type="file" id="profilePicture" name="profilePicture" class="file-input" accept="*" onchange="handleFileSelect(event)">
                                    <span class="file-info">JPEG, PNG.</span>
                                </div>
                                <div id="pictureError" style="display:none; color:#e74c3c; font-size:13px; margin-top:4px;"></div>
                            </div>
                            
                            <!-- Hidden Re-confirm Password Field -->
                            <div class="info-group reconfirm-password-group" id="reconfirmPasswordGroup">
                                <label class="info-label">Re-confirm Password</label>
                                <div class="password-wrapper">
                                    <input type="password" class="info-value" id="confirmPassword" name="confirmPassword" placeholder="Re-enter new password" minlength="6">
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')" aria-label="Toggle password visibility">
                                        <img src="{{ asset('images/icons/eyeDisable.png') }}" alt="Show password" id="confirmPassword-toggle-icon">
                                    </button>
                                </div>
                                <div id="passwordError" style="display:none; color:#e74c3c; font-size:13px; margin-top:4px;"></div>
                            </div>

                            <div class="info-group full-width">
                                <label class="info-label">Address</label>
                                <input type="text" class="info-value" id="address" name="address" value="{{ $user->staff_address }}" required>
                            </div>
                        </div>

                        <div class="btn-container">
                            <button type="button" class="btn-update" id="updateBtn" onclick="showUpdateModal()">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage"></div>

    <!-- Error Toast (same position/style as success message but red) -->
    <div class="success-message" id="errorToast" style="background: #e74c3c;"></div>

    <!-- Update Confirmation Modal -->
    <div class="modal-overlay" id="updateModal">
        <div class="modal-content">
            <div class="modal-icon">⚠️</div>
            <div class="modal-message">
                Are you sure you want to update your profile information?
            </div>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-no" onclick="closeUpdateModal()">No</button>
                <button class="modal-btn modal-btn-yes" onclick="confirmUpdate()">Yes</button>
            </div>
        </div>
    </div>

    <script>
        // Phone number validation - digits only
        const phoneInput = document.getElementById('phone');
        
        // Phone validation happens on Update Profile click
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Validate phone on blur
        phoneInput.addEventListener('blur', function() {
            const phoneValue = this.value.trim();
            if (phoneValue.length > 0 && !/^[0-9]+$/.test(phoneValue)) {
                showProfileError('Phone number must contain only digits.');
                this.focus();
            }
        });
        
        let isChangingPassword = false;
        // Passwords are stored hashed, so only a mask is shown
        const ORIGINAL_PASSWORD = @json($passwordMask);
        
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === 'true') {
                showSuccessMessage('Profile updated successfully!');
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (urlParams.get('error')) {
                showProfileError({{ \Illuminate\Support\Js::from(session('error', 'Failed to update profile. Please try again.')) }});
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        function showChangePassword() {
            isChangingPassword = true;
            const passwordField = document.getElementById('password');
            const reconfirmGroup = document.getElementById('reconfirmPasswordGroup');
            const changeLink = document.getElementById('changePasswordLink');
            const cancelLink = document.getElementById('cancelPasswordLink');
            
            passwordField.readOnly = false;
            passwordField.value = '';
            passwordField.placeholder = 'Enter new password';
            passwordField.focus();
            
            reconfirmGroup.classList.add('show');
            cancelLink.classList.add('show');
            changeLink.style.display = 'none';
        }

        function cancelChangePassword() {
            isChangingPassword = false;
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');
            const reconfirmGroup = document.getElementById('reconfirmPasswordGroup');
            const changeLink = document.getElementById('changePasswordLink');
            const cancelLink = document.getElementById('cancelPasswordLink');
            const errorMsg = document.getElementById('passwordError');
            const updateBtn = document.getElementById('updateBtn');
            
            passwordField.readOnly = true;
            passwordField.type = 'password';
            passwordField.value = ORIGINAL_PASSWORD;
            passwordField.placeholder = '';
            
            confirmPasswordField.type = 'password';
            confirmPasswordField.value = '';
            
            reconfirmGroup.classList.remove('show');
            cancelLink.classList.remove('show');
            changeLink.style.display = 'inline-block';
            
            errorMsg.style.display = 'none';
            errorMsg.textContent = '';
            updateBtn.disabled = false;
            
            document.getElementById('password-toggle-icon').src = '{{ asset('images/icons/eyeDisable.png') }}';
            document.getElementById('confirmPassword-toggle-icon').src = '{{ asset('images/icons/eyeDisable.png') }}';
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + '-toggle-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleIcon.src = '{{ asset('images/icons/eyeEnable.png') }}';
            } else {
                field.type = 'password';
                toggleIcon.src = '{{ asset('images/icons/eyeDisable.png') }}';
            }
        }

        function validatePasswordMatch() {
            if (!isChangingPassword) return true;

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorMsg = document.getElementById('passwordError');
            const updateBtn = document.getElementById('updateBtn');

            if (password && confirmPassword) {
                if (password !== confirmPassword) {
                    errorMsg.textContent = 'Passwords do not match!';
                    errorMsg.classList.add('show');
                    updateBtn.disabled = true;
                    return false;
                } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
                    errorMsg.textContent = 'Password does not meet the security criteria.';
                    errorMsg.classList.add('show');
                    updateBtn.disabled = true;
                    return false;
                } else {
                    errorMsg.classList.remove('show');
                    updateBtn.disabled = false;
                    return true;
                }
            }
            return true;
        }

        // Password validation happens on Update Profile click only

        function handleFileSelect(event) {
            const file = event.target.files[0];
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const pictureError = document.getElementById('pictureError');

            // Clear any previous error when user selects a new file
            pictureError.style.display = 'none';
            pictureError.textContent = '';

            if (file) {
                fileNameDisplay.textContent = file.name;

                // Only preview if it's a valid image type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (validTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatar = document.getElementById('profileAvatar');
                        avatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile Picture">';
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                fileNameDisplay.textContent = 'Choose File';
            }
        }

        function showUpdateModal() {
            clearProfileError();

            let hasError = false;

            // Clear inline errors from previous attempt
            const pictureError = document.getElementById('pictureError');
            pictureError.style.display = 'none';
            pictureError.textContent = '';
            const passwordError = document.getElementById('passwordError');
            passwordError.style.display = 'none';
            passwordError.textContent = '';

            // Validate profile picture file type if a file is selected
            const picFile = document.getElementById('profilePicture').files[0];
            if (picFile) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(picFile.type)) {
                    pictureError.textContent = 'Invalid file format. Only JPEG and PNG files are accepted.';
                    pictureError.style.display = 'block';
                    hasError = true;
                }
            }

            // Validate phone - digits only
            const phoneValue = document.getElementById('phone').value.trim();
            if (!phoneValue || !/^[0-9]+$/.test(phoneValue)) {
                showProfileError('Phone number must contain only digits.');
                hasError = true;
            }

            // Validate password if user is changing it
            if (isChangingPassword) {
                const password = document.getElementById('password').value;
                const confirmPwd = document.getElementById('confirmPassword').value;

                if (!password || !confirmPwd) {
                    passwordError.textContent = 'Please enter and confirm your new password.';
                    passwordError.style.display = 'block';
                    hasError = true;
                } else if (password !== confirmPwd) {
                    passwordError.textContent = 'Passwords do not match!';
                    passwordError.style.display = 'block';
                    hasError = true;
                } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
                    passwordError.textContent = 'Password does not meet the security criteria.';
                    passwordError.style.display = 'block';
                    hasError = true;
                }
            }

            // Only show confirmation modal if all validation passed
            if (hasError) return;
            document.getElementById('updateModal').classList.add('active');
        }

        function showProfileError(message) {
            const errToast = document.getElementById('errorToast');
            errToast.textContent = message;
            errToast.classList.add('show');

            setTimeout(function() {
                errToast.classList.remove('show');
                errToast.classList.add('hide');
                setTimeout(function() {
                    errToast.classList.remove('hide');
                }, 400);
            }, 3000);
        }

        function clearProfileError() {
            const errToast = document.getElementById('errorToast');
            if (errToast) {
                errToast.classList.remove('show');
                errToast.classList.remove('hide');
            }
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').classList.remove('active');
        }

        function confirmUpdate() {
            if (!isChangingPassword) {
                document.getElementById('password').value = '';
                document.getElementById('confirmPassword').value = '';
            }
            
            document.getElementById('profileForm').submit();
        }

        function showSuccessMessage(message) {
            const successMsg = document.getElementById('successMessage');
            successMsg.textContent = message;
            successMsg.classList.add('show');

            setTimeout(function() {
                successMsg.classList.remove('show');
                successMsg.classList.add('hide');
                
                setTimeout(function() {
                    successMsg.classList.remove('hide');
                }, 400);
            }, 3000);
        }

        document.getElementById('updateModal').addEventListener('click', function(e) {
            if (e.target === this) closeUpdateModal();
        });
    </script>
</body>
</html>
