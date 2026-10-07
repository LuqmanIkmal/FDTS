// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Show notification function
function showNotification(message, type) {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;

    if (type === 'success') {
        notification.classList.add('success');
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

// Search is now handled server-side via form submit
function searchTable() { }

// Update FD function - loads data from database
function updateFD(fdId) {
    window.location.href = pageData.fdUpdateUrl + '?id=' + fdId;
}

// Auto-open Fixed Deposits dropdown
document.addEventListener('DOMContentLoaded', function() {
    const fdDropdown = document.getElementById('fdDropdown');
    const fdNavItem = document.getElementById('fdNavItem');

    if (fdDropdown && fdNavItem) {
        fdDropdown.classList.add('show');
        fdNavItem.classList.add('open');
    }

    // Success messages (flashed by FixedDepositController)
    if (pageData.fdSuccessMessage) {
        showNotification(pageData.fdSuccessMessage, 'success');
    }

    if (pageData.fdUpdateSuccessMessage) {
        showNotification(pageData.fdUpdateSuccessMessage, 'success');
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { updateFD });
