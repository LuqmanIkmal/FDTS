// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// ========== MATURITY DATE CONSTANT ==========
const MATURITY_DATE = pageData.maturityDate;

// ========== TRANSACTION DATE VALIDATION ==========
function validateTransactionDate(field) {
    const status = document.getElementById('fdStatus').value;

    // Only validate when status is Matured
    if (status !== 'Matured') return true;

    const selectedDate = field.value;
    if (!selectedDate) return true;

    if (selectedDate < MATURITY_DATE) {
        showFieldError(field.id, '❌ Date cannot be before maturity date (' + formatDate(MATURITY_DATE) + ')');
        field.value = ''; // Clear invalid date
        return false;
    }

    // Clear any existing error
    field.classList.remove('field-error');
    const existingError = field.parentElement.parentElement.querySelector('.error-message');
    if (existingError) existingError.remove();

    return true;
}

// Format date for display (yyyy-mm-dd to dd/mm/yyyy)
function formatDate(dateStr) {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    return dateStr;
}

// Validate all transaction dates before form submission
function validateAllTransactionDates() {
    const status = document.getElementById('fdStatus').value;
    if (status !== 'Matured') return true;

    const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;

    if (transactionType === 'Withdraw') {
        const transDateField = document.getElementById('transactionDate');
        if (transDateField && transDateField.value) {
            if (!validateTransactionDate(transDateField)) return false;
        }
    } else if (transactionType === 'Reinvest') {
        const reinvestTransDateField = document.getElementById('reinvestTransactionDate');
        const newStartDateField = document.getElementById('newStartDate');

        if (reinvestTransDateField && reinvestTransDateField.value) {
            if (!validateTransactionDate(reinvestTransDateField)) return false;
        }
        if (newStartDateField && newStartDateField.value) {
            if (!validateTransactionDate(newStartDateField)) return false;
        }
    }

    return true;
}

// ========== UPDATE CONFIRMATION MODAL FUNCTIONS ==========
function showUpdateModal() {
    // Validate form before showing modal
    const form = document.getElementById('updateFDForm');

    // Check basic form validation
    if (!form.checkValidity()) {
        // Show "This field is required." for empty required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(function(field) {
            if (!field.value || field.value.trim() === '') {
                showFieldError(field.id, 'This field is required.');
            }
        });
        return;
    }

    // Validate deposit amount - digits only and > 0
    const depositAmountVal = document.getElementById('depositAmount').value;
    if (depositAmountVal) {
        const depositNum = parseFloat(depositAmountVal);
        if (isNaN(depositNum)) {
            showFieldError('depositAmount', 'Deposit amount must contain only digits without letters and symbols.');
            return;
        } else if (depositNum <= 0) {
            showFieldError('depositAmount', 'Deposit amount must be greater than zero.');
            return;
        }
    }

    // Run custom validations
    clearFieldErrors();

    const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
    const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;

    // Validate transaction dates (must be >= maturity date when status is Matured)
    if (!validateAllTransactionDates()) {
        return;
    }

    // Validate pledge value
    if (fdType === 'Pledge') {
        const pledgeValue = document.getElementById('pledgeValue')?.value;
        if (pledgeValue) {
            if (!validatePledgeValue()) {
                return;
            }
        }
    }

    // Validate withdrawal
    if (transactionType === 'Withdraw') {
        if (!validateWithdrawAmount()) {
            return;
        }
    }

    // Show the modal
    document.getElementById('updateModal').classList.add('active');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('active');
}

function confirmUpdate() {
    closeUpdateModal();
    document.getElementById('updateFDForm').submit();
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('updateModal').addEventListener('click', function(e) {
        if (e.target === this) closeUpdateModal();
    });
});

// ========== FD TYPE AND STATUS FUNCTIONS ==========
// Handle FD Type change (Pledge/Free)
function handleFDTypeChange() {
    const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
    document.getElementById('pledgeFields').style.display = fdType === 'Pledge' ? 'block' : 'none';
    document.getElementById('freeFields').style.display = fdType === 'Free' ? 'block' : 'none';

    // Update transaction options when FD type changes
    updateTransactionOptions();
}

