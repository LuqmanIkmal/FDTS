// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Auto-calculate maturity date based on start date and tenure
function calculateMaturityDate() {
    var startDate = document.getElementById('startDate').value;
    var tenure = parseInt(document.getElementById('tenure').value) || 0;

    if (startDate && tenure > 0) {
        var start = new Date(startDate);
        var originalDay = start.getDate();
        start.setMonth(start.getMonth() + tenure);

        if (start.getDate() !== originalDay) {
            start.setDate(0);
        }

        var year = start.getFullYear();
        var month = String(start.getMonth() + 1).padStart(2, '0');
        var day = String(start.getDate()).padStart(2, '0');

        document.getElementById('maturityDate').value = year + '-' + month + '-' + day;
    }
}

// Show notification function
function showNotification(message, isTurnedOn) {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;

    if (isTurnedOn) {
        notification.classList.add('turned-on');
    } else {
        notification.classList.add('turned-off');
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    setTimeout(() => {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// Handle FD Type change - show/hide conditional fields
function handleCollateralChange() {
    const collateralStatus = document.getElementById('collateralStatus').value;
    const depositVal = parseFloat(document.getElementById('depositAmount').value) || 0;
    const pledgeInput = document.getElementById('pledgeValue');
    const pledgeHint = document.getElementById('pledgeValueHint');

    if (collateralStatus === 'Y') {
        // Active - auto fill full deposit amount, read-only
        pledgeInput.value = depositVal > 0 ? depositVal.toFixed(2) : '';
        pledgeInput.removeAttribute('max');
        pledgeInput.readOnly = true;
        pledgeInput.style.cursor = 'not-allowed';
        pledgeHint.style.display = 'none';
    } else if (collateralStatus === 'N') {
        // Partial - max is half of deposit amount
        const maxPartial = depositVal > 0 ? (depositVal / 2).toFixed(2) : 0;
        pledgeInput.value = '';
        pledgeInput.setAttribute('max', maxPartial);
        pledgeInput.readOnly = false;
        pledgeInput.style.cursor = 'auto';
        if (depositVal > 0) {
            pledgeHint.textContent = 'Maximum pledge value for Partial: RM ' + parseFloat(maxPartial).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            pledgeHint.style.display = 'block';
        } else {
            pledgeHint.textContent = 'Please enter Deposit Amount first.';
            pledgeHint.style.display = 'block';
        }
    } else {
        // No selection - reset
        pledgeInput.value = '';
        pledgeInput.removeAttribute('max');
        pledgeInput.readOnly = false;
        pledgeInput.style.cursor = 'auto';
        pledgeHint.style.display = 'none';
    }
}

// When deposit amount changes, update pledge value if collateral is already set
document.getElementById('depositAmount').addEventListener('input', function() {
    const collateralStatus = document.getElementById('collateralStatus').value;
    if (collateralStatus) handleCollateralChange();
});

function handleFDTypeChange() {
    const fdType = document.getElementById('fdType').value;

    const autoRenewalField = document.getElementById('autoRenewalField');
    const withdrawableField = document.getElementById('withdrawableField');
    const pledgeValueField = document.getElementById('pledgeValueField');
    const collateralField = document.getElementById('collateralField');

    const autoRenewalStatus = document.getElementById('autoRenewalStatus');
    const withdrawableStatus = document.getElementById('withdrawableStatus');
    const pledgeValue = document.getElementById('pledgeValue');
    const collateralStatus = document.getElementById('collateralStatus');

    // Hide all conditional fields first
    autoRenewalField.style.display = 'none';
    withdrawableField.style.display = 'none';
    pledgeValueField.style.display = 'none';
    collateralField.style.display = 'none';

    // Remove required attribute from all conditional fields
    autoRenewalStatus.removeAttribute('required');
    withdrawableStatus.removeAttribute('required');
    pledgeValue.removeAttribute('required');
    collateralStatus.removeAttribute('required');

    // Show relevant fields based on FD Type
    if (fdType === 'Free') {
        autoRenewalField.style.display = 'flex';
        withdrawableField.style.display = 'flex';
        autoRenewalStatus.setAttribute('required', 'required');
        withdrawableStatus.setAttribute('required', 'required');
    } else if (fdType === 'Pledge') {
        pledgeValueField.style.display = 'flex';
        collateralField.style.display = 'flex';
        pledgeValue.setAttribute('required', 'required');
        collateralStatus.setAttribute('required', 'required');
    }
}

// Toggle reminder handlers
document.getElementById('reminderMaturity').addEventListener('change', function() {
    if (this.checked) {
        showNotification('Fixed Deposit Maturity Reminder turned on!', true);
    } else {
        showNotification('Fixed Deposit Maturity Reminder turned off!', false);
    }
});

document.getElementById('reminderIncomplete').addEventListener('change', function() {
    if (this.checked) {
        showNotification('Incomplete Fixed Deposit Details Reminder turned on!', true);
    } else {
        showNotification('Incomplete Fixed Deposit Details Reminder turned off!', false);
    }
});

// File upload display
document.getElementById('fdCertificate').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Choose File';
    const fileText = document.getElementById('fileText');
    if (fileName !== 'Choose File') {
        fileText.textContent = fileName;
    }
});

// Calculate interest and show modal
function calculateInterest() {
    const depositAmount = parseFloat(document.getElementById('depositAmount').value) || 0;
    const interestRate = parseFloat(document.getElementById('interestRate').value) || 0;
    const tenure = parseFloat(document.getElementById('tenure').value) || 0;

    if (depositAmount && interestRate && tenure) {
        const interest = (depositAmount * interestRate * tenure) / (100 * 12);
        const totalAmount = depositAmount + interest;

        document.getElementById('totalProfit').value = totalAmount.toFixed(2);
        document.getElementById('totalInterest').value = interest.toFixed(2);
        document.getElementById('calculationModal').classList.add('show');
    } else {
        showNotification('Please fill in Deposit Amount, Interest Rate, and Tenure to calculate.', false);
    }
}

// Close calculation modal
function closeCalculationModal() {
    document.getElementById('calculationModal').classList.remove('show');
}

// Close modal when clicking outside
document.getElementById('calculationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCalculationModal();
    }
});

