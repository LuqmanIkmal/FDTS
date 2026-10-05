<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - Fixed Deposit Tracking System</title>

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
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .left-panel {
            flex: 0 0 30%;
            background: linear-gradient(180deg, #1e5765 0%, #2d7080 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding: 10px 40px 60px 40px;
            color: white;
            position: relative;
            background-image: 
                linear-gradient(rgba(12, 55, 63, 0.7), rgba(12, 55, 63, 0.8)),
                url('{{ asset('images/signupBG.png') }}');
            background-size: cover;
            background-position: center;
        }

        .logo {
            width: 240px;
            height: 240px;
            position: relative;
            margin-bottom: 0px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .system-title {
            text-align: left;
            margin-left: 20px;
        }

        .system-title h1 {
            font-size: 4rem;
            font-weight: 700;
            color: #ff9a5a;
            line-height: 1.1;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.2);
            font-family: 'Libre Baskerville', serif;
        }

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: -150px;
            right: 150px;
            width: 1000px;
            height: 2000px;
            background-image: url('{{ asset('images/line.png') }}');
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 1;
            pointer-events: none;
        }
	
	/* ===== FORM ===== */
	.signup-form {
	  width: 100%;
	  max-width: 600px;
	  position: relative;
	  z-index: 1;
	}
	
	.form-header {
	  text-align: center;
	  margin-bottom: 50px;
	}
	
	.form-header h2 {
	  font-size: 3rem;
	  color: #1a4d5e;
	  font-weight: 700;
	  margin-bottom: 15px;
	}
	
	.form-header .divider {
	  width: 100%;
	  border: none;
	  border-top: 3px dashed #1a4d5e;
	  background: transparent;
	}
	
	/* ===== INPUT ROW ===== */
	.form-row {
	  display: flex;
	  justify-content: center;
	  margin-bottom: 25px;
	}
	
	.form-group {
	  width: 65%;
	  max-width: 560px;
	}
	
	.input-wrapper {
	  position: relative;
	  display: flex;
	  align-items: center;
	}
	
	.input-icon {
	  position: absolute;
	  left: 25px;
	  width: 35px;
	  height: 35px;
	  margin-left: -6px;
	  z-index: 1;
	  object-fit: contain;
	}
	
	/* ✅ IMPORTANT: password inputs need same style */
	input[type="password"] {
	  width: 100%;
	  padding: 20px 25px 20px 65px;
	  border: 2px solid #d0d0d0;
	  border-radius: 50px;
	  font-size: 16px;
	  transition: all 0.3s ease;
	  background: #fafafa;
	  color: #333;
	}
	
	input:focus {
	  outline: none;
	  border-color: #1a4d5e;
	  background: white;
	}
	
	input::placeholder { color: #a8a8a8; }
	
	.helper-text {
	  color: #7f8c8d;
	  font-size: 14px;
	  margin-top: 8px;
	  padding-left: 42px;
	}
	
	/* ===== BUTTON ===== */
	.submit-btn {
	  width: 70%;
	  max-width: 400px;
	  padding: 20px;
	  background: linear-gradient(135deg, #ff9a5a, #ff8547);
	  border: none;
	  border-radius: 50px;
	  color: white;
	  font-size: 20px;
	  font-weight: 600;
	  cursor: pointer;
	  transition: all 0.3s ease;
	  margin: 40px auto 0;
	  display: block;
	  box-shadow: 0 6px 20px rgba(255, 154, 90, 0.3);
	}
	
	.submit-btn:hover {
	  transform: translateY(-2px);
	  box-shadow: 0 8px 25px rgba(255, 154, 90, 0.4);
	}
	
	/* ===== BACK LINK ===== */
	.login-link {
	  width: 60%;
	  max-width: 560px;
	  margin: 10px auto 0;
	  text-align: center;
	  font-size: 16px;
	}
	
	.back-link {
	  display: inline-flex;
	  align-items: center !important;
	  gap: 8px;
	  color: #1a4d5e;
	  font-weight: 700;
	  text-decoration: underline;
	
	  white-space: nowrap; 
	}

	
	/* ===== MESSAGES ===== */
	.error-message {
	  color: #e74c3c;
	  font-size: 14px;
	  margin-top: 15px;
	  text-align: center;
	}
	
	.success-message {
	  color: #27ae60;
	  font-size: 14px;
	  margin-top: 15px;
	  text-align: center;
	}
	
	/* ===== RESPONSIVE ===== */
	@media (max-width: 1200px) {
	  .system-title h1 { font-size: 3.5rem; }
	  .form-header h2 { font-size: 2.5rem; }
	  .form-group { width: 80%; }
	}
	
	@media (max-width: 968px) {
	  .container { flex-direction: column; }
	
	  .left-panel {
	    flex: 0 0 auto;
	    padding: 40px 20px;
	  }
	
	  .logo { width: 100px; height: 100px; }
	
	  .system-title h1 { font-size: 2.5rem; }
	
	  .right-panel { padding: 40px 20px; }
	
	  .form-group { width: 100%; max-width: none; }
	
	  .submit-btn { width: 100%; }
	}
  </style>
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
          <h2>Reset Password</h2>
          <hr class="divider">
        </div>

        <!-- FIXED: Message after OTP verified (using JSP scriptlet instead of JSTL) -->
        @if (request('verified') === 'true')
          <div class="success-message" style="margin-bottom: 12px;">
            Your account has been verified. You can now reset your password.
          </div>
        @endif

        <form id="resetForm" action="{{ route('password.reset.submit') }}" method="post" novalidate>
          @csrf
          <!-- New Password -->
          <div class="form-row">
            <div class="form-group">
              <div class="input-wrapper" style="position: relative;">
                <img src="{{ asset('images/icons/password.png') }}" alt="Password Icon" class="input-icon">

                <input type="password" id="password" name="password" placeholder="New Password" required>

                <button type="button" id="togglePassword"
                        style="position: absolute; right: 25px; background: none; border: none; cursor: pointer; z-index: 2;">
                  <img id="passwordIcon" src="{{ asset('images/icons/eyeEnable.png') }}" alt="Show Password" style="width: 25px; height: 25px;">
                </button>
              </div>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="form-row">
            <div class="form-group">
              <div class="input-wrapper" style="position: relative;">
                <img src="{{ asset('images/icons/password.png') }}" alt="Confirm Password Icon" class="input-icon">

                <input type="password" id="confirm" name="confirm" placeholder="Re-enter Password" required>

                <button type="button" id="toggleConfirm"
                        style="position: absolute; right: 25px; background: none; border: none; cursor: pointer; z-index: 2;">
                  <img id="confirmIcon" src="{{ asset('images/icons/eyeEnable.png') }}" alt="Show Password" style="width: 25px; height: 25px;">
                </button>
              </div>

              <div class="helper-text">Make sure both passwords match.</div>

              <!-- Client-side error (only shown after submit) -->
              <div id="clientError" class="error-message" style="display:none;"></div>
            </div>
          </div>

          <button type="submit" class="submit-btn">Reset</button>

          <!-- FIXED: Server-side messages (using JSP scriptlet instead of JSTL) -->
          @if (session('error'))
            <div class="error-message">{{ session('error') }}</div>
          @endif

          @if (session('success'))
            <div class="success-message">{{ session('success') }}</div>
          @endif
        </form>

        <!-- Back to Login Link -->
        <div class="login-link">
		  <a href="{{ route('login') }}" class="back-link">
		    <span>Back to Login</span>
		  </a>
		</div>
      </div>
    </div>
  </div>

  <script>
    // Elements
    const form = document.getElementById('resetForm');
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm');
    const clientError = document.getElementById('clientError');

    // Toggle New Password - FIXED: Using correct icon names
    const togglePasswordButton = document.getElementById("togglePassword");
    const passwordIcon = document.getElementById("passwordIcon");

    togglePasswordButton.addEventListener("click", () => {
      if (passwordField.type === "password") {
        passwordField.type = "text";
        passwordIcon.src = "{{ asset('images/icons/eyeDisable.png') }}";  // Hide icon when showing password
        passwordIcon.alt = "Hide Password";
      } else {
        passwordField.type = "password";
        passwordIcon.src = "{{ asset('images/icons/eyeEnable.png') }}";   // Show icon when hiding password
        passwordIcon.alt = "Show Password";
      }
    });

    // Toggle Confirm Password - FIXED: Using correct icon names
    const toggleConfirmButton = document.getElementById("toggleConfirm");
    const confirmIcon = document.getElementById("confirmIcon");

    toggleConfirmButton.addEventListener("click", () => {
      if (confirmField.type === "password") {
        confirmField.type = "text";
        confirmIcon.src = "{{ asset('images/icons/eyeDisable.png') }}";  // Hide icon when showing password
        confirmIcon.alt = "Hide Password";
      } else {
        confirmField.type = "password";
        confirmIcon.src = "{{ asset('images/icons/eyeEnable.png') }}";   // Show icon when hiding password
        confirmIcon.alt = "Show Password";
      }
    });

    function showClientError(message) {
      clientError.textContent = message;
      clientError.style.display = "block";
    }

    function clearClientError() {
      clientError.textContent = "";
      clientError.style.display = "none";
    }

    // Only validate when submit is pressed
    form.addEventListener("submit", (e) => {
      clearClientError();

      const p = passwordField.value.trim();
      const c = confirmField.value.trim();

      if (!p || !c) {
        e.preventDefault();
        showClientError("Please fill in all fields.");
        return;
      }

      if (p !== c) {
        e.preventDefault();
        showClientError("Password and Confirm Password must match.");
        return;
      }

      if (p.length < 6) {
        e.preventDefault();
        showClientError("Password must be at least 6 characters long.");
        return;
      }
    });
  </script>
</body>
</html>
