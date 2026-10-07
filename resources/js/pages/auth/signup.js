// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Form validation
const form = document.getElementById('signupForm');
const fullName = document.getElementById('fullName');
const phoneNumber = document.getElementById('phoneNumber');
const email = document.getElementById('email');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const homeAddress = document.getElementById('homeAddress');
const profilePicture = document.getElementById('profilePicture');
const uploadWrapper = document.getElementById('uploadWrapper');
const uploadLabel = document.getElementById('uploadLabel');
const fileError = document.getElementById('fileError');
const staffRole = document.getElementById('staffRole');
const managerId = document.getElementById('managerId');

// Password toggle elements
const togglePasswordButton = document.getElementById('togglePassword');
const passwordIcon = document.getElementById('passwordIcon');
const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

// Toggle password visibility
togglePasswordButton.addEventListener('click', () => {
    if (password.type === 'password') {
        password.type = 'text';
        passwordIcon.src = pageData.eyeEnableIcon;
        passwordIcon.alt = 'Show Password';
    } else {
        password.type = 'password';
        passwordIcon.src = pageData.eyeDisableIcon;
        passwordIcon.alt = 'Hide Password';
    }
});

// Toggle confirm password visibility
toggleConfirmPasswordButton.addEventListener('click', () => {
    if (confirmPassword.type === 'password') {
        confirmPassword.type = 'text';
        confirmPasswordIcon.src = pageData.eyeEnableIcon;
        confirmPasswordIcon.alt = 'Show Password';
    } else {
        confirmPassword.type = 'password';
        confirmPasswordIcon.src = pageData.eyeDisableIcon;
        confirmPasswordIcon.alt = 'Hide Password';
    }
});

// Phone number validation happens on form submit
phoneNumber.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// File upload preview
profilePicture.addEventListener('change', function(e) {
    const file = e.target.files[0];

    if (file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            fileError.textContent = 'Invalid file format. Only JPEG and PNG files are accepted.';
            fileError.classList.add('show');
            uploadWrapper.classList.add('error');
            this.value = '';
            uploadLabel.textContent = 'Upload Profile Picture';
            uploadLabel.style.color = '#a8a8a8';
            return;
        }

        if (file.size > 5 * 1024 * 1024) { // 5MB limit
            fileError.textContent = 'File size must be less than 5MB';
            fileError.classList.add('show');
            uploadWrapper.classList.add('error');
            this.value = '';
            uploadLabel.textContent = 'Upload Profile Picture';
            uploadLabel.style.color = '#a8a8a8';
            return;
        }

        // File is valid
        uploadLabel.textContent = file.name;
        uploadLabel.style.color = '#333';
        fileError.classList.remove('show');
        uploadWrapper.classList.remove('error');
    }
});

// ========================================
// DYNAMIC MANAGER FIELD BASED ON ROLE
// ========================================
staffRole.addEventListener('change', function() {
    const selectedRole = this.value;
    const managerSelect = document.getElementById('managerId');
    const imManagerOption = managerSelect.querySelector('option[value="0"]');

    if (selectedRole === 'Senior Finance Manager') {
        // Senior Finance Manager: Auto-select "I'm Manager" and disable field
        managerSelect.value = '0';
        managerSelect.disabled = true;
        managerSelect.style.backgroundColor = '#f5f5f5';
        managerSelect.style.cursor = 'not-allowed';

        // Clear any error
        document.getElementById('managerError').classList.remove('show');
        managerSelect.classList.remove('error');

    } else if (selectedRole === 'Finance Executive') {
        // Finance Executive: Enable field and hide "I'm Manager" option
        managerSelect.disabled = false;
        managerSelect.style.backgroundColor = 'white';
        managerSelect.style.cursor = 'pointer';
        managerSelect.value = ''; // Reset to placeholder

        // Hide "I'm Manager" option
        if (imManagerOption) {
            imManagerOption.style.display = 'none';
            imManagerOption.disabled = true;
        }

    } else {
        // No role selected: Enable field and show all options
        managerSelect.disabled = false;
        managerSelect.style.backgroundColor = 'white';
        managerSelect.style.cursor = 'pointer';
        managerSelect.value = '';

        // Show "I'm Manager" option
        if (imManagerOption) {
            imManagerOption.style.display = '';
            imManagerOption.disabled = false;
        }
    }
});

