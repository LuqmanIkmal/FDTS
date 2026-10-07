<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/auth/login.css',
        'resources/js/pages/auth/login.js',
    ])
</head>
<body>

    <!-- Success Message (signup) -->
    <div class="success-message" id="successMessage" style="display:none;">
        User Account Created Successfully!
    </div>

    <!-- Success Message (reset password) -->
    <div class="success-message" id="resetSuccessMessage" style="display:none;">
        Your password has been updated successfully. Please log in.
    </div>

    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <img src="{{ asset('images/Logo.png') }}" alt="Infra Desa Johor Logo">
            </div>
            <div class="system-title">
                <h1>Fixed Deposit<br>Tracking System</h1>
            </div>
            <div class="system-description">
                <p>A digital platform designed to simplify fixed deposit tracking and monitoring.</p>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-form">
                <div class="form-header">
                    <h2>Login Account</h2>
                    <hr class="divider">
                </div>

                <!-- SERVER ERROR MESSAGE (POST only) -->
                @if (session("error"))
                    <div class="server-error" style="margin-bottom: 12px; padding: 10px; border-radius: 8px;">
                        {{ session("error") }}
                    </div>
                @endif

                <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <div class="input-wrapper">
                            <img src="{{ asset('images/icons/user.jpg') }}" alt="Email" class="input-icon">
                            <input type="email" id="email" name="email" placeholder="Your Email" required
                                   value="{{ old('email') }}">
                        </div>
                        <div class="error-message" id="emailError">Please enter a valid email address</div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper" style="position: relative;">
                            <img src="{{ asset('images/icons/password.png') }}" alt="Password" class="password-icon">
                            <input type="password" id="password" name="password" placeholder="Your Password" required>
                            <button type="button" id="togglePassword"
                                    style="position: absolute; right: 25px; background: none; border: none; cursor: pointer; z-index: 2;">
                                <img id="passwordIcon" src="{{ asset('images/icons/eyeDisable.png') }}" alt="Show Password" style="width: 25px; height: 25px;">
                            </button>
                        </div>

                        <div class="error-message" id="passwordError">Please enter your password</div>

                        <div class="forgot-password-link">
                            <a href="{{ route('password.forgot') }}">Forgot Password ?</a>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Login</button>

                    <div class="signup-link">
                        Don't have an account? Click here to <a href="{{ route('signup') }}">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

{{-- Values for resources/js/pages/auth/login.js --}}
<script>
    window.pageData = {
        eyeEnableIcon: "{{ asset('images/icons/eyeEnable.png') }}",
        eyeDisableIcon: "{{ asset('images/icons/eyeDisable.png') }}",
    };
</script>

</body>
</html>
