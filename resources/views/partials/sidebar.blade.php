@php
    // Role-based menu display
    $isSeniorFinanceManager = auth()->user()?->isSeniorFinanceManager() ?? false;
    $fdMenuOpen = request()->routeIs("fd.list", "fd.create", "fd.report");
    $bankMenuOpen = request()->routeIs("banks.list", "banks.create");
@endphp
<!-- Sidebar Component -->
<script>
    // Keep the sidebar closed from the first paint if it was closed on the last page (wide screens)
    try {
        if (localStorage.getItem('sidebarCollapsed') === '1') document.documentElement.classList.add('sidebar-collapsed');
    } catch (e) {}
</script>

<div class="sidebar-backdrop" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
    <button type="button" class="sidebar-close" onclick="closeSidebar()" aria-label="Close the menu">&times;</button>
    <div class="logo-section">
        <div class="logo-icon">
            <img src="{{ asset('images/LogoWhite.png') }}" alt="Logo">
        </div>
    </div>
    
    <div class="system-text">
        <p>Fixed Deposits<br>Tracking System</p>
    </div>

    <nav class="nav-menu">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        	<img src="{{ asset('images/icons/dashboard-icon.png') }}" alt="Home" class="dashboard-icon">
            <span>Dashboard</span>
        </a>

        <div class="nav-item {{ $fdMenuOpen ? 'open' : '' }}" id="fdNavItem" onclick="toggleDropdown('fdDropdown', this)">
            <img src="{{ asset('images/icons/fd-icon.png') }}" alt="Home" class="fd-icon">
            <span>Fixed Deposits</span>
            <span class="dropdown-icon">▼</span>
        </div>
        <div class="sub-menu {{ $fdMenuOpen ? 'show' : '' }}" id="fdDropdown">
            <a href="{{ route('fd.list') }}" class="sub-item {{ request()->routeIs('fd.list') ? 'active' : '' }}">Fixed Deposit List</a>
            <a href="{{ route('fd.create') }}" class="sub-item {{ request()->routeIs('fd.create') ? 'active' : '' }}">Create New FD</a>
            <a href="{{ route('fd.report') }}" class="sub-item {{ request()->routeIs('fd.report') ? 'active' : '' }}">Generate Report</a>
        </div>

        @if ($isSeniorFinanceManager)
        <!-- Bank Menu - Only visible to Senior Finance Manager -->
        <div class="nav-item {{ $bankMenuOpen ? 'open' : '' }}" id="bankNavItem" onclick="toggleDropdown('bankDropdown', this)">
            <img src="{{ asset('images/icons/bank-icon.png') }}" alt="Home" class="bank-icon">
            <span>Bank</span>
            <span class="dropdown-icon">▼</span>
        </div>
        <div class="sub-menu {{ $bankMenuOpen ? 'show' : '' }}" id="bankDropdown">
            <a href="{{ route('banks.list') }}" class="sub-item {{ request()->routeIs('banks.list') ? 'active' : '' }}">Bank List</a>
            <a href="{{ route('banks.create') }}" class="sub-item {{ request()->routeIs('banks.create') ? 'active' : '' }}">Register Bank</a>
        </div>

        <!-- Users Menu - Only visible to Senior Finance Manager -->
        <a href="{{ route('users.list') }}" class="nav-item {{ request()->routeIs('users.list') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/user-icon.png') }}" alt="Home" class="user-icon">
            <span>User</span>
        </a>
        @endif

        <div class="divider"></div>

        <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/profile-icon.png') }}" alt="Home" class="profile-icon">
            <span>Profile</span>
        </a>

        <a href="{{ route('logout') }}" class="nav-item">
            <img src="{{ asset('images/icons/logout-icon.png') }}" alt="Home" class="logout-icon">
            <span>Log Out</span>
        </a>
    </nav>
</div>