// Block e, E, + in number inputs (allow '-' so negative values trigger validation error)
['depositAmount', 'interestRate', 'tenure', 'pledgeValue'].forEach(function(id) {
    const field = document.getElementById(id);
    if (field) {
        field.addEventListener('keydown', function(e) {
            if (['e', 'E', '+'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }
});

// Form validation before submit
document.getElementById('createFDForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let hasError = false;

    // Clear previous custom errors
    document.querySelectorAll('.fd-field-error').forEach(el => el.remove());

    function showFDError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        const err = document.createElement('div');
        err.className = 'fd-field-error';
        err.style.cssText = 'color:#e74c3c; font-size:13px; margin-top:4px;';
        err.textContent = message;

        // Use .closest() to find the main field container instead of the direct parent
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.appendChild(err);
        } else {
            field.parentNode.appendChild(err);
        }

        hasError = true;
    }

    // Required field checks - "This field is required."
    const requiredFields = ['accountNumber', 'bankName', 'depositAmount', 'interestRate', 'startDate', 'tenure', 'maturityDate', 'fdType'];
    requiredFields.forEach(function(id) {
        const field = document.getElementById(id);
        if (field && !field.value.trim()) {
            showFDError(id, 'This field is required.');
        }
    });

    // Free FD type required fields
    const fdTypeVal = document.getElementById('fdType').value;
    if (fdTypeVal === 'Free') {
        if (!document.getElementById('autoRenewalStatus').value) {
            showFDError('autoRenewalStatus', 'This field is required.');
        }
        if (!document.getElementById('withdrawableStatus').value) {
            showFDError('withdrawableStatus', 'This field is required.');
        }
    }

    // Pledge FD type required fields
    if (fdTypeVal === 'Pledge') {
        if (!document.getElementById('pledgeValue').value) {
            showFDError('pledgeValue', 'This field is required.');
        }
        if (!document.getElementById('collateralStatus').value) {
            showFDError('collateralStatus', 'This field is required.');
        }
    }

    // Referral Number - alphanumeric only (letters and numbers, no special chars)
    const referralVal = document.getElementById('referralNumber').value.trim();
    if (referralVal && !/^[a-zA-Z0-9]+$/.test(referralVal)) {
        showFDError('referralNumber', 'Referral number must contain only letters and numbers.');
    }

    // Deposit amount validation
    const depositVal = document.getElementById('depositAmount').value;
    if (depositVal) {
        const depositNum = parseFloat(depositVal);
        if (isNaN(depositNum)) {
            showFDError('depositAmount', 'Deposit amount must contain only digits without letters and symbols.');
        } else if (depositNum <= 0) {
            showFDError('depositAmount', 'Deposit amount must be greater than zero.');
        }
    }

    // Pledge value validation (if visible)
    const pledgeField = document.getElementById('pledgeValue');
    const pledgeVisible = pledgeField && pledgeField.offsetParent !== null;
    if (pledgeVisible && pledgeField.value) {
        const pledgeNum = parseFloat(pledgeField.value);
        if (isNaN(pledgeNum)) {
            showFDError('pledgeValue', 'Pledge value must contain only digits without letters and symbols.');
        } else if (pledgeNum <= 0) {
            showFDError('pledgeValue', 'Pledge value must be greater than zero.');
        } else {
            const collateralVal = document.getElementById('collateralStatus').value;
            const depositNum = parseFloat(document.getElementById('depositAmount').value) || 0;
            if (collateralVal === 'N' && depositNum > 0 && pledgeNum > depositNum / 2) {
                showFDError('pledgeValue', 'Pledge value cannot exceed RM ' + (depositNum / 2).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (half of deposit amount) for Partial collateral.');
            }
        }
    }

    // Certificate file type validation - JPEG, PNG, PDF only
    const certFile = document.getElementById('fdCertificate');
    if (certFile && certFile.files.length > 0) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!validTypes.includes(certFile.files[0].type)) {
            showFDError('fdCertificate', 'Invalid file format. Only JPEG, PDF, and PNG files are accepted.');
        }
    }

    if (hasError) return;

    const startDate = new Date(document.getElementById('startDate').value);
    const maturityDate = new Date(document.getElementById('maturityDate').value);

    if (maturityDate < startDate) {
        showFDError('maturityDate', 'Maturity Date must be after Start Date.');
        return;
    }

    this.submit();
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand Fixed Deposits dropdown in sidebar
    const fdDropdown = document.getElementById('fdDropdown');
    const fdNavItem = document.getElementById('fdNavItem');

    if (fdDropdown && fdNavItem) {
        fdDropdown.classList.add('show');
        fdNavItem.classList.add('open');
    }

    // Show FD Type conditional fields if value is pre-selected (from session)
    const fdType = document.getElementById('fdType').value;
    if (fdType) {
        handleFDTypeChange();
    }

    // Auto-calculate maturity date on page load if start date and tenure exist
    const startDate = document.getElementById('startDate').value;
    const tenure = document.getElementById('tenure').value;
    if (startDate && tenure) {
        calculateMaturityDate();
    }

    // Show file name if previously uploaded
    if (pageData.fdCertFileName !== '') {
        document.getElementById('fileText').textContent = pageData.fdCertFileName;
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, {
    calculateInterest,
    calculateMaturityDate,
    handleFDTypeChange,
    handleCollateralChange,
    closeCalculationModal,
});