// Initialize on page load (in case role is pre-selected)
window.addEventListener('DOMContentLoaded', function() {
    if (staffRole.value) {
        staffRole.dispatchEvent(new Event('change'));
    }
});

// Form submission validation
form.addEventListener('submit', function(e) {
    e.preventDefault();
    let isValid = true;
    let firstErrorField = null;

    // Reset all errors
    document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input').forEach(el => el.classList.remove('error'));
    document.querySelectorAll('select').forEach(el => el.classList.remove('error'));
    uploadWrapper.classList.remove('error');

    // Full Name validation
    if (fullName.value.trim() === '') {
        showError('fullName', 'fullNameError', 'This field is required.');
        isValid = false;
        if (!firstErrorField) firstErrorField = fullName;
    }

    // Phone Number validation
    const phoneVal = phoneNumber.value.trim();
    if (phoneVal === '') {
        showError('phoneNumber', 'phoneError', 'This field is required.');
        isValid = false;
        if (!firstErrorField) firstErrorField = phoneNumber;
    } else if (!/^[0-9]+$/.test(phoneVal)) {
        showError('phoneNumber', 'phoneError', 'Phone number must contain only digits.');
        isValid = false;
        if (!firstErrorField) firstErrorField = phoneNumber;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value)) {
        showError('email', 'emailError', 'Invalid email format.');
        isValid = false;
        if (!firstErrorField) firstErrorField = email;
    }

    // Home Address validation
    if (homeAddress.value.trim() === '') {
        showError('homeAddress', 'addressError', 'This field is required.');
        isValid = false;
        if (!firstErrorField) firstErrorField = homeAddress;
    }

    // Profile Picture validation
    if (!profilePicture.files || profilePicture.files.length === 0) {
        fileError.textContent = 'This field is required.';
        fileError.classList.add('show');
        uploadWrapper.classList.add('error');
        isValid = false;
        if (!firstErrorField) firstErrorField = uploadWrapper;
    }

    // Role validation
    if (staffRole.value === '' || staffRole.value === null) {
        showErrorSelect('staffRole', 'roleError', 'This field is required.');
        isValid = false;
        if (!firstErrorField) firstErrorField = staffRole;
    }

    // Manager validation - Skip if field is disabled (Senior Finance Manager auto-selected)
    if (!managerId.disabled && (managerId.value === '' || managerId.value === null)) {
        showErrorSelect('managerId', 'managerError', 'This field is required.');
        isValid = false;
        if (!firstErrorField) firstErrorField = managerId;
    }

    // Password validation - min 8 chars + combination of letters and numbers
    const pwVal = password.value;
    if (pwVal.length < 8 || !/[a-zA-Z]/.test(pwVal) || !/[0-9]/.test(pwVal)) {
        showError('password', 'passwordError', 'Password does not meet the security criteria.');
        isValid = false;
        if (!firstErrorField) firstErrorField = password;
    }

    // Confirm password validation
    if (confirmPassword.value !== password.value) {
        showError('confirmPassword', 'confirmPasswordError', 'Passwords do not match');
        isValid = false;
        if (!firstErrorField) firstErrorField = confirmPassword;
    }

    if (isValid) {
        // Show confirmation modal
        document.getElementById('confirmModal').classList.add('show');
    } else {
        // Scroll to first error
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (firstErrorField === uploadWrapper) {
                uploadWrapper.focus();
            } else {
                firstErrorField.focus();
            }
        }
    }
});

// Modal buttons
document.getElementById('modalNo').addEventListener('click', function() {
    document.getElementById('confirmModal').classList.remove('show');
});

document.getElementById('modalYes').addEventListener('click', function() {
    document.getElementById('confirmModal').classList.remove('show');
    // Submit the form to servlet
    form.submit();
});

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('show');
    }
});

function showError(inputId, errorId, message) {
    document.getElementById(inputId).classList.add('error');
    const errorEl = document.getElementById(errorId);
    errorEl.textContent = message;
    errorEl.classList.add('show');
}

function showErrorSelect(selectId, errorId, message) {
    document.getElementById(selectId).classList.add('error');
    const errorEl = document.getElementById(errorId);
    errorEl.textContent = message;
    errorEl.classList.add('show');
}
