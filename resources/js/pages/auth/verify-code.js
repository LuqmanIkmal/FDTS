// Auto-format input to accept only numbers
const otpInput = document.querySelector('input[name="otp"]');

if (otpInput) {
  // Remove any non-digit characters
  otpInput.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^\d]/g, '');
  });

  // Auto-focus the input
  otpInput.focus();
}

// Countdown timer (5 minutes = 300 seconds)
let timeLeft = 300;

function updateTimer() {
  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;
  const display = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

  const countdownElement = document.getElementById('countdown');
  if (countdownElement) {
    countdownElement.textContent = display;
  }

  if (timeLeft <= 0) {
    const timerElement = document.getElementById('timer');
    if (timerElement) {
      timerElement.style.color = '#e74c3c';
      timerElement.innerHTML = '<strong>⏰ Code EXPIRED - Request a new one</strong>';
    }
    if (otpInput) {
      otpInput.disabled = true;
      otpInput.placeholder = 'Code Expired';
      otpInput.style.background = '#f8d7da';
    }
    alert('⏰ Verification code has expired. Please request a new one.');
  } else {
    timeLeft--;
    setTimeout(updateTimer, 1000);
  }
}

// Start countdown
updateTimer();

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
  const otp = otpInput.value.trim();

  if (otp === '') {
    e.preventDefault();
    alert('❌ Please enter the verification code');
    otpInput.focus();
    return false;
  }

  if (!/^\d{6}$/.test(otp)) {
    e.preventDefault();
    alert('❌ Please enter a valid 6-digit code (numbers only)');
    otpInput.focus();
    return false;
  }

  // Disable button to prevent double submission
  const submitBtn = this.querySelector('.submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Verifying...';
  submitBtn.style.opacity = '0.7';
});
