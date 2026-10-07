<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Deposits Lists - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/index.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/index.js',
    ])
</head>
<body>
    <!-- Include Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('partials.header', ['pageTitle' => 'Fixed Deposit List'])
        <!-- Page Content -->
        <div class="page-content">
            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search Bar -->
            <div class="search-section">
                <form id="searchForm" method="get" action="{{ route('fd.list') }}" style="display:contents;">
                    <div class="search-bar">
                        <input type="text" id="searchInput" name="search" placeholder="Search" value="{{ $search }}" onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('searchForm').submit();}">
                        <span class="search-icon" onclick="document.getElementById('searchForm').submit();" style="cursor:pointer;">🔍</span>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table id="fdTable">
                    <thead>
                        <tr>
                            <th>Fixed Deposit ID</th>
                            <th>Account No.</th>
                            <th>Deposits (RM)</th>
                            <th>Bank</th>
                            <th>Tenure (Months)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pageItems as $fd)
                        <tr>
                            <td>FD{{ $fd->fd_id }}</td>
                            <td>
                                <a href="{{ route('fd.view', ['id' => $fd->fd_id]) }}" class="account-link">
                                    {{ $fd->acc_number }}
                                </a>
                            </td>
                            <td>{{ number_format((float) $fd->deposit_amount, 2) }}</td>
                            <td>{{ $fd->bank_name ?? '-' }}</td>
                            <td>{{ $fd->tenure }}</td>
                            <td>
                                <span class="status {{ $fd->status_class }}">
                                    {{ $fd->display_status }}
                                </span>
                            </td>
                            <td>
                                <div class="action-btn" onclick="updateFD({{ $fd->fd_id }})">
								    <div class="action-icon update">
								        <img src="{{ asset('images/icons/update-icon.png') }}" alt="Update" style="width:100px; height:90px;">
								    </div>
								    	<div class="action-label">Update</div>
								</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    @if ($search !== '')
                                    <div class="empty-state-icon">🔍</div>
                                    <div class="empty-state-text">No matching data found.</div>
                                    @else
                                    <div class="empty-state-icon">📋</div>
                                    <div class="empty-state-text">No Fixed Deposits Found</div>
                                    <div class="empty-state-subtext">Create a new Fixed Deposit to get started</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- ========================================
                     PAGINATION CONTROLS
                     ======================================== -->
                @php
                    $pageUrl = fn ($page) => route('fd.list', array_filter(['page' => $page, 'search' => $search], fn ($v) => $v !== '' && $v !== null));
                @endphp

                @if ($totalItems > 0)
				<div class="pagination-container">
				    <div class="pagination-info">
				        Showing {{ $startIndex + 1 }} to {{ $endIndex }} of {{ $totalItems }} FDs
				    </div>

				    <div class="pagination">

				        <!-- Previous Button -->
				        <a href="{{ $pageUrl($currentPage - 1) }}"
				           class="pagination-btn prev {{ $currentPage <= 1 ? 'disabled' : '' }}">
				            Previous
				        </a>

				        <!-- Page Numbers -->
				        <div class="page-numbers">
				            @php
				                $maxVisible = 5;
				                $startPage = max(1, $currentPage - 2);
				                $endPage = min($totalPages, $startPage + $maxVisible - 1);

				                if ($endPage - $startPage < $maxVisible - 1) {
				                    $startPage = max(1, $endPage - $maxVisible + 1);
				                }
				            @endphp

				            {{-- First page --}}
				            @if ($startPage > 1)
				                <a href="{{ $pageUrl(1) }}"
				                   class="pagination-btn">1</a>

				                @if ($startPage > 2)
				                    <span style="padding: 8px;">...</span>
				                @endif
				            @endif

				            {{-- Page numbers --}}
				            @for ($i = $startPage; $i <= $endPage; $i++)
				                <a href="{{ $pageUrl($i) }}"
				                   class="pagination-btn {{ $i == $currentPage ? 'active' : '' }}">
				                    {{ $i }}
				                </a>
				            @endfor

				            {{-- Last page --}}
				            @if ($endPage < $totalPages)
				                @if ($endPage < $totalPages - 1)
				                    <span style="padding: 8px;">...</span>
				                @endif

				                <a href="{{ $pageUrl($totalPages) }}"
				                   class="pagination-btn">
				                    {{ $totalPages }}
				                </a>
				            @endif
				        </div>

				        <!-- Next Button -->
				        <a href="{{ $pageUrl($currentPage + 1) }}"
				           class="pagination-btn next {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
				            Next
				        </a>

				    </div>
				</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Values for resources/js/pages/fd/index.js --}}
    <script>
        window.pageData = {
            fdUpdateUrl: '{{ route('fd.update') }}',
            // Success messages (flashed by FixedDepositController)
            @if (session('fdSuccess'))
                fdSuccessMessage: @json('New Fixed Deposit ' . (session('newFdID') ? 'FD' . session('newFdID') : '') . ' added successfully!'),
            @endif
            @if (session('fdUpdateSuccess'))
                fdUpdateSuccessMessage: {{ \Illuminate\Support\Js::from('Fixed Deposit ' . session('updatedFdId', '') . ' has been updated!') }},
            @endif
        };
    </script>
</body>
</html>
