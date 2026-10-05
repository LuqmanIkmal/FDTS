<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fixed Deposit Application Form</title>
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
            font-size: 14px;
            line-height: 1.5;
            background: #f5f5f5;
        }
        
        .document-container {
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }

        .screen-wrapper {
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            color: #2c3e50;
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
            display: flex;
            justify-content: center;
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

        /* Error Message */
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .document-wrapper {
            background: white;
            width: 100%;
            max-width: 21cm;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .document-container {
            padding: 2cm 1.5cm;
            border: 1px solid #e0e0e0;
        }

        .doc-section {
            margin-bottom: 1.5em;
        }

        .ref-line {
            margin-bottom: 0.3em;
        }

        .bold {
            font-weight: 700;
        }

        .red {
            color: #C00000;
            font-weight: 700;
        }

        .title-line {
            border-bottom: 1.5pt solid #000;
            padding-bottom: 3px;
            margin-top: 3px;
        }

        .info-table {
            width: 75%;
            border-collapse: collapse;
            border: 1.5pt solid #000;
            margin: 1em 0;
        }

        .info-table td {
            border: 1pt solid #000;
            padding: 0.4em 0.8em;
        }

        .info-table .label {
            width: 35%;
        }

        .info-table .colon {
            width: 5%;
            text-align: center;
        }

        .signature-area {
            margin-top: 3em;
        }

        .signature-lines {
            display: flex;
            justify-content: space-between;
            margin-top: 5em;
            gap: 8em;
        }

        .sig-line {
            border-top: 1.5pt solid #000;
            padding-top: 0.3em;
            min-width: 150px;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .btn {
            padding: 15px 50px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-print {
            background: #003f5c;
            color: white;
        }

        .btn-print:hover {
            background: #002d42;
        }

        .btn-submit {
            background: white;
            color: #2c3e50;
            border: 2px solid #2c3e50;
        }

        .btn-submit:hover {
            background: #2c3e50;
            color: white;
        }

        /* Confirmation Modal */
        /* Loading overlay */
        .loader-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        .loader-overlay.show {
            display: flex;
        }
        .loader-spinner {
            width: 52px;
            height: 52px;
            border: 5px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loader-text {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            text-align: center;
            line-height: 1.5;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 40px 50px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .modal-message {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .modal-btn {
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .modal-btn-no {
            background: #f0f0f0;
            color: #2c3e50;
            border: 2px solid #d0d0d0;
        }

        .modal-btn-no:hover {
            background: #e0e0e0;
        }

        .modal-btn-yes {
            background: #003f5c;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #002d42;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }

            body {
                background: white;
            }

            .screen-wrapper {
                display: block;
            }

            .sidebar,
            .header,
            .form-actions,
            .back-button,
            .alert-error {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
            }

            .document-wrapper {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }

            .document-container {
                border: none;
                padding: 0;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="screen-wrapper">
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.header', ['pageTitle' => 'Application Form'])
            <div class="page-content">
                <a href="{{ $backUrl }}" class="back-button">
                    <span class="back-icon">←</span>
                    <span>Back</span>
                </a>

                <div class="document-wrapper">
                    @if (session('error'))
                        <div class="alert-error">{{ session('error') }}</div>
                    @endif
                    
                    <div class="document-container" id="printable-content">
                        <div class="ref-line">Our Ref.<span style="margin-left: 2em;">:</span><span style="margin-left: 1.5em;">IDJSB/HO/FCA/L-FD/2025</span></div>
                        <div class="ref-line">Date<span style="margin-left: 3.2em;">:</span><span style="margin-left: 1.5em;" id="docDate"></span></div>

                        <div class="doc-section">
                            <div class="bold">The Manager</div>
                            <div class="bold">{{ $app['bankName'] }}</div>
                            <div id="bankAddress1"></div>
                            <div id="bankAddress2"></div>
                        </div>

                        <div class="doc-section">Dear Sir/Madam,</div>

                        <div class="doc-section">
                            <div class="bold">FIXED DEPOSIT (ISLAMIC) PLACEMENT</div>
                            <div class="title-line"></div>
                        </div>

                        <div class="doc-section">The above matters refers.</div>

                        <div class="doc-section">Kindly arrange for placement of Fixed Deposit as per details given below:</div>

                        <table class="info-table">
                            <tr>
                                <td class="label">Amount</td>
                                <td class="colon">:</td>
                                <td id="fdAmount"></td>
                            </tr>
                            <tr>
                                <td class="label">Duration</td>
                                <td class="colon">:</td>
                                <td id="fdDuration"></td>
                            </tr>
                            <tr>
                                <td class="label">Profit Rate</td>
                                <td class="colon">:</td>
                                <td id="fdProfitRate"></td>
                            </tr>
                        </table>

                        <div class="doc-section">Please debit our accounts as per mention below:</div>

                        <table class="info-table">
                            <tr>
                                <td class="label">Name</td>
                                <td class="colon">:</td>
                                <td>INFRA DESA (JOHOR) SDN BHD</td>
                            </tr>
                            <tr>
                                <td class="label">Bank</td>
                                <td class="colon">:</td>
                                <td>{{ $app['bankName'] }}</td>
                            </tr>
                            <tr>
                                <td class="label">Account Number</td>
                                <td class="colon">:</td>
                                <td id="accountNumberDisplay"></td>
                            </tr>
                        </table>

                        <div class="doc-section">Your cooperation on this matter is highly appreciated.</div>

                        <div class="doc-section">Thank you.</div>

                        <div class="signature-area">
                            <div>Yours faithfully,</div>
                            <div class="bold">INFRA DESA (JOHOR) SDN. BHD.</div>
                        </div>

                        <div class="signature-lines">
                            <div class="sig-line red">Authorised Signatory</div>
                            <div class="sig-line red">Authorised Signatory</div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-print" onclick="window.print()">Print</button>
                        @if (! $isViewMode)
                        <button class="btn btn-submit" onclick="showConfirmModal()">Submit</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if (! $isViewMode)
    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-content">
            <div class="modal-message">Are you sure you want to submit this Fixed Deposit?</div>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-no" onclick="closeConfirmModal()">No</button>
                <button class="modal-btn modal-btn-yes" onclick="submitToDatabase()">Yes</button>
            </div>
        </div>
    </div>

    <!-- Hidden form that saves the FD to the database -->
    <form id="submitForm" action="{{ route('fd.submit') }}" method="POST" style="display: none;">
        @csrf
	</form>
    @endif

    <script>
        // FD data (draft from session, or the saved FD in view mode)
        const fdData = {{ \Illuminate\Support\Js::from([
            'accountNumber' => $app['accountNumber'],
            'bankName' => $app['bankName'],
            'depositAmount' => $app['depositAmount'],
            'interestRate' => $app['interestRate'],
            'tenure' => $app['tenure'],
        ]) }};

        // Bank addresses
        const bankAddresses = {
            'Maybank': { line1: 'Cawangan Jalan Gombak,', line2: 'Selangor.' },
            'CIMB': { line1: 'Menara CIMB, Jalan Stesen Sentral 2,', line2: 'Kuala Lumpur Sentral, 50470 Kuala Lumpur.' },
            'MBSB': { line1: 'Menara MBSB, Jalan Kia Peng,', line2: '50450 Kuala Lumpur.' },
            'Public Bank': { line1: 'Menara Public Bank, Jalan Raja Chulan,', line2: '50200 Kuala Lumpur.' },
            'RHB Bank': { line1: 'RHB Centre, Jalan Tun Razak,', line2: '50400 Kuala Lumpur.' },
            'Hong Leong Bank': { line1: 'Menara Hong Leong, Damansara City,', line2: '50490 Kuala Lumpur.' },
            'AmBank': { line1: 'Menara AmBank, Jalan Yap Kwan Seng,', line2: '50450 Kuala Lumpur.' }
        };

        // Initialize page
        window.addEventListener('DOMContentLoaded', function() {
            populateForm();
            setDocumentDate();
        });

        function populateForm() {
            // Format deposit amount
            if (fdData.depositAmount) {
                const amount = parseFloat(fdData.depositAmount).toLocaleString('en-MY', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                document.getElementById('fdAmount').textContent = 'RM ' + amount;
            }
            
            // Display tenure
            if (fdData.tenure) {
                const monthText = parseInt(fdData.tenure) === 1 ? 'month' : 'months';
                document.getElementById('fdDuration').textContent = fdData.tenure + ' ' + monthText;
            }
            
            // Display interest rate
            if (fdData.interestRate) {
                document.getElementById('fdProfitRate').textContent = fdData.interestRate + '%';
            }
            
            // Display account number
            if (fdData.accountNumber) {
                document.getElementById('accountNumberDisplay').textContent = formatAccountNumber(fdData.accountNumber);
            }
            
            // Set bank address
            if (fdData.bankName) {
                // Fall back to the address saved in the Bank table
                const address = bankAddresses[fdData.bankName] || { line1: @json($bankAddress ?? ''), line2: '' };
                document.getElementById('bankAddress1').textContent = address.line1;
                document.getElementById('bankAddress2').textContent = address.line2;
            }
        }

        function formatAccountNumber(accountNumber) {
            const cleaned = accountNumber.replace(/\s/g, '');
            const parts = [];
            for (let i = 0; i < cleaned.length; i += 4) {
                parts.push(cleaned.substring(i, i + 4));
            }
            return parts.join(' ');
        }

        function setDocumentDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            document.getElementById('docDate').textContent = day + '/' + month + '/' + year;
        }

        function showConfirmModal() {
            document.getElementById('confirmModal').classList.add('show');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('show');
        }

        function submitToDatabase() {
            closeConfirmModal();

            // Check if any reminder is turned on
            const reminderMaturity = @json($app['reminderMaturity'] ?? '');
            const reminderIncomplete = @json($app['reminderIncomplete'] ?? '');
            const hasReminder = reminderMaturity === 'Y' || reminderIncomplete === 'Y';

            if (hasReminder) {
                // Show loader — email sending will cause a delay
                document.getElementById('loaderOverlay').classList.add('show');
            }

            // Submit the form, which saves to the database
            document.getElementById('submitForm').submit();
        }
    </script>

    <!-- Loading overlay (shown when reminders cause email delay) -->
    <div class="loader-overlay" id="loaderOverlay">
        <div class="loader-spinner"></div>
        <div class="loader-text">
            Creating your Fixed Deposit...<br>
            <span style="font-size:13px; opacity:0.85;">Sending reminder email, please wait.</span>
        </div>
    </div>

</body>
</html>
