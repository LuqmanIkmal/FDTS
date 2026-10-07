<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Fixed Deposit Tracking System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/report.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/report.js',
    ])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Generate Report'])

        <div class="page-content" id="filterView">
            <div class="filter-container">
                <div class="filter-header">
                    <div class="filter-icon">
                        <img src="{{ asset('images/icons/filter.png') }}" alt="Filter Icon" onerror="this.style.display='none'; this.parentElement.innerHTML='🔻';">
                    </div>
                    <h2 class="filter-title">Filter</h2>
                </div>

                <form class="filter-form" onsubmit="filterReport(event)">
                    <div class="form-group">
                        <label>Deposit Amount (RM)</label>
                        <input type="number" id="amount" placeholder="Enter amount" min="0" step="0.01">
                    </div>

                    <div class="form-group">
                        <label>Month</label>
                        <select id="month">
                            <option value="">Select Month</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bank Name</label>
                        <select id="bank">
                            <option value="">Select Bank</option>
                            @forelse ($bankList as $bank)
                            <option value="{{ $bank->bank_name }}">{{ $bank->bank_name }}</option>
                            @empty
                            <option value="" disabled>No banks available</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="status">
                            <option value="">Select Status</option>
                            <option value="PENDING">Pending</option>
                            <option value="ONGOING">Ongoing</option>
                            <option value="MATURED">Matured</option>
                        </select>
                    </div>

                    <div class="validation-message" id="validationMessage">
                        Please fill in at least 1 field.
                    </div>

                    <div class="button-container">
                        <button type="submit" class="btn-filter">Filter Now</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="page-content" id="resultsView" style="display: none;">
        <a href="{{ route('fd.report') }}" class="back-button">
                    <span class="back-icon">←</span>
                    <span>Back</span>
                </a>
            <div class="results-header">
                <button class="btn-print" onclick="window.print()">Print Report</button>
            </div>

            <div class="filter-criteria-display" id="filterCriteriaDisplay">
                <div class="filter-criteria-title">Filter Criteria:</div>
                <div class="filter-criteria-items" id="filterCriteriaItems">
                    <!-- Filter items will be inserted here by JavaScript -->
                </div>
            </div>

            <div class="table-container">
                <table>
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
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <div class="print-report">
                <div class="print-header">
                    <img src="{{ asset('images/vv-logo.png') }}" alt="Logo" class="print-logo" onerror="this.style.display='none'">
                    <h1 class="print-title">FIXED DEPOSIT REPORT</h1>
                </div>

                <div class="print-filters" id="printFilters">
                    Filter Criteria: DP Amount : | Month : | Bank : | Status : 
                </div>

                <table class="print-table">
                    <thead>
                        <tr>
                            <th>FD ID</th>
                            <th>ACCOUNT NUMBER</th>
                            <th>DETAILS</th>
                        </tr>
                    </thead>
                    <tbody id="printTableBody"></tbody>
                </table>

                <div class="print-footer">
                    <p><strong id="printDate">Report Generated:</strong></p>
                    <p><strong>Prepared By:</strong> Nor Azlina (Senior Finance Manager)</p>
                    <p><strong>Organization:</strong> Infra Desa Johor</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Values for resources/js/pages/fd/report.js --}}
    <script>
        window.pageData = {
            reportData: {{ \Illuminate\Support\Js::from($reportData) }},
            fdViewUrl: '{{ route('fd.view') }}',
            fdUpdateUrl: '{{ route('fd.update') }}',
        };
    </script>
</body>
</html>