<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - Fixed Deposit Tracking System</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  @vite([
      'resources/css/pages/auth/reset-password.css',
      'resources/js/pages/auth/reset-password.js',
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
        <h1>Fixed <br>Deposit <br>Tracking <br>System</h1>
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

  {{-- Values for resources/js/pages/auth/reset-password.js --}}
  <script>
      window.pageData = {
          eyeEnableIcon: "{{ asset('images/icons/eyeEnable.png') }}",
          eyeDisableIcon: "{{ asset('images/icons/eyeDisable.png') }}",
      };
  </script>
</body>
</html>
