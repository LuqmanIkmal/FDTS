// Phone number validation - digits only
const bankPhoneInput = document.getElementById('bankPhone');

// Phone validation happens on Register Bank click
bankPhoneInput.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Auto-expand Bank dropdown in sidebar
document.addEventListener('DOMContentLoaded', function() {
    const bankDropdown = document.getElementById('bankDropdown');
    const bankNavItem = document.getElementById('bankNavItem');

    if (bankDropdown && bankNavItem) {
        bankDropdown.classList.add('show');
        bankNavItem.classList.add('open');
    }
});

function showConfirmation() {
    const bankName = document.getElementById('bankName').value.trim();
    const bankPhone = document.getElementById('bankPhone').value.trim();
    const bankAddress = document.getElementById('bankAddress').value.trim();
    let hasError = false;

    // Clear previous errors
    ['bankNameError','bankPhoneError','bankAddressError'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    if (!bankName) {
        showFieldError('bankName', 'This field is required.'); hasError = true;
    }
    if (!bankPhone) {
        showFieldError('bankPhone', 'This field is required.'); hasError = true;
    } else if (!/^[0-9]+$/.test(bankPhone)) {
        showFieldError('bankPhone', 'Phone number must contain only digits.'); hasError = true;
    }
    if (!bankAddress) {
        showFieldError('bankAddress', 'This field is required.'); hasError = true;
    }
    if (hasError) return;

    document.getElementById('confirmationModal').classList.add('active');
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    let errEl = document.getElementById(fieldId + 'Error');
    if (!errEl) {
        errEl = document.createElement('div');
        errEl.id = fieldId + 'Error';
        errEl.style.cssText = 'color:#d92d20; font-size:13px; margin-top:4px;';
        field.parentNode.appendChild(errEl);
    }
    errEl.textContent = message;
    errEl.style.display = 'block';
}

function closeConfirmation() {
    document.getElementById('confirmationModal').classList.remove('active');
}

function confirmCreate() {
    closeConfirmation();
    document.getElementById('createBankForm').submit();
}

// Close modal when clicking outside
document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmation();
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { showConfirmation, closeConfirmation, confirmCreate });
