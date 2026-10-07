@php
    // Header profile: current user name, role and picture
    $headerUser = auth()->user();
    $pageTitle = $pageTitle ?? 'Fixed Deposit Tracking System';
@endphp

<!-- Header Section -->
<div class="header">
    <div class="header-title">
        <button type="button" class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Open or close the menu" aria-controls="sidebar">
            <span></span><span></span><span></span>
        </button>
        <h1>{{ $pageTitle }}</h1>
    </div>
    <div class="user-profile">
        <div class="user-info">
            <div class="user-name">{{ $headerUser?->staff_name ?? 'Guest' }}</div>
            <div class="user-role">{{ $headerUser?->staff_role ?? '' }}</div>
        </div>
        <div class="user-avatar" onclick="window.location.href='{{ route('profile') }}'" style="cursor: pointer;">
            @if ($headerUser && $headerUser->staff_picture)
                <img src="{{ route('profile.picture', ['staffId' => $headerUser->staff_id]) }}" alt="User Avatar">
            @else
                <img src="{{ asset('images/icons/user.jpg') }}" alt="User Avatar">
            @endif
        </div>
    </div>
</div>
