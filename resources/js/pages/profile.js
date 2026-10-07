// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Phone number validation - digits only
const phoneInput = document.getElementById('phone');

// Phone validation happens on Update Profile click
phoneInput.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Validate phone on blur
phoneInput.addEventListener('blur', function() {
    const phoneValue = this.value.trim();
    if (phoneValue.length > 0 && !/^[0-9]+$/.test(phoneValue)) {
        showProfileError('Phone number must contain only digits.');
        this.focus();
    }
});

let isChangingPassword = false;
// Passwords are stored hashed, so only a mask is shown
const ORIGINAL_PASSWORD = pageData.passwordMask;

window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === 'true') {
        showSuccessMessage('Profile updated successfully!');
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (urlParams.get('error')) {
        showProfileError(pageData.errorMessage);
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

function showChangePassword() {
    isChangingPassword = true;
    const passwordField = document.getElementById('password');
    const reconfirmGroup = document.getElementById('reconfirmPasswordGroup');
    const changeLink = document.getElementById('changePasswordLink');
    const cancelLink = document.getElementById('cancelPasswordLink');

    passwordField.readOnly = false;
    passwordField.value = '';
    passwordField.placeholder = 'Enter new password';
    passwordField.focus();

    reconfirmGroup.classList.add('show');
    cancelLink.classList.add('show');
    changeLink.style.display = 'none';
}

function cancelChangePassword() {
    isChangingPassword = false;
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const reconfirmGroup = document.getElementById('reconfirmPasswordGroup');
    const changeLink = document.getElementById('changePasswordLink');
    const cancelLink = document.getElementById('cancelPasswordLink');
    const errorMsg = document.getElementById('passwordError');
    const updateBtn = document.getElementById('updateBtn');

    passwordField.readOnly = true;
    passwordField.type = 'password';
    passwordField.value = ORIGINAL_PASSWORD;
    passwordField.placeholder = '';

    confirmPasswordField.type = 'password';
    confirmPasswordField.value = '';

    reconfirmGroup.classList.remove('show');
    cancelLink.classList.remove('show');
    changeLink.style.display = 'inline-block';

    errorMsg.style.display = 'none';
    errorMsg.textContent = '';
    updateBtn.disabled = false;

    document.getElementById('password-toggle-icon').src = pageData.eyeDisableIcon;
    document.getElementById('confirmPassword-toggle-icon').src = pageData.eyeDisableIcon;
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '-toggle-icon');

    if (field.type === 'password') {
        field.type = 'text';
        toggleIcon.src = pageData.eyeEnableIcon;
    } else {
        field.type = 'password';
        toggleIcon.src = pageData.eyeDisableIcon;
    }
}

function validatePasswordMatch() {
    if (!isChangingPassword) return true;

    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorMsg = document.getElementById('passwordError');
    const updateBtn = document.getElementById('updateBtn');

    if (password && confirmPassword) {
        if (password !== confirmPassword) {
            errorMsg.textContent = 'Passwords do not match!';
            errorMsg.classList.add('show');
            updateBtn.disabled = true;
            return false;
        } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
            errorMsg.textContent = 'Password does not meet the security criteria.';
            errorMsg.classList.add('show');
            updateBtn.disabled = true;
            return false;
        } else {
            errorMsg.classList.remove('show');
            updateBtn.disabled = false;
            return true;
        }
    }
    return true;
}

// Password validation happens on Update Profile click only

function handleFileSelect(event) {
    const file = event.target.files[0];
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const pictureError = document.getElementById('pictureError');

    // Clear any previous error when user selects a new file
    pictureError.style.display = 'none';
    pictureError.textContent = '';

    if (file) {
        fileNameDisplay.textContent = file.name;

        // Only preview if it's a valid image type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (validTypes.includes(file.type)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatar = document.getElementById('profileAvatar');
                avatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile Picture">';
            };
            reader.readAsDataURL(file);
        }
    } else {
        fileNameDisplay.textContent = 'Choose File';
    }
}

function showUpdateModal() {
    clearProfileError();

    let hasError = false;

    // Clear inline errors from previous attempt
    const pictureError = document.getElementById('pictureError');
    pictureError.style.display = 'none';
    pictureError.textContent = '';
    const passwordError = document.getElementById('passwordError');
    passwordError.style.display = 'none';
    passwordError.textContent = '';

    // Validate profile picture file type if a file is selected
    const picFile = document.getElementById('profilePicture').files[0];
    if (picFile) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(picFile.type)) {
            pictureError.textContent = 'Invalid file format. Only JPEG and PNG files are accepted.';
            pictureError.style.display = 'block';
            hasError = true;
        }
    }

    // Validate phone - digits only
    const phoneValue = document.getElementById('phone').value.trim();
    if (!phoneValue || !/^[0-9]+$/.test(phoneValue)) {
        showProfileError('Phone number must contain only digits.');
        hasError = true;
    }

    // Validate password if user is changing it
    if (isChangingPassword) {
        const password = document.getElementById('password').value;
        const confirmPwd = document.getElementById('confirmPassword').value;

        if (!password || !confirmPwd) {
            passwordError.textContent = 'Please enter and confirm your new password.';
            passwordError.style.display = 'block';
            hasError = true;
        } else if (password !== confirmPwd) {
            passwordError.textContent = 'Passwords do not match!';
            passwordError.style.display = 'block';
            hasError = true;
        } else if (password.length < 8 || !/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
            passwordError.textContent = 'Password does not meet the security criteria.';
            passwordError.style.display = 'block';
            hasError = true;
        }
    }

    // Only show confirmation modal if all validation passed
    if (hasError) return;
    document.getElementById('updateModal').classList.add('active');
}

function showProfileError(message) {
    const errToast = document.getElementById('errorToast');
    errToast.textContent = message;
    errToast.classList.add('show');

    setTimeout(function() {
        errToast.classList.remove('show');
        errToast.classList.add('hide');
        setTimeout(function() {
            errToast.classList.remove('hide');
        }, 400);
    }, 3000);
}

function clearProfileError() {
    const errToast = document.getElementById('errorToast');
    if (errToast) {
        errToast.classList.remove('show');
        errToast.classList.remove('hide');
    }
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('active');
}

function confirmUpdate() {
    if (!isChangingPassword) {
        document.getElementById('password').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    document.getElementById('profileForm').submit();
}

function showSuccessMessage(message) {
    const successMsg = document.getElementById('successMessage');
    successMsg.textContent = message;
    successMsg.classList.add('show');

    setTimeout(function() {
        successMsg.classList.remove('show');
        successMsg.classList.add('hide');

        setTimeout(function() {
            successMsg.classList.remove('hide');
        }, 400);
    }, 3000);
}

document.getElementById('updateModal').addEventListener('click', function(e) {
    if (e.target === this) closeUpdateModal();
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, {
    togglePassword,
    showChangePassword,
    cancelChangePassword,
    handleFileSelect,
    showUpdateModal,
    closeUpdateModal,
    confirmUpdate,
});
