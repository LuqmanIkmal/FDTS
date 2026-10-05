// Sidebar dropdown menus. The sidebar calls this from onclick attributes,
// so it has to be on window: code in a Vite module is not global by default.
window.toggleDropdown = function (dropdownId, element) {
    const dropdown = document.getElementById(dropdownId);
    const isOpen = dropdown.classList.contains('show');

    // Toggle current dropdown
    if (isOpen) {
        dropdown.classList.remove('show');
        element.classList.remove('open');
    } else {
        dropdown.classList.add('show');
        element.classList.add('open');
    }
};
