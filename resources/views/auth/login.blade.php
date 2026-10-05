<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: 
                linear-gradient(rgba(12, 55, 63, 0.7), rgba(12, 55, 63, 0.8)),
                url('{{ asset('images/signupBG.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            max-height: 100vh;
            display: flex;
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            align-items: center;
            padding: 40px;
        }

        .left-panel {
            flex: 0 0 50%;
            background: transparent;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 80px;
            color: white;
            position: relative;
        }

        .logo {
            width: 240px;
            height: 240px;
            position: relative;
            margin-top: -150px;
            margin-bottom: -30px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .system-title {
            text-align: left;
            margin-bottom: -20px;
        }

        .system-title h1 {
            font-size: 4.5rem;
            font-weight: 700;
            color: #ff914d;
            line-height: 1.1;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.2);
            font-family: 'Libre Baskerville', serif;
        }

        .system-description {
            max-width: 600px;
            margin-top: 35px;
        }

        .system-description p {
            font-size: 1.4rem;
            line-height: 1.6;
            color: #e0e0e0;
            font-weight: medium;
            font-family: 'Inter', sans-serif;
        }

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: transparent;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 130%;
            height: 100%;
            background-image: url('{{ asset('images/lineP.png') }}');
            background-size: cover;
            background-position: 100% center;
            background-repeat: no-repeat;
            opacity: 0.4;
            pointer-events: none;
        }

        .login-form {
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
        }

        .form-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .form-header h2 {
            font-size: 3rem;
            color: #ffffff;
            font-weight: 700;
        }

        .divider {
            border-top: 3px dashed #ffffff;
            margin-top: 15px;
        }

        /* ================= FORM ================= */
        .form-group {
            margin-bottom: 30px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        /* ================= ICONS ================= */
        .input-icon,
        .password-icon {
            position: absolute;
            left: 25px;
            width: 40px;
            height: 40px;
            object-fit: contain;
            z-index: 2;
        }

        /* ================= INPUTS ================= */
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 20px 25px 20px 70px;
            border: 2px solid #d0d0d0;
            border-radius: 50px;
            font-size: 16px;
            background: #fafafa;
            color: #333;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #1a4d5e;
            background: #fff;
        }

        input::placeholder {
            color: #a8a8a8;
        }

        /* ================= FORGOT PASSWORD ================= */
        .forgot-password-link {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-password-link a {
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: underline !important;
            text-underline-offset: 3px;
        }

        .forgot-password-link a:hover {
            text-decoration: none;
        }

        /* ================= BUTTON ================= */
        .submit-btn {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, #ff9a5a, #ff8547);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        /* ================= SIGN UP LINK ================= */
        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #ffffff;
            font-size: 16px;
        }

        .signup-link a {
            color: #ffffff;
            font-weight: 700;
            text-decoration: underline !important;
            text-underline-offset: 3px;
        }

        .signup-link a:hover {
            text-decoration: none;
        }

        /* ================= ERROR ================= */
        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
            margin-left: 25px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        input.error {
            border-color: #e74c3c;
        }

        /* ================= SERVER ERROR (from servlet) ================= */
        .server-error {
            background: rgba(231, 76, 60, 0.18);
            border: 1px solid rgba(231, 76, 60, 0.7);
            color: #ffffff;
            font-weight: 600;
        }

        /* ================= SUCCESS MESSAGE ================= */
        .success-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #7dd3c0;
            color: #0c373f;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: none;
            animation: slideDown 0.5s ease;
            font-family: 'Inter', sans-serif;
        }

        .success-message.show {
            display: block;
        }

        .success-message.hide {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes slideDown {
            from {
                top: -100px;
                opacity: 0;
            }
            to {
                top: 20px;
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                top: 20px;
            }
            to {
                opacity: 0;
                top: -100px;
            }
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1200px) {
            .left-panel {
                padding: 40px 50px;
            }

            .system-title h1 {
                font-size: 3.5rem;
            }

            .form-header h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
            }

            .left-panel {
                flex: 0 0 auto;
                padding: 40px 20px;
            }

            .logo {
                width: 120px;
                height: 120px;
            }

            .system-title h1 {
                font-size: 2.5rem;
            }

            .right-panel {
                padding: 40px 20px;
            }

            .submit-btn {
                width: 100%;
            }
        }
    </style>
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

<script>
    const urlParams = new URLSearchParams(window.location.search);

    function showSuccessMessage(elementId) {
        const el = document.getElementById(elementId);
        if (!el) return;

        el.style.display = 'block';
        el.classList.add('show');

        setTimeout(() => {
            el.classList.add('hide');
            setTimeout(() => {
                el.classList.remove('show', 'hide');
                el.style.display = 'none';
                // remove query params after showing
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 500);
        }, 5000);
    }

    // success message (signup)
    if (urlParams.get('signup') === 'success') {
        showSuccessMessage('successMessage');
    }

    // success message (reset password)
    if (urlParams.get('reset') === 'success') {
        showSuccessMessage('resetSuccessMessage');
    }

    // Elements
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const passwordField = document.getElementById('password');

    const togglePasswordButton = document.getElementById("togglePassword");
    const passwordIcon = document.getElementById("passwordIcon");

    // Toggle password visibility - FIXED ICON NAMES
    togglePasswordButton.addEventListener("click", () => {
        if (passwordField.type === "password") {
            // Show password
            passwordField.type = "text";
            passwordIcon.src = "{{ asset('images/icons/eyeEnable.png') }}";  // CHANGED: Hide icon when password is visible
            passwordIcon.alt = "Show Password";
        } else {
            // Hide password
            passwordField.type = "password";
            passwordIcon.src = "{{ asset('images/icons/eyeDisable.png') }}";   // CHANGED: Show icon when password is hidden
            passwordIcon.alt = "Hide Password";
        }
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
        document.querySelectorAll('input').forEach(el => el.classList.remove('error'));

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value)) {
            showError('email', 'emailError', 'Please enter a valid email address');
            isValid = false;
        }

        if (passwordField.value.trim() === '') {
            showError('password', 'passwordError', 'Please enter your password');
            isValid = false;
        }

        if (!isValid) e.preventDefault();
    });

    function showError(inputId, errorId, message) {
        document.getElementById(inputId).classList.add('error');
        const errorEl = document.getElementById(errorId);
        errorEl.textContent = message;
        errorEl.classList.add('show');
    }
</script>

</body>
</html>
