<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .user-role {
            font-size: 13px;
            color: #7f8c8d;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #d0d0d0;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .page-content {
            padding: 40px;
            flex: 1;
        }
        
        .back-button {
            position: absolute;
            top: 120px;
            left: 290px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #7f8c8d;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            width: fit-content;
        }

        .back-button:hover {
            color: #2c3e50;
        }

        .back-icon {
            font-size: 24px;
        }
        

        .filter-container {
            background: white;
            border-radius: 20px;
            padding: 50px 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin: 40px auto;
        }

        .filter-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
            justify-content: center;
        }

        .filter-icon {
            width: 32px;
            height: 32px;
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .filter-title {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            padding: 12px 18px;
            border: 2px solid #d0d0d0;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #003f5c;
        }

        .button-container {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-filter {
            padding: 15px 60px;
            background: #003f5c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .btn-filter:hover {
            background: #002d42;
        }

        .validation-message {
            grid-column: 1 / -1;
            padding: 12px 18px;
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 8px;
            color: #c33;
            font-size: 14px;
            text-align: center;
            display: none;
            margin-top: 10px;
        }

        .validation-message.show {
            display: block;
        }

        .filter-criteria-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .filter-criteria-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .filter-criteria-items {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filter-item {
            background: white;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            font-size: 14px;
            color: #495057;
        }

        .filter-item strong {
            color: #003f5c;
            margin-right: 4px;
        }

        .results-header {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn-print {
            padding: 8px 20px;
            background: #003f5c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .btn-print:hover {
            background: #002d42;
        }

        /* Print Report Format */
        .print-report {
            display: none;
        }

        @media print {
            body {
                background: white;
            }

            .sidebar,
            .header,
            .results-header,
            .table-container {
                display: none !important;
            }

            .print-report {
                display: block !important;
                padding: 50px 60px;
                max-width: 210mm;
                margin: 0 auto;
            }

            .print-header {
                display: flex;
                align-items: center;
                gap: 20px;
                margin-bottom: 10px;
                padding-bottom: 15px;
                border-bottom: 3px solid #2c3e50;
            }

            .print-logo {
                width: 70px;
                height: 70px;
            }

            .print-title {
                font-size: 28px;
                font-weight: 700;
                color: #2c3e50;
                letter-spacing: 0.5px;
            }

            .print-filters {
                font-size: 13px;
                color: #2c3e50;
                margin: 25px 0;
                padding: 12px 15px;
                background: #f8f9fa;
                border-left: 4px solid #003f5c;
                font-weight: 500;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 25px;
                border: 2px solid #2c3e50;
            }

            .print-table th {
                background: #2c3e50;
                color: white;
                padding: 15px 12px;
                text-align: center;
                font-size: 13px;
                font-weight: 600;
                border: 1px solid #2c3e50;
                letter-spacing: 0.3px;
            }

            .print-table td {
                border: 1px solid #666;
                padding: 12px;
                font-size: 12px;
                color: #2c3e50;
                vertical-align: top;
            }

            .print-table td:first-child {
                text-align: center;
                font-weight: 600;
                width: 15%;
            }

            .print-table td:nth-child(2) {
                text-align: center;
                width: 25%;
                font-family: 'Courier New', monospace;
            }

            .print-table td:last-child {
                width: 60%;
                line-height: 1.6;
            }

            .print-table tr:nth-child(even) {
                background: #f9f9f9;
            }

            .print-footer {
                margin-top: 40px;
                padding-top: 20px;
                border-top: 2px solid #e0e0e0;
                font-size: 11px;
                color: #555;
                line-height: 1.8;
            }

            .print-footer p {
                margin: 3px 0;
            }
        }

        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 20px;
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 20px;
            text-align: center;
            color: #2c3e50;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .account-link {
            color: #3498db;
            text-decoration: underline;
            cursor: pointer;
        }

        .account-link:hover {
            color: #2980b9;
        }

        .status {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
        }

        .status.pending {
            background: #ffe5e5;
            color: #c0392b;
        }

        .status.ongoing {
            background: #fff9e5;
            color: #d68910;
        }

        .status.matured {
            background: #e5f5f0;
            color: #0c7a5a;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            opacity: 0.7;
        }

        .action-icon {
            font-size: 20px;
        }

        .action-icon.update {
            color: #3498db;
        }

        .action-label {
            font-size: 11px;
            color: #7f8c8d;
            font-weight: 500;
        }

        .no-results {
            text-align: center;
            padding: 60px;
            color: #7f8c8d;
        }

        @media print {
            .sidebar, .header, .results-header {
                display: none !important;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="print-logo" onerror="this.style.display='none'">
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

    <script>
        // Real FD data from database
        const data = {{ \Illuminate\Support\Js::from($reportData) }};

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
                        '<td><a href="{{ route('fd.view') }}?id=' + encodeURIComponent(item.id) + '" class="account-link">' + escapeHtml(item.accountNo) + '</a></td>' +
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
            window.location.href = '{{ route('fd.update') }}?id=' + encodeURIComponent(id);
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
    </script>
</body>
</html>