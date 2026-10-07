// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Load banks from database
const banks = pageData.banks;

console.log("========================================");
console.log("🏦 Loaded " + banks.length + " banks from database");
if (banks.length > 0) {
    console.log("✅ First bank:", banks[0]);
}
console.log("========================================");

document.addEventListener('DOMContentLoaded', function() {
    const bankDropdown = document.getElementById('bankDropdown');
    const bankNavItem = document.getElementById('bankNavItem');

    if (bankDropdown && bankNavItem) {
        bankDropdown.classList.add('show');
        bankNavItem.classList.add('open');
    }

    loadBanks();

    // Check for success messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'created') {
        showSuccessMessage('Bank has been registered!');
        window.history.replaceState({}, document.title, pageData.bankListUrl);
    } else if (urlParams.get('msg') === 'updated') {
        const bankId = urlParams.get('bankId'); showSuccessMessage((bankId ? bankId : 'Bank') + ' has been updated!');
        window.history.replaceState({}, document.title, pageData.bankListUrl);
    }
});

function loadBanks() {
    const tbody = document.getElementById('bankTableBody');
    tbody.innerHTML = '';

    if (banks.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="no-data">No banks found. Add a new bank to get started.</td></tr>';
        return;
    }

    banks.forEach(bank => {
        const row = document.createElement('tr');
        row.innerHTML =
            '<td>' + bank.id + '</td>' +
            '<td>' + escapeHtml(bank.name) + '</td>' +
            '<td>' + escapeHtml(bank.phone) + '</td>' +
            '<td>' + escapeHtml(bank.address) + '</td>' +
            '<td>' +
            '<div class="action-buttons">' +
            '<div class="action-btn" onclick="updateBank(\'' + bank.id + '\')">' +
                '<div class="action-icon update">' +
                    '<img src="' + pageData.updateIcon + '" alt="Update" style="width:100px; height:90px;">' +
                '</div>' +
                '<div class="action-label">Update</div>' +
            '</div>' +
    '</div>' +
            '</td>';
        tbody.appendChild(row);
    });

    console.log("✅ Table populated with " + banks.length + " banks");
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text == null ? '' : text;
    return div.innerHTML;
}

function updateBank(bankId) {
    // Redirect to the edit page with bank ID
    window.location.href = pageData.bankEditUrl + '?id=' + bankId;
}

function showSuccessMessage(message) {
    const successMsg = document.getElementById('successMessage');
    successMsg.textContent = message;
    successMsg.classList.add('show');
    successMsg.classList.remove('hide');

    setTimeout(function() {
        successMsg.classList.remove('show');
        successMsg.classList.add('hide');

        setTimeout(function() {
            successMsg.classList.remove('hide');
        }, 400);
    }, 3000);
}

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { updateBank });
