// Values passed in from the Blade view (its window.pageData block)
const pageData = window.pageData;

// Edit FD - open the update page
function editFD() {
    window.location.href = pageData.fdUpdateUrl;
}
// View Application Form
function viewApplicationForm() {
    window.location.href = pageData.applicationUrl;
}


// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand Fixed Deposits menu
    const fdDropdown = document.getElementById('fdDropdown');
    const fdNavItem = document.getElementById('fdNavItem');
    if (fdDropdown && fdNavItem) {
        fdDropdown.classList.add('show');
        fdNavItem.classList.add('open');
    }
});

// Called from on... attributes in the Blade view, so these must be on window
Object.assign(window, { editFD, viewApplicationForm });