// Handle Status change - Show/Hide Transaction section
function handleStatusChange() {
    const status = document.getElementById('fdStatus').value;
    const transactionSection = document.getElementById('transactionSection');

    if (status === 'Matured') {
        transactionSection.style.display = 'block';
        updateTransactionOptions(); // Update options when showing
    } else {
        transactionSection.style.display = 'none';
        // Reset transaction fields when hiding
        const withdrawRadio = document.getElementById('transWithdraw');
        const reinvestRadio = document.getElementById('transReinvest');
        if (withdrawRadio) withdrawRadio.checked = false;
        if (reinvestRadio) reinvestRadio.checked = false;

        const withdrawFields = document.getElementById('withdrawFields');
        const reinvestFields = document.getElementById('reinvestFields');
        if (withdrawFields) withdrawFields.style.display = 'none';
        if (reinvestFields) reinvestFields.style.display = 'none';
    }
}

// Update transaction options based on current FD type and settings
function updateTransactionOptions() {
    const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
    const isFreeFD = (fdType === 'Free');
    const isPledgeFD = (fdType === 'Pledge');

    console.log('🔄 Updating transaction options for:', fdType);

    // ==================== HANDLE PLEDGE FD FIRST ====================
    const pledgeContainer = document.getElementById('pledgeMessagesContainer');

    if (isPledgeFD) {
        // Show Pledge FD messages
        if (pledgeContainer) {
            pledgeContainer.style.display = 'block';
            console.log('✅ Showing Pledge FD messages');
        }

        // Hide ALL Free FD options and messages
        const autoRenewMsg = document.getElementById('autoRenewMessage');
        const reinvestContainer = document.getElementById('reinvestOptionContainer');
        const noWithdrawMsg = document.getElementById('noWithdrawMessage');
        const withdrawContainer = document.getElementById('withdrawOptionContainer');

        if (autoRenewMsg) autoRenewMsg.style.display = 'none';
        if (reinvestContainer) reinvestContainer.style.display = 'none';
        if (noWithdrawMsg) noWithdrawMsg.style.display = 'none';
        if (withdrawContainer) withdrawContainer.style.display = 'none';

        console.log('✅ Hidden all Free FD options');
        return; // EXIT EARLY - Don't process Free FD logic
    }

    // ==================== HANDLE FREE FD ====================
    // Hide Pledge messages for Free FD
    if (pledgeContainer) {
        pledgeContainer.style.display = 'none';
    }

    // Get Free FD settings
    const autoRenewalSelect = document.getElementById('autoRenewalStatus');
    const withdrawableSelect = document.getElementById('withdrawableStatus');
    const autoRenewal = autoRenewalSelect ? autoRenewalSelect.value : 'No';
    const withdrawable = withdrawableSelect ? withdrawableSelect.value : 'No';

    console.log('📊 Free FD settings:', {
        autoRenewal: autoRenewal,
        withdrawable: withdrawable
    });

    // Calculate what to show for Free FD
    const showAutoRenewMessage = (autoRenewal === 'Yes');
    const showReinvestOption = (autoRenewal === 'No');
    const showNoWithdrawMessage = (withdrawable === 'No');
    const showWithdrawOption = (withdrawable === 'Full' || withdrawable === 'Partial');

    console.log('📊 Display logic:', {
        showAutoRenewMessage: showAutoRenewMessage,
        showReinvestOption: showReinvestOption,
        showNoWithdrawMessage: showNoWithdrawMessage,
        showWithdrawOption: showWithdrawOption
    });

    // Update visibility for Free FD
    const autoRenewMsg = document.getElementById('autoRenewMessage');
    const reinvestContainer = document.getElementById('reinvestOptionContainer');
    const noWithdrawMsg = document.getElementById('noWithdrawMessage');
    const withdrawContainer = document.getElementById('withdrawOptionContainer');

    if (autoRenewMsg) {
        autoRenewMsg.style.display = showAutoRenewMessage ? 'block' : 'none';
        console.log('Auto-renew message:', showAutoRenewMessage ? 'visible' : 'hidden');
    }

    if (reinvestContainer) {
        reinvestContainer.style.display = showReinvestOption ? 'block' : 'none';
        console.log('Reinvest option:', showReinvestOption ? 'visible' : 'hidden');
    }

    if (noWithdrawMsg) {
        noWithdrawMsg.style.display = showNoWithdrawMessage ? 'block' : 'none';
        console.log('No-withdraw message:', showNoWithdrawMessage ? 'visible' : 'hidden');
    }

    if (withdrawContainer) {
        withdrawContainer.style.display = showWithdrawOption ? 'block' : 'none';
        console.log('Withdraw option:', showWithdrawOption ? 'visible' : 'hidden');
    }

    // Update withdraw label
    const withdrawLabel = document.getElementById('withdrawLabel');
    if (withdrawLabel) {
        withdrawLabel.textContent = showReinvestOption ? '' : 'Transaction Type';
    }

    // Reset radio buttons and hide fields when options change
    const withdrawRadio = document.getElementById('transWithdraw');
    const reinvestRadio = document.getElementById('transReinvest');
    if (withdrawRadio) withdrawRadio.checked = false;
    if (reinvestRadio) reinvestRadio.checked = false;

    const withdrawFields = document.getElementById('withdrawFields');
    const reinvestFields = document.getElementById('reinvestFields');
    if (withdrawFields) withdrawFields.style.display = 'none';
    if (reinvestFields) reinvestFields.style.display = 'none';

    console.log('✅ Transaction options updated successfully');
}

