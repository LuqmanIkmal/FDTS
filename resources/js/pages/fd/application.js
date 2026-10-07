// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// FD data (draft from session, or the saved FD in view mode)
const fdData = pageData.fdData;

// Bank addresses
const bankAddresses = {
    'Maybank': { line1: 'Cawangan Jalan Gombak,', line2: 'Selangor.' },
    'CIMB': { line1: 'Menara CIMB, Jalan Stesen Sentral 2,', line2: 'Kuala Lumpur Sentral, 50470 Kuala Lumpur.' },
    'MBSB': { line1: 'Menara MBSB, Jalan Kia Peng,', line2: '50450 Kuala Lumpur.' },
    'Public Bank': { line1: 'Menara Public Bank, Jalan Raja Chulan,', line2: '50200 Kuala Lumpur.' },
    'RHB Bank': { line1: 'RHB Centre, Jalan Tun Razak,', line2: '50400 Kuala Lumpur.' },
    'Hong Leong Bank': { line1: 'Menara Hong Leong, Damansara City,', line2: '50490 Kuala Lumpur.' },
    'AmBank': { line1: 'Menara AmBank, Jalan Yap Kwan Seng,', line2: '50450 Kuala Lumpur.' }
};

// Initialize page
window.addEventListener('DOMContentLoaded', function() {
    populateForm();
    setDocumentDate();
});

function populateForm() {
    // Format deposit amount
    if (fdData.depositAmount) {
        const amount = parseFloat(fdData.depositAmount).toLocaleString('en-MY', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('fdAmount').textContent = 'RM ' + amount;
    }

    // Display tenure
    if (fdData.tenure) {
        const monthText = parseInt(fdData.tenure) === 1 ? 'month' : 'months';
        document.getElementById('fdDuration').textContent = fdData.tenure + ' ' + monthText;
    }

    // Display interest rate
    if (fdData.interestRate) {
        document.getElementById('fdProfitRate').textContent = fdData.interestRate + '%';
    }

    // Display account number
    if (fdData.accountNumber) {
        document.getElementById('accountNumberDisplay').textContent = formatAccountNumber(fdData.accountNumber);
    }

    // Set bank address
    if (fdData.bankName) {
        // Fall back to the address saved in the Bank table
        const address = bankAddresses[fdData.bankName] || { line1: pageData.bankAddress, line2: '' };
        document.getElementById('bankAddress1').textContent = address.line1;
        document.getElementById('bankAddress2').textContent = address.line2;
    }
}

function formatAccountNumber(accountNumber) {
    const cleaned = accountNumber.replace(/\s/g, '');
    const parts = [];
    for (let i = 0; i < cleaned.length; i += 4) {
        parts.push(cleaned.substring(i, i + 4));
    }
    return parts.join(' ');
}

function setDocumentDate() {
    const today = new Date();
    const day = String(today.getDate()).padStart(2, '0');
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const year = today.getFullYear();
    document.getElementById('docDate').textContent = day + '/' + month + '/' + year;
}

function showConfirmModal() {
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
}

function submitToDatabase() {
    closeConfirmModal();

    // Check if any reminder is turned on
    const reminderMaturity = pageData.reminderMaturity;
    const reminderIncomplete = pageData.reminderIncomplete;
    const hasReminder = reminderMaturity === 'Y' || reminderIncomplete === 'Y';

    if (hasReminder) {
        // Show loader — email sending will cause a delay
        document.getElementById('loaderOverlay').classList.add('show');
    }

    // Submit the form, which saves to the database
    document.getElementById('submitForm').submit();
}

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { showConfirmModal, closeConfirmModal, submitToDatabase });
