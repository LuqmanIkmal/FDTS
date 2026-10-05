<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Lists - Fixed Deposit Tracking System</title>
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
            cursor: pointer;
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

        .table-container {
            background: white;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: white;
        }

        th {
            padding: 25px 20px;
            text-align: center;
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 25px 20px;
            text-align: center;
            color: #2c3e50;
            font-size: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

         .action-btn {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            opacity: 0.7;
        }

        .action-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .action-icon img {
		    width: 100%;
		    height: 100%;
		    object-fit: contain;
		    display: block; /* removes default inline gap */
		}
		
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            font-size: 16px;
        }

        .success-message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: #80cbc4;
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            opacity: 0;
            display: block;
        }
        
        .success-message.show {
            animation: slideDown 0.4s ease forwards;
        }
        
        .success-message.hide {
            animation: slideUpFade 0.4s ease forwards;
        }
        
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideUpFade {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            to {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Bank List'])

        <div class="page-content">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bank ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bankTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage"></div>

    <script>
        // Load banks from database
        const banks = @json($banks);
        
        console.log("========================================");
        console.log("🏦 Loaded " + banks.length + " banks from database");
        if (banks.length > 0) {
            console.log("✅ First bank:", banks[0]);
        }
        console.log("========================================");

        document.addEventListener('DOMContentLoaded', function() {
            const bankDropdown = document.getElementById('bankDropdown');
            const bankNavItem = document.getElementById('bankNavItem');
            
            if (bankDropdown && bankNavItem) {
                bankDropdown.classList.add('show');
                bankNavItem.classList.add('open');
            }

            loadBanks();

            // Check for success messages
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('msg') === 'created') {
                showSuccessMessage('Bank has been registered!');
                window.history.replaceState({}, document.title, '{{ route('banks.list') }}');
            } else if (urlParams.get('msg') === 'updated') {
                const bankId = urlParams.get('bankId'); showSuccessMessage((bankId ? bankId : 'Bank') + ' has been updated!');
                window.history.replaceState({}, document.title, '{{ route('banks.list') }}');
            }
        });

        function loadBanks() {
            const tbody = document.getElementById('bankTableBody');
            tbody.innerHTML = '';

            if (banks.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="no-data">No banks found. Add a new bank to get started.</td></tr>';
                return;
            }

            banks.forEach(bank => {
                const row = document.createElement('tr');
                row.innerHTML = 
                    '<td>' + bank.id + '</td>' +
                    '<td>' + escapeHtml(bank.name) + '</td>' +
                    '<td>' + escapeHtml(bank.phone) + '</td>' +
                    '<td>' + escapeHtml(bank.address) + '</td>' +
                    '<td>' +
                    '<div class="action-buttons">' +
                    '<div class="action-btn" onclick="updateBank(\'' + bank.id + '\')">' +
                        '<div class="action-icon update">' +
                            '<img src="{{ asset('images/icons/update-icon.png') }}" alt="Update" style="width:100px; height:90px;">' +
                        '</div>' +
                        '<div class="action-label">Update</div>' +
                    '</div>' +
            '</div>' +
                    '</td>';
                tbody.appendChild(row);
            });
            
            console.log("✅ Table populated with " + banks.length + " banks");
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : text;
            return div.innerHTML;
        }

        function updateBank(bankId) {
            // Redirect to the edit page with bank ID
            window.location.href = '{{ route('banks.edit') }}?id=' + bankId;
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
    </script>
</body>
</html>