// Handle Transaction Type change (Withdraw/Reinvest)
function handleTransactionTypeChange() {
    const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;
    const withdrawFields = document.getElementById('withdrawFields');
    const reinvestFields = document.getElementById('reinvestFields');

    console.log('💳 Transaction type changed to:', transactionType);

    if (withdrawFields && reinvestFields) {
        if (transactionType === 'Withdraw') {
            withdrawFields.style.display = 'block';
            reinvestFields.style.display = 'none';
            console.log('✅ Showing withdraw fields');
        } else if (transactionType === 'Reinvest') {
            withdrawFields.style.display = 'none';
            reinvestFields.style.display = 'block';
            console.log('✅ Showing reinvest fields');
        } else {
            withdrawFields.style.display = 'none';
            reinvestFields.style.display = 'none';
        }
    } else {
        console.error('❌ Could not find withdrawFields or reinvestFields');
    }
}

// Calculate maturity date for original FD
function calculateMaturityDate() {
    const startDate = document.getElementById('startDate').value;
    const tenure = parseInt(document.getElementById('tenure').value) || 0;

    if (startDate && tenure > 0) {
        const start = new Date(startDate);
        const originalDay = start.getDate();
        start.setMonth(start.getMonth() + tenure);
        if (start.getDate() !== originalDay) start.setDate(0);

        const year = start.getFullYear();
        const month = String(start.getMonth() + 1).padStart(2, '0');
        const day = String(start.getDate()).padStart(2, '0');
        document.getElementById('maturityDate').value = year + '-' + month + '-' + day;
    }
}

// Calculate new maturity date for Reinvest
function calculateNewMaturityDate() {
    const newStartDate = document.getElementById('newStartDate')?.value;
    const newTenure = parseInt(document.getElementById('newTenure')?.value) || 0;

    if (newStartDate && newTenure > 0) {
        const start = new Date(newStartDate);
        const originalDay = start.getDate();
        start.setMonth(start.getMonth() + newTenure);
        if (start.getDate() !== originalDay) start.setDate(0);

        const year = start.getFullYear();
        const month = String(start.getMonth() + 1).padStart(2, '0');
        const day = String(start.getDate()).padStart(2, '0');
        const newMaturityField = document.getElementById('newMaturityDate');
        if (newMaturityField) {
            newMaturityField.value = year + '-' + month + '-' + day;
        }
    }
}

// Calculate interest
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

document.getElementById('calculationModal').addEventListener('click', function(e) {
    if (e.target === this) closeCalculationModal();
});

// Show notification
function showNotification(message, isSuccess) {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = 'notification ' + (isSuccess ? 'success' : 'error');
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// Reminder toggle handlers
document.getElementById('reminderMaturity').addEventListener('change', function() {
    showNotification(this.checked ? 'Fixed Deposit Maturity Reminder turned on!' : 'Fixed Deposit Maturity Reminder turned off!', this.checked);
});

document.getElementById('reminderIncomplete').addEventListener('change', function() {
    showNotification(this.checked ? 'Incomplete Fixed Deposit Details Reminder turned on!' : 'Incomplete Fixed Deposit Details Reminder turned off!', this.checked);
});

// File upload display
document.getElementById('fdCertificate').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Choose File';
    document.getElementById('fileName').textContent = fileName;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand Fixed Deposits menu in sidebar
    const fdDropdown = document.getElementById('fdDropdown');
    const fdNavItem = document.getElementById('fdNavItem');
    if (fdDropdown && fdNavItem) {
        fdDropdown.classList.add('show');
        fdNavItem.classList.add('open');
    }

    // Initialize transaction options on load
    updateTransactionOptions();

    console.log('Page loaded - Initial settings:', {
        reminderMaturity: pageData.reminderMaturity,
        reminderIncomplete: pageData.reminderIncomplete,
        fdType: pageData.fdType,
        autoRenewal: pageData.autoRenewal,
        withdrawable: pageData.withdrawable
    });
});

