// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Real FD data from database
const data = pageData.reportData;

let currentFilters = {};

function filterReport(e) {
    e.preventDefault();

    const amount = document.getElementById('amount').value;
    const month = document.getElementById('month').value;
    const bank = document.getElementById('bank').value;
    const status = document.getElementById('status').value;
    const validationMessage = document.getElementById('validationMessage');

    if (!amount && !month && !bank && !status) {
        validationMessage.classList.add('show');
        return;
    }

    // Hide validation message if it was showing
    validationMessage.classList.remove('show');

    // Store current filters
    currentFilters = { amount, month, bank, status };

    // Debug logging
    console.log('Applied Filters:', currentFilters);
    console.log('Total records:', data.length);

    let results = data.filter(item => {
        if (amount && item.amount != parseFloat(amount)) return false;
        if (month && item.month != month) return false;
        if (bank && item.bank != bank) return false;
        if (status && item.status != status) return false;
        return true;
    });

    console.log('Filtered results:', results.length);
    if (results.length === 0) {
        console.log('Sample data status values:', data.slice(0, 3).map(d => d.status));
    }

    showResults(results);
}

function showResults(results) {
    const tbody = document.getElementById('tableBody');
    const printBody = document.getElementById('printTableBody');

    tbody.innerHTML = '';
    printBody.innerHTML = '';

    // Populate screen table
    if (results.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="no-results">No matching data found.</td></tr>';
        printBody.innerHTML = '<tr><td colspan="3">No matching data found.</td></tr>';
    } else {
        results.forEach(item => {
            // Screen table row
            const row = document.createElement('tr');
            row.innerHTML =
                '<td>' + item.id + '</td>' +
                '<td><a href="' + pageData.fdViewUrl + '?id=' + encodeURIComponent(item.id) + '" class="account-link">' + escapeHtml(item.accountNo) + '</a></td>' +
                '<td>' + item.amount.toLocaleString() + '</td>' +
                '<td>' + escapeHtml(item.bank) + '</td>' +
                '<td>' + item.tenure + '</td>' +
                '<td><span class="status ' + item.status.toLowerCase() + '">' + item.status + '</span></td>' +
                '<td>' +
                    '<div class="action-buttons">' +
                        '<div class="action-btn" onclick="updateFD(\'' + item.id + '\')">' +
                            '<div class="action-icon update">✏️</div>' +
                            '<div class="action-label">Update</div>' +
                        '</div>' +
                    '</div>' +
                '</td>';
            tbody.appendChild(row);

            // Print table row
            const printRow = document.createElement('tr');
            const details = 'Deposit: RM' + item.amount.toLocaleString() + '\n' +
                          'Bank: ' + item.bank + '\n' +
                          'Tenure: ' + item.tenure + ' Months\n' +
                          'Status: ' + item.status;
            printRow.innerHTML =
                '<td>' + item.id + '</td>' +
                '<td>' + item.accountNo + '</td>' +
                '<td style="white-space: pre-line;">' + details + '</td>';
            printBody.appendChild(printRow);
        });
    }

    // Update print filters
    const monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];

    let filterParts = [];
    if (currentFilters.amount) filterParts.push('Amount: RM ' + parseFloat(currentFilters.amount).toLocaleString());
    if (currentFilters.month) filterParts.push('Month: ' + monthNames[parseInt(currentFilters.month)]);
    if (currentFilters.bank) filterParts.push('Bank: ' + currentFilters.bank);
    if (currentFilters.status) filterParts.push('Status: ' + currentFilters.status);

    const filterText = 'Filter Criteria: ' + (filterParts.length > 0 ? filterParts.join(' | ') : 'All Records');
    document.getElementById('printFilters').textContent = filterText;

    // Update filter criteria display above the table
    const filterCriteriaItems = document.getElementById('filterCriteriaItems');
    filterCriteriaItems.innerHTML = '';

    if (currentFilters.amount) {
        const item = document.createElement('div');
        item.className = 'filter-item';
        item.innerHTML = '<strong>Amount:</strong> RM ' + parseFloat(currentFilters.amount).toLocaleString();
        filterCriteriaItems.appendChild(item);
    }

    if (currentFilters.month) {
        const item = document.createElement('div');
        item.className = 'filter-item';
        item.innerHTML = '<strong>Month:</strong> ' + monthNames[parseInt(currentFilters.month)];
        filterCriteriaItems.appendChild(item);
    }

    if (currentFilters.bank) {
        const item = document.createElement('div');
        item.className = 'filter-item';
        item.innerHTML = '<strong>Bank:</strong> ' + currentFilters.bank;
        filterCriteriaItems.appendChild(item);
    }

    if (currentFilters.status) {
        const item = document.createElement('div');
        item.className = 'filter-item';
        item.innerHTML = '<strong>Status:</strong> ' + currentFilters.status;
        filterCriteriaItems.appendChild(item);
    }

    // Update print date
    const today = new Date();
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                  'July', 'August', 'September', 'October', 'November', 'December'];
    const dateStr = today.getDate() + ' ' + months[today.getMonth()] + ' ' + today.getFullYear();
    document.getElementById('printDate').innerHTML = '<strong>Report Generated:</strong> ' + dateStr;

    document.getElementById('filterView').style.display = 'none';
    document.getElementById('resultsView').style.display = 'block';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text == null ? '' : text;
    return div.innerHTML;
}

function updateFD(id) {
    window.location.href = pageData.fdUpdateUrl + '?id=' + encodeURIComponent(id);
}

// Auto-expand Fixed Deposits menu
document.addEventListener('DOMContentLoaded', function() {
    const fdDropdown = document.getElementById('fdDropdown');
    const fdNavItem = document.getElementById('fdNavItem');
    if (fdDropdown && fdNavItem) {
        fdDropdown.classList.add('show');
        fdNavItem.classList.add('open');
    }

    // Hide validation message when user interacts with any field
    const fields = ['amount', 'month', 'bank', 'status'];
    const validationMessage = document.getElementById('validationMessage');

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                validationMessage.classList.remove('show');
            });
            field.addEventListener('change', function() {
                validationMessage.classList.remove('show');
            });
        }
    });
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { filterReport, updateFD });
