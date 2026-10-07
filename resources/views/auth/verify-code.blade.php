<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verify Code - Fixed Deposit Tracking System</title>
  <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  @vite([
      'resources/css/pages/auth/verify-code.css',
      'resources/js/pages/auth/verify-code.js',
  ])
</head>

<body>

  <div class="container">
    <!-- LEFT PANEL -->
    <div class="left-panel">
      <div class="logo">
        <img src="{{ asset('images/vv-logo-white.png') }}" alt="Vista Velocity">
      </div>

      <div class="system-title">
        <h1>Fixed <br>Deposit <br>Tracking <br>System</h1>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <div class="signup-form">
        <div class="form-header">
          <h2>Verification Code</h2>
          <hr class="divider">
        </div>

        <!-- Success message from redirect -->
        @if (request('sent') === 'true')
          <div class="success-message" style="margin-bottom: 12px;">
            ✅ The verification code has been sent to your email. Please check it.
          </div>
        @endif

        <form action="{{ route('password.verify.submit') }}" method="post">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <div class="input-wrapper">
                <img src="{{ asset('images/icons/email.png') }}" alt="Email Icon" class="input-icon">
                <!-- ✅ FIXED: Changed name from "code" to "otp" -->
                <input type="text" name="otp" placeholder="Enter 6-Digit OTP" required maxlength="6" pattern="\d{6}" autocomplete="off">
              </div>

              <div class="helper-text">Please enter the verification code sent to your email.</div>
              
              <!-- Countdown Timer -->
              <div class="helper-text" id="timer" style="color: #14171c; font-weight: 600; margin-top: 15px;">
                ⏱️ Code expires in: <span id="countdown">5:00</span>
              </div>
              
              <!-- Resend Link -->
              <div class="helper-text" style="margin-top: 10px;">
                <a href="{{ route('password.forgot') }}" style="color: #bf1e2e; text-decoration: underline; font-weight: 600;">📧 Request New Code</a>
              </div>
            </div>
          </div>

          <button type="submit" class="submit-btn">Verify</button>

          <!-- Server-side messages -->
          @if (session('error'))
            <div class="error-message">❌ {{ session('error') }}</div>
          @endif

          @if (session('success'))
            <div class="success-message">✅ {{ session('success') }}</div>
          @endif
        </form>
        
        <div class="login-link">
		  <a href="{{ route('login') }}" class="back-link">
		    <span>Back to Login</span>
		  </a>
		</div>
      </div>
    </div>
  </div>

</body>
</html>
