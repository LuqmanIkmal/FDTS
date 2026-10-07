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

// Sidebar open/close (the menu button in the header).
// Wide screens: the sidebar sits beside the page and the choice is remembered.
// Small screens (same 1024px limit as app.css): it slides over the page and starts closed.
const root = document.documentElement;
const smallScreen = window.matchMedia('(max-width: 1024px)');

window.toggleSidebar = function () {
    if (smallScreen.matches) {
        root.classList.toggle('sidebar-open');
        return;
    }

    const collapsed = root.classList.toggle('sidebar-collapsed');
    try {
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    } catch (e) {
        // storage blocked: the sidebar still works, the choice is just not remembered
    }
};

window.closeSidebar = function () {
    root.classList.remove('sidebar-open');
};

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        window.closeSidebar();
    }
});

// Leaving the small-screen layout (rotating a tablet, resizing the window) closes the drawer
smallScreen.addEventListener('change', window.closeSidebar);
