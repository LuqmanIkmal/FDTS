// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

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
        passwordIcon.src = pageData.eyeEnableIcon;  // CHANGED: Hide icon when password is visible
        passwordIcon.alt = "Show Password";
    } else {
        // Hide password
        passwordField.type = "password";
        passwordIcon.src = pageData.eyeDisableIcon;   // CHANGED: Show icon when password is hidden
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