// Show inline error message
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    // Add error styling
    field.classList.add('field-error');

    // Remove existing error message
    const existingError = field.parentElement.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Add error message
    const errorSpan = document.createElement('span');
    errorSpan.className = 'error-message';
    errorSpan.textContent = message;
    field.parentElement.parentElement.appendChild(errorSpan);

    // Focus on field
    field.focus();

    // Remove error styling when user starts typing
    field.addEventListener('input', function() {
        field.classList.remove('field-error');
        const error = field.parentElement.parentElement.querySelector('.error-message');
        if (error) error.remove();
    }, { once: true });
}

// Clear all field errors
function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
        el.classList.remove('field-error');
    });
    document.querySelectorAll('.error-message').forEach(el => {
        el.remove();
    });
}

// Validate withdraw amount
function validateWithdrawAmount() {
    const withdrawAmount = parseFloat(document.getElementById('withdrawAmount')?.value || 0);
    const withdrawableStatus = document.getElementById('withdrawableStatus')?.value;
    const remainingBalance = pageData.remainingBalance;
    const depositAmount = pageData.depositAmount;

    if (withdrawAmount <= 0) return true;

    // Check remaining balance
    if (withdrawAmount > remainingBalance) {
        showFieldError('withdrawAmount',
            '❌ Withdrawal (RM ' + withdrawAmount.toFixed(2) + ') exceeds balance (RM ' + remainingBalance.toFixed(2) + ')');
        return false;
    }

    // Check half limit for Partial
    if (withdrawableStatus === 'Partial') {
        const halfBalance = remainingBalance / 2;
        if (withdrawAmount > halfBalance) {
            showFieldError('withdrawAmount',
                '❌ Partial withdrawal max RM ' + halfBalance.toFixed(2) + ' (half of balance RM ' + remainingBalance.toFixed(2) + ')');
            return false;
        }
    }

    return true;
}

// WITHDRAW FIELD LOGIC - FIXED
function updateWithdrawAmountLimit() {
    const withdrawAmountField = document.getElementById('withdrawAmount');
    const withdrawableStatusField = document.getElementById('withdrawableStatus');

    if (!withdrawAmountField || !withdrawableStatusField) return;

    const withdrawableStatus = withdrawableStatusField.value;  // Get current value
    const remainingBalance = pageData.remainingBalance;
    const depositAmount = pageData.depositAmount;

    let maxWithdraw = remainingBalance;
    let placeholderText = '';

    // ==== CONDITION BASED ON withdrawableStatus ====
    if (withdrawableStatus === 'Full') {
        maxWithdraw = remainingBalance;
        placeholderText = 'Max: RM ' + remainingBalance.toFixed(2);
    } else if (withdrawableStatus === 'Partial') {
        const halfBalance = remainingBalance / 2;  // Half of BALANCE
        maxWithdraw = halfBalance;
        placeholderText = 'Max: RM ' + halfBalance.toFixed(2) + ' (half)';
    }

    withdrawAmountField.max = maxWithdraw;
    withdrawAmountField.placeholder = placeholderText;

    // revalidate current input
    if (withdrawAmountField.value) {
        validateWithdrawAmount();
    }
}


// initialize on load




// Validate pledge value
function validatePledgeValue() {
    const pledgeInput = document.getElementById('pledgeValue');
    const pledgeValue = parseFloat(pledgeInput?.value || 0);
    const collateralStatus = document.getElementById('collateralStatus')?.value;
    const depositAmount = pageData.depositAmount;

    if (pledgeValue <= 0) {
        showFieldError('pledgeValue', 'Pledge value must be greater than zero.');
        return false;
    }

    if (collateralStatus === 'Active') {
        if (pledgeValue > depositAmount) {
            showFieldError('pledgeValue',
                'Pledge value cannot exceed RM ' + depositAmount.toFixed(2) + ' (full deposit amount).');
            return false;
        }
    } else if (collateralStatus === 'Partial') {
        const halfDeposit = depositAmount / 2;
        if (pledgeValue > halfDeposit) {
            showFieldError('pledgeValue',
                'Pledge value cannot exceed RM ' + halfDeposit.toFixed(2) + ' (half of deposit amount) for Partial collateral.');
            return false;
        }
    }

    return true;
}

