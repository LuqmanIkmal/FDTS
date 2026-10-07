<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/auth/signup.css',
        'resources/js/pages/auth/signup.js',
    ])
</head>
<body>
<div class="container">

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="logo">
            <img src="{{ asset('images/Logo.png') }}" alt="Infra Desa Johor Logo">
        </div>
        <div class="system-title">
            <h1>Fixed<br>Deposit<br>Tracking<br>System</h1>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="signup-form">

            <div class="form-header">
                <h2>Sign Up Account</h2>
                <hr class="divider">
            </div>

            @if (session("error"))
                <div class="error-message show server-error" style="margin-bottom: 12px;">
                    {{ session("error") }}
                </div>
            @endif

            <!-- FORM SUBMITS TO SignUpController -->
            <form id="signupForm" action="{{ route('signup.submit') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Full Name (Full Width) -->
                <div class="form-row fullwidth">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <img src="{{ asset('images/icons/user.jpg') }}" alt="User" class="input-icon">
                            <input type="text" id="fullName" name="name" placeholder="Full Name">
                        </div>
                        <div class="error-message" id="fullNameError">Please enter your full name</div>
                    </div>
                </div>

                <!-- Row 1: Phone | Email -->
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <img src="{{ asset('images/icons/phone.png') }}" alt="Phone" class="phone-icon">
                            <input type="tel" id="phoneNumber" name="phone" placeholder="Phone Number" pattern="[0-9]+">
                        </div>
                        <div class="error-message" id="phoneError">Please enter a valid phone number (numbers only)</div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <img src="{{ asset('images/icons/email.png') }}" alt="Email" class="input-icon">
                            <input type="email" id="email" name="email" placeholder="Email Address">
                        </div>
                        <div class="error-message" id="emailError">Please enter a valid email address</div>
                    </div>
                </div>

                <!-- Row 2: Address | Profile Picture -->
                <div class="form-row">
                    <div class="form-group fullwidth">
                        <div class="input-wrapper">
                            <img src="{{ asset('images/icons/haddress.png') }}" alt="Home" class="address-icon">
                            <input type="text" id="homeAddress" name="address" placeholder="Home Address">
                        </div>
                        <div class="error-message" id="addressError">Please enter your home address</div>
                    </div>

                    <div class="form-group profile-upload">
                        <label for="profilePicture" class="profile-upload-wrapper" id="uploadWrapper">
                            <img src="{{ asset('images/icons/file.png') }}" alt="Upload" class="profile-icon">
                            <span class="profile-upload-input" id="uploadLabel">Upload Profile Picture</span>
                        </label>

                        <div class="file-format">JPEG, PNG</div>
                        <input type="file" id="profilePicture" name="profilePicture" >
                        <div class="error-message" id="fileError">Please upload your profile picture</div>
                    </div>
                </div>

                <!-- Row 3: Role | Manager -->
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper select-wrapper">
                            <img src="{{ asset('images/icons/role.png') }}" alt="Role" class="role-icon">
                            <select id="staffRole" name="role">
                                <option value="" disabled selected>Select Role</option>
                                <option value="Senior Finance Manager">Senior Finance Manager</option>
                                <option value="Finance Executive">Finance Executive</option>
                            </select>
                        </div>
                        <div class="error-message" id="roleError">Please select a role</div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper select-wrapper">
                            <img src="{{ asset('images/icons/manager.png') }}" alt="Manager" class="manager-icon">
                            <!-- REMOVED required attribute -->
                            <select id="managerId" name="managerId">
                                <option value="" disabled selected>Select Manager</option>
                                <option value="0">I'm Manager</option>
                                {{-- Only active Senior Finance Managers who have no manager --}}
                                @foreach ($managers as $mgr)
								    <option value="{{ $mgr->staff_id }}">
								        {{ $mgr->staff_name }} - {{ $mgr->staff_role }}
								    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- NEW: Error message for manager select -->
                        <div class="error-message" id="managerError">Please select a manager or choose "No Manager"</div>
                    </div>
                </div>

                <!-- Row 4: Password | Re-confirm Password -->
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-wrapper" style="position: relative;">
                            <img src="{{ asset('images/icons/password.png') }}" alt="Password" class="password-icon">
                            <input type="password" id="password" name="password" placeholder="Password">

                            <button type="button" id="togglePassword"
                                    style="position:absolute; right:25px; background:none; border:none; cursor:pointer; z-index:2;">
                                <img id="passwordIcon" src="{{ asset('images/icons/eyeDisable.png') }}" alt="Show Password" style="width:25px; height:25px;">
                            </button>
                        </div>
                        <div class="error-message" id="passwordError">Password must be at least 8 characters</div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper" style="position: relative;">
                            <img src="{{ asset('images/icons/password.png') }}" alt="Confirm Password" class="password-icon">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-confirm Password">

                            <button type="button" id="toggleConfirmPassword"
                                    style="position:absolute; right:25px; background:none; border:none; cursor:pointer; z-index:2;">
                                <img id="confirmPasswordIcon" src="{{ asset('images/icons/eyeDisable.png') }}" alt="Show Password" style="width:25px; height:25px;">
                            </button>
                        </div>
                        <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Sign Up</button>

                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}">Login Now</a>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal-overlay" id="confirmModal">
    <div class="modal-content">
        <h3>Are you sure you want to submit this information?</h3>
        <div class="modal-buttons">
            <button type="button" class="modal-btn modal-btn-no" id="modalNo">No</button>
            <button type="button" class="modal-btn modal-btn-yes" id="modalYes">Yes</button>
        </div>
    </div>
</div>

{{-- Values for resources/js/pages/auth/signup.js --}}
<script>
    window.pageData = {
        eyeEnableIcon: '{{ asset('images/icons/eyeEnable.png') }}',
        eyeDisableIcon: '{{ asset('images/icons/eyeDisable.png') }}',
    };
</script>
</body>
</html>
