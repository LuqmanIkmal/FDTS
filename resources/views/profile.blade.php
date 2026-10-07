<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fixed Deposit Tracking System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/profile.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/profile.js',
    ])
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
                                <div id="pictureError" style="display:none; color:#d92d20; font-size:13px; margin-top:4px;"></div>
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
                                <div id="passwordError" style="display:none; color:#d92d20; font-size:13px; margin-top:4px;"></div>
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
    <div class="success-message" id="errorToast" style="background: #d92d20;"></div>

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

    {{-- Values for resources/js/pages/profile.js --}}
    <script>
        window.pageData = {
            // Passwords are stored hashed, so only a mask is shown
            passwordMask: @json($passwordMask),
            errorMessage: {{ \Illuminate\Support\Js::from(session('error', 'Failed to update profile. Please try again.')) }},
            eyeEnableIcon: '{{ asset('images/icons/eyeEnable.png') }}',
            eyeDisableIcon: '{{ asset('images/icons/eyeDisable.png') }}',
        };
    </script>
</body>
</html>