// Update pledge value when collateral status changes (mirrors CreateFD behaviour)
function updatePledgeValueLimit() {
    clearFieldErrors();
    const collateralStatus = document.getElementById('collateralStatus')?.value;
    const depositAmount = pageData.depositAmount;
    const pledgeValueInput = document.getElementById('pledgeValue');
    let pledgeHint = document.getElementById('pledgeValueHint');

    // Create hint div if not present
    if (!pledgeHint) {
        pledgeHint = document.createElement('div');
        pledgeHint.id = 'pledgeValueHint';
        pledgeHint.style.cssText = 'font-size:12px; color:#666; margin-top:4px; display:none;';
        pledgeValueInput.parentNode.appendChild(pledgeHint);
    }

    if (!pledgeValueInput) return;

    if (collateralStatus === 'Active') {
        // Auto-fill full deposit amount, read-only
        pledgeValueInput.value = depositAmount > 0 ? depositAmount.toFixed(2) : '';
        pledgeValueInput.removeAttribute('max');
        pledgeValueInput.readOnly = true;
        pledgeValueInput.style.cursor = 'not-allowed';
        pledgeHint.style.display = 'none';
    } else if (collateralStatus === 'Partial') {
        // Max is half of deposit, editable
        const halfDeposit = (depositAmount / 2).toFixed(2);
        pledgeValueInput.value = '';
        pledgeValueInput.setAttribute('max', halfDeposit);
        pledgeValueInput.readOnly = false;
        pledgeValueInput.style.cursor = 'auto';
        if (depositAmount > 0) {
            pledgeHint.textContent = 'Maximum pledge value for Partial: RM ' + parseFloat(halfDeposit).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            pledgeHint.style.display = 'block';
        } else {
            pledgeHint.textContent = 'Please enter Deposit Amount first.';
            pledgeHint.style.display = 'block';
        }
    } else {
        pledgeValueInput.value = '';
        pledgeValueInput.removeAttribute('max');
        pledgeValueInput.readOnly = false;
        pledgeValueInput.style.cursor = 'auto';
        pledgeHint.style.display = 'none';
    }
}

// Form submit validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateFDForm');

    form.addEventListener('submit', function(e) {
        clearFieldErrors();

        const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
        const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;

        // Validate transaction dates (must be >= maturity date when status is Matured)
        if (!validateAllTransactionDates()) {
            e.preventDefault();
            return false;
        }

        // Validate pledge value
        if (fdType === 'Pledge') {
            const pledgeValue = document.getElementById('pledgeValue')?.value;
            if (pledgeValue && parseFloat(pledgeValue) > 0) {
                if (!validatePledgeValue()) {
                    e.preventDefault();
                    return false;
                }
            }
        }

        // Validate withdrawal
        if (transactionType === 'Withdraw') {
            if (!validateWithdrawAmount()) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Add onchange handlers
    const pledgeValueField = document.getElementById('pledgeValue');
    if (pledgeValueField) {
        pledgeValueField.addEventListener('change', validatePledgeValue);
    }

    const withdrawAmountField = document.getElementById('withdrawAmount');
    if (withdrawAmountField) {
        withdrawAmountField.addEventListener('change', validateWithdrawAmount);
    }

    const collateralStatusField = document.getElementById('collateralStatus');
    if (collateralStatusField) {
        collateralStatusField.addEventListener('change', updatePledgeValueLimit);
        // Run on page load to apply correct state for existing collateral status
        updatePledgeValueLimit();
    }

    // Initialize and handle withdrawable status changes
    updateWithdrawAmountLimit();

    const withdrawableStatusField = document.getElementById("withdrawableStatus");
    if (withdrawableStatusField) {
        withdrawableStatusField.addEventListener("change", function() {
            updateWithdrawAmountLimit();
        });
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, {
    handleFDTypeChange,
    updateTransactionOptions,
    calculateMaturityDate,
    calculateInterest,
    handleStatusChange,
    handleTransactionTypeChange,
    validateTransactionDate,
    calculateNewMaturityDate,
    showUpdateModal,
    closeCalculationModal,
    closeUpdateModal,
    confirmUpdate,
});
