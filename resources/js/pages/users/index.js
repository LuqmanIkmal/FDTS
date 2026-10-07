// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Load users from database
const users = pageData.users;

console.log("========================================");
console.log("📊 Loaded " + users.length + " users from database");
if (users.length > 0) {
    console.log("✅ First user:", users[0]);
} else {
    console.error("❌ No users loaded!");
}
console.log("========================================");

document.addEventListener('DOMContentLoaded', function() {
    loadUsers(users);
});

function loadUsers(userList) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';

    if (userList.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="no-data">No users found.</td></tr>';
        return;
    }

    userList.forEach(user => {
        const row = document.createElement('tr');
        const statusClass = user.status.toLowerCase();

        row.innerHTML =
            '<td>' + escapeHtml(user.staffId) + '</td>' +
            '<td>' + escapeHtml(user.name) + '</td>' +
            '<td>' + escapeHtml(user.role) + '</td>' +
            '<td><span class="status ' + escapeHtml(statusClass) + '">' + escapeHtml(user.status) + '</span></td>' +
            '<td>' + escapeHtml(user.manageBy) + '</td>' +
            '<td>' +
                '<div class="action-buttons">' +
                '<div class="action-btn" onclick="updateUser(\'' + user.staffId + '\')">' +
                    '<div class="action-icon update">' +
                        '<img src="' + pageData.updateIcon + '" alt="Update" style="width:100px; height:90px;">' +
                    '</div>' +
                    '<div class="action-label">Update</div>' +
                '</div>' +
        '</div>' +
            '</td>';
        tbody.appendChild(row);
    });

    console.log("✅ Table populated with " + userList.length + " rows");
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text == null ? '' : text;
    return div.innerHTML;
}

function searchUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filtered = users.filter(user => {
        return user.staffId.toString().toLowerCase().includes(searchTerm) ||
               user.name.toLowerCase().includes(searchTerm) ||
               user.role.toLowerCase().includes(searchTerm) ||
               user.status.toLowerCase().includes(searchTerm) ||
               user.manageBy.toLowerCase().includes(searchTerm);
    });
    loadUsers(filtered);
}

function filterUsers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();

    const filtered = users.filter(user => {
        // Search filter
        const matchesSearch = user.staffId.toString().toLowerCase().includes(searchTerm) ||
                             user.name.toLowerCase().includes(searchTerm) ||
                             user.role.toLowerCase().includes(searchTerm) ||
                             user.status.toLowerCase().includes(searchTerm) ||
                             user.manageBy.toLowerCase().includes(searchTerm);

        // Status filter
        const matchesStatus = statusFilter === 'all' || user.status.toLowerCase() === statusFilter;

        return matchesSearch && matchesStatus;
    });

    loadUsers(filtered);

    console.log("🔍 Filter applied - Search: '" + searchTerm + "', Status: '" + statusFilter + "', Results: " + filtered.length);
}

function updateUser(staffId) {
    const user = users.find(u => u.staffId == staffId);
    if (!user) return;

    console.log("Opening modal for user:", user.name);

    // Store current user
    window.currentUpdateUser = user;

    // Populate modal fields
    document.getElementById('userName').value = user.name;
    document.getElementById('userStaffId').value = user.staffId;
    document.getElementById("hiddenStaffId").value = user.numericId; // Send numeric ID to servlet
    document.getElementById('userRole').value = user.role;
    document.getElementById('userEmail').value = user.email || '';
    document.getElementById('userPhone').value = user.phone || '';
    document.getElementById('userStatus').value = user.status;
    document.getElementById('userManageBy').value = user.manageBy;
    document.getElementById('userAddress').value = user.address || '';

    // Set reason if exists
    if (user.reason) {
        document.getElementById('fileName').textContent = user.reason;
        document.getElementById('reasonText').value = user.reason;
    } else {
        document.getElementById('fileName').textContent = '';
        document.getElementById('reasonText').value = '';
    }

    // Clear file input
    document.getElementById('reasonFile').value = '';

    // Toggle reason field based on status
    toggleReasonField();

    // Show modal
    document.getElementById('userDetailsModal').classList.add('active');
}

function closeUserDetailsModal() {
    document.getElementById('userDetailsModal').classList.remove('active');
    window.currentUpdateUser = null;
}

function toggleReasonField() {
    const status = document.getElementById('userStatus').value;
    const fileInput = document.getElementById('reasonFile');
    const fileUploadBtn = document.getElementById('fileUploadBtn');

    if (status === 'Inactive') {
        fileInput.disabled = false;
        fileUploadBtn.classList.remove('disabled');
        fileUploadBtn.style.pointerEvents = 'auto';
    } else {
        fileInput.disabled = true;
        fileInput.value = '';
        document.getElementById('fileName').textContent = '';
        fileUploadBtn.classList.add('disabled');
        fileUploadBtn.style.pointerEvents = 'none';
    }
}

function displayFileName() {
    const fileInput = document.getElementById('reasonFile');
    const fileName = document.getElementById('fileName');

    if (fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
        document.getElementById('reasonText').value = fileInput.files[0].name;
    } else {
        fileName.textContent = '';
        document.getElementById('reasonText').value = '';
    }
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

// Check for success parameter in URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'true') {
    const numericStaffId = urlParams.get('staffId'); const matchedUser = users ? users.find(u => String(u.numericId) === String(numericStaffId)) : null; const displayId = matchedUser ? matchedUser.staffId : (numericStaffId || 'Staff'); showSuccessMessage(displayId + ' has been updated!');
}

function showConfirmation() {
    const staffId = document.getElementById('hiddenStaffId').value;
    const status = document.getElementById('userStatus').value;

    // Clear previous errors
    clearUserError();

    if (!staffId) {
        showUserError('Error: Staff ID is missing. Please select a user first.');
        return;
    }

    if (status === 'Inactive') {
        const reasonFile = document.getElementById('reasonFile').files[0];
        const reasonText = document.getElementById('reasonText').value;

        if (!reasonFile && !reasonText) {
            document.getElementById('reasonError').style.display = 'block';
            document.getElementById('fileUploadBtn').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
    }

    document.getElementById('confirmationModal').classList.add('active');
}

function showUserError(message) {
    let errEl = document.getElementById('userFormError');
    if (!errEl) {
        errEl = document.createElement('div');
        errEl.id = 'userFormError';
        errEl.style.cssText = 'color:#a51a28; background:#fdecea; padding:10px 15px; border-radius:8px; font-size:14px; margin-bottom:10px;';
        const form = document.getElementById('userDetailsForm');
        if (form) form.insertBefore(errEl, form.firstChild);
    }
    errEl.textContent = message;
    errEl.style.display = 'block';
}

function clearUserError() {
    const errEl = document.getElementById('userFormError');
    if (errEl) errEl.style.display = 'none';
    const reasonErr = document.getElementById('reasonError');
    if (reasonErr) reasonErr.style.display = 'none';
}

function closeConfirmation() {
    document.getElementById('confirmationModal').classList.remove('active');
}

function confirmUpdate() {
    closeConfirmation();
    document.getElementById('userDetailsForm').submit();
}

document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmation();
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, {
    filterUsers,
    closeUserDetailsModal,
    toggleReasonField,
    displayFileName,
    showConfirmation,
    closeConfirmation,
    confirmUpdate,
    updateUser,
});
