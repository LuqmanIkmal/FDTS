// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

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
    passwordIcon.src = pageData.eyeDisableIcon;  // Hide icon when showing password
    passwordIcon.alt = "Hide Password";
  } else {
    passwordField.type = "password";
    passwordIcon.src = pageData.eyeEnableIcon;   // Show icon when hiding password
    passwordIcon.alt = "Show Password";
  }
});

// Toggle Confirm Password - FIXED: Using correct icon names
const toggleConfirmButton = document.getElementById("toggleConfirm");
const confirmIcon = document.getElementById("confirmIcon");

toggleConfirmButton.addEventListener("click", () => {
  if (confirmField.type === "password") {
    confirmField.type = "text";
    confirmIcon.src = pageData.eyeDisableIcon;  // Hide icon when showing password
    confirmIcon.alt = "Hide Password";
  } else {
    confirmField.type = "password";
    confirmIcon.src = pageData.eyeEnableIcon;   // Show icon when hiding password
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
