@php
    // Error message from a failed withdrawal check (flashed by the controller)
    $errorMessage = session('errorMessage');

    // Format dates for input fields
    $startDateStr = $fd->start_date ? $fd->start_date->format('Y-m-d') : '';
    $maturityDateStr = $fd->maturity_date ? $fd->maturity_date->format('Y-m-d') : '';

    // Reminder settings default to "off"
    $reminderMaturity = $reminderMaturity ?? 'off';
    $reminderIncomplete = $reminderIncomplete ?? 'off';

    // Convert fdType for display
    $fdTypeDisplay = $fd->fd_type === 'FREEFD' ? 'Free' : 'Pledge';

    // Convert status for display
    $statusDisplay = $fd->display_status;

    // Transaction section is only shown for matured FDs
    $isMatured = strcasecmp($statusDisplay, 'Matured') === 0;

    // ========== CONDITIONAL TRANSACTION LOGIC (for page load only) ==========
    $isFreeFD = $fd->fd_type === 'FREEFD';
    $isPledgeFD = $fd->fd_type === 'PLEDGEFD';
    $hasAutoRenewal = $autoRenewalStatus === 'Yes';

    // Pledge FD shows special messages only
    $showPledgeMessages = $isPledgeFD;

    // Free FD logic
    $showReinvestOption = $isFreeFD && ! $hasAutoRenewal;
    $showReinvestMessage = $isFreeFD && $hasAutoRenewal;

    // Show withdraw option only for Free FD with Full or Partial
    $showWithdrawOption = $isFreeFD && in_array($withdrawableStatus, ['Full', 'Partial'], true);
    $showWithdrawMessage = $isFreeFD && $withdrawableStatus === 'No';

    $remainingBalanceJs = $fd->remaining_balance !== null ? (string) $fd->remaining_balance : '0';
    $depositAmountJs = $fd->deposit_amount !== null ? (string) $fd->deposit_amount : '0';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Fixed Deposit - Fixed Deposit Tracking System</title>
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
            font-size: 1.8rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info { text-align: right; }
        .user-name { font-weight: 600; color: #2c3e50; font-size: 16px; }
        .user-role { font-size: 13px; color: #7f8c8d; }

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

        .back-button {
            position: absolute;
            top: 110px;
            left: 290px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #7f8c8d;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover { color: #2c3e50; }
        .back-icon { font-size: 24px; }

        .page-content {
            padding: 30px 40px;
            margin-top: 35px;
            flex: 1;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .section-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a4d5e;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1a4d5e;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .form-row:last-child { margin-bottom: 0; }

        .form-row label {
            font-size: 13px;
            font-weight: 500;
            color: #2c3e50;
            width: 260px;
            flex-shrink: 0;
        }

        .form-row input,
        .form-row select {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            background-color: white;
        }

        .form-row input:focus,
        .form-row select:focus {
            outline: none;
            border-color: #1a4d5e;
        }

        .form-row input[readonly] {
            background-color: #f0f0f0;
            color: #666;
        }

        .radio-group {
            display: flex;
            gap: 30px;
            flex: 1;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .radio-item input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #1a4d5e;
            cursor: pointer;
        }

        .radio-item label {
            width: auto;
            cursor: pointer;
            font-size: 13px;
        }

        .calculate-link {
            color: #0066cc;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            margin-left: 480px;
            margin-top: -10px;
            cursor: pointer;
        }

        .calculate-link:hover { text-decoration: underline; }

        .input-with-link {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .input-with-link input { flex: 1; }

        .file-upload-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .file-upload-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: white;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .file-upload-btn:hover { border-color: #1a4d5e; }
        .file-upload-btn input[type="file"] { display: none; }
        .file-icon { font-size: 18px; color: #7f8c8d; }
        .file-name { color: #2c3e50; font-size: 13px; }
        .file-info { font-size: 11px; color: #7f8c8d; margin-top: 4px; }

        .conditional-fields {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #d0d0d0;
        }

        /* Reinvest Fields */
        .reinvest-fields {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #d0d0d0;
        }

        /* Info Messages */
        .info-message {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 13px;
            color: #1565c0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-message.auto-renew {
            background: #e8f5e9;
            border-left-color: #4caf50;
            color: #2e7d32;
        }

        .info-message.no-withdraw {
            background: #fff3e0;
            border-left-color: #ff9800;
            color: #e65100;
        }

        .info-icon {
            font-size: 18px;
        }

        .reminder-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .reminder-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 500px;
        }

        .reminder-label { font-size: 13px; color: #2c3e50; }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 26px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider { background-color: #1a4d5e; }
        input:checked + .slider:before { transform: translateX(24px); }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 10px 35px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-update {
            background: #1a4d5e;
            color: white;
        }

        .btn-update:hover {
            background: #153d4a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 77, 94, 0.3);
        }

        .notification {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            font-weight: 500;
            z-index: 10000;
            color: white;
            min-width: 300px;
            text-align: center;
            margin-left: 125px;
            opacity: 0;
        }

        .notification.show { animation: slideDown 0.4s ease forwards; }
        .notification.hide { animation: slideUpFade 0.4s ease forwards; }

        @keyframes slideDown {
            from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }

        @keyframes slideUpFade {
            from { transform: translateX(-50%) translateY(0); opacity: 1; }
            to { transform: translateX(-50%) translateY(-20px); opacity: 0; }
        }

        .notification.success { background: #80cbc4; }
        .notification.error { background: #e74c3c; }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active { display: flex; }
        .modal-overlay.show { display: flex; }

        .modal-content {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            min-width: 400px;
            position: relative;
        }

        .modal-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .modal-message {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .modal-btn-no {
            background: #95a5a6;
            color: white;
        }

        .modal-btn-no:hover {
            background: #7f8c8d;
        }

        .modal-btn-yes {
            background: #1a4d5e;
            color: white;
        }

        .modal-btn-yes:hover {
            background: #153d4a;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            color: #7f8c8d;
            cursor: pointer;
            background: none;
            border: none;
        }

        .modal-close:hover { color: #2c3e50; }

        .modal-title {
            font-size: 1.5rem;
            color: #2c3e50;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        .calculation-result { margin-bottom: 15px; }

        .calculation-result label {
            display: block;
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .calculation-result input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 14px;
            background: #f8f9fa;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 1200px) {
            .section-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 200px; }
            .header { padding: 15px 20px; }
            .page-content { padding: 20px; }
            .form-row { flex-direction: column; align-items: flex-start; }
            .form-row label { width: 100%; margin-bottom: 5px; }
        }
        
        .error-message {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 4px;
            margin-left: 260px;
            display: block;
        }
        
        .field-error {
            border-color: #d32f2f !important;
            background-color: #ffebee !important;
        }
        
        .alert-error-inline {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-error-inline::before {
            content: "⚠️";
            font-size: 18px;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Update Fixed Deposit'])

        <div class="page-content">
            <a href="{{ route('fd.list') }}" class="back-button">
                <span class="back-icon">←</span>
                <span>Back</span>
            </a>

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <div class="form-container">
                @if ($errorMessage)
                    <div class="alert-error-inline">
                        {{ $errorMessage }}
                    </div>
                @endif
                <form id="updateFDForm" action="{{ route('fd.update.submit') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <!-- Row 1: Reference & Type -->
                    <div class="section-grid">
                        <div class="section-box">
                            <div class="section-title">Fixed Deposit Reference</div>
                            <div class="form-row">
                                <label>Fixed Deposit ID</label>
                                <input type="text" id="fdID" name="fdID" value="FD{{ $fd->fd_id }}" readonly>
                            </div>
                            <div class="form-row">
                                <label>Account Number</label>
                                <input type="text" id="accountNumber" name="accountNumber" value="{{ $fd->acc_number }}" required>
                            </div>
                            <div class="form-row">
                                <label>Referral Number</label>
                                <input type="text" id="referralNumber" name="referralNumber" value="{{ $fd->referral_number ?? '' }}">
                            </div>
                        </div>

                        <div class="section-box">
                            <div class="section-title">Fixed Deposit Type</div>
                            <div class="form-row">
                                <label>FD Type</label>
                                <div class="radio-group">
                                    <div class="radio-item">
                                        <input type="radio" id="typePledge" name="fdType" value="Pledge" {{ $fdTypeDisplay === 'Pledge' ? 'checked' : '' }} onchange="handleFDTypeChange()">
                                        <label for="typePledge">Pledge FD</label>
                                    </div>
                                    <div class="radio-item">
                                        <input type="radio" id="typeFree" name="fdType" value="Free" {{ $fdTypeDisplay === 'Free' ? 'checked' : '' }} onchange="handleFDTypeChange()">
                                        <label for="typeFree">Free FD</label>
                                    </div>
                                </div>
                            </div>

                            <div id="pledgeFields" class="conditional-fields" style="display: {{ $fdTypeDisplay === 'Pledge' ? 'block' : 'none' }};">
                                <div class="form-row">
                                    <label>Collateral Status</label>
                                    <select id="collateralStatus" name="collateralStatus">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ $collateralStatus === 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Partial" {{ $collateralStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>Pledge Value (RM)</label>
                                    <input type="number" id="pledgeValue" name="pledgeValue" step="0.01" value="{{ $pledgeValue !== null ? $pledgeValue : '' }}">
                                    <div id="pledgeValueHint" style="font-size:12px; color:#666; margin-top:4px; display:none;"></div>
                                </div>
                            </div>

                            <div id="freeFields" class="conditional-fields" style="display: {{ $fdTypeDisplay === 'Free' ? 'block' : 'none' }};">
                                <div class="form-row">
                                    <label>Auto Renewal Status</label>
                                    <select id="autoRenewalStatus" name="autoRenewalStatus" onchange="updateTransactionOptions()">
                                        <option value="">Select Status</option>
                                        <option value="Yes" {{ $autoRenewalStatus === 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $autoRenewalStatus === 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>Withdrawable Status</label>
                                    <select id="withdrawableStatus" name="withdrawableStatus" onchange="updateTransactionOptions()">
                                        <option value="">Select Status</option>
                                        <option value="Full" {{ $withdrawableStatus === 'Full' ? 'selected' : '' }}>Full</option>
                                        <option value="Partial" {{ $withdrawableStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="No" {{ $withdrawableStatus === 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Details -->
                    <div class="section-box full-width" style="margin-bottom: 25px;">
                        <div class="section-title">Fixed Deposit Details</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <div class="form-row">
                                    <label>Bank</label>
                                    <select id="bankName" name="bankName" required>
                                        <option value="">Select Bank</option>
                                        @forelse ($bankList as $bank)
                                        <option value="{{ $bank->bank_name }}" {{ $bank->bank_name === $fd->bank_name ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                        @empty
                                        <option value="" disabled>No banks available</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>Start Date</label>
                                    <input type="date" id="startDate" name="startDate" value="{{ $startDateStr }}" required onchange="calculateMaturityDate()">
                                </div>
                                <div class="form-row">
                                    <label>Maturity Date</label>
                                    <input type="date" id="maturityDate" name="maturityDate" value="{{ $maturityDateStr }}" required>
                                </div>
                                <div class="form-row">
                                    <label>Interest Rate (%)</label>
                                    <input type="number" id="interestRate" name="interestRate" step="0.01" value="{{ $fd->interest_rate !== null ? rtrim(rtrim((string) $fd->interest_rate, '0'), '.') : '' }}" required>
                                </div>
                                <div class="calculate-link" onclick="calculateInterest();">Calculate</div>
                            </div>
                            <div>
                                <div class="form-row">
                                    <label>Deposit Amount (RM)</label>
                                    <input type="number" id="depositAmount" name="depositAmount" step="0.01" value="{{ $fd->deposit_amount ?? '' }}" required>
                                </div>
                                <div class="form-row">
                                    <label>Tenure (Months)</label>
                                    <input type="number" id="tenure" name="tenure" min="1" value="{{ $fd->tenure }}" required onchange="calculateMaturityDate()">
                                </div>
                                <div class="form-row">
                                    <label>Status</label>
                                    <select id="fdStatus" name="fdStatus" required onchange="handleStatusChange()">
                                        <option value="">Select Status</option>
                                        <option value="Pending" {{ $statusDisplay === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Ongoing" {{ $statusDisplay === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="Matured" {{ $statusDisplay === 'Matured' ? 'selected' : '' }}>Matured</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>Created By</label>
                                    <input type="text" id="createdBy" name="createdBy" value="Admin" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Transaction & Certification -->
                    <div class="section-grid">
                        <!-- Fixed Deposit Transaction - Dynamic based on FD type and settings -->
                        <div class="section-box" id="transactionSection" style="display: {{ $isMatured ? 'block' : 'none' }};">
                            <div class="section-title">Fixed Deposit Transaction</div>
                            
                            <!-- Balance Information Display -->
                            <div style="background: #f0f8ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;">💰 Remaining Balance</div>
                                        <div style="font-size: 15px; font-weight: 600; color: #1a4d5e;">
                                            RM {{ number_format((float) ($fd->remaining_balance ?? 0), 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;">📤 Total Withdrawn</div>
                                        <div style="font-size: 15px; font-weight: 600; color: #1a4d5e;">
                                            RM {{ number_format((float) ($fd->total_withdrawn ?? 0), 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Container that will be dynamically updated -->
                            <div id="transactionOptionsContainer">
                            
                            	<!-- ==================== PLEDGE FD MESSAGES ==================== -->
                                <div id="pledgeMessagesContainer" style="display: {{ $showPledgeMessages ? 'block' : 'none' }};">
                                    <div class="info-message no-withdraw">
                                        <span class="info-icon">🚫</span>
                                        <span><strong>Withdraw is not allowed on FD with Pledge Type</strong><br>
                                        Pledge FDs cannot be withdrawn before maturity.</span>
                                    </div>
                                    
                                    <div class="info-message auto-renew" style="margin-top: 15px;">
                                        <span class="info-icon">🔄</span>
                                        <span><strong>FD with Pledge Type will auto Renew</strong><br>
                                        This FD will automatically renew when it matures.</span>
                                    </div>
                                </div>
                                <!-- ==================== END PLEDGE FD MESSAGES ==================== -->
                                    
                                <!-- AUTO-RENEWAL MESSAGE (Free FD with Auto Renewal = Yes) -->
                                <div id="autoRenewMessage" style="display: {{ $showReinvestMessage ? 'block' : 'none' }};">
                                    <div class="info-message auto-renew">
                                        <span class="info-icon">🔄</span>
                                        <span><strong>This FD Will Auto Renew</strong><br>
                                        System will automatically create a new FD when this matures.</span>
                                    </div>
                                </div>
                                
                                <!-- MANUAL REINVEST OPTION -->
                                <div id="reinvestOptionContainer" style="display: {{ $showReinvestOption ? 'block' : 'none' }};">
                                    <div class="form-row">
                                        <label>Transaction Type</label>
                                        <div class="radio-group">
                                            <div class="radio-item">
                                                <input type="radio" id="transReinvest" name="transactionType" value="Reinvest" onchange="handleTransactionTypeChange()">
                                                <label for="transReinvest">Reinvest</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Reinvest Fields -->
                                    <div id="reinvestFields" class="reinvest-fields" style="display: none;">
                                        <div class="form-row">
                                            <label>Transaction Date</label>
                                            <input type="date" id="reinvestTransactionDate" name="transactionDate[]" min="{{ $maturityDateStr }}" onchange="validateTransactionDate(this)">
                                        </div>
                                        <div class="form-row">
                                            <label>New Start Date</label>
                                            <input type="date" id="newStartDate" name="newStartDate" min="{{ $maturityDateStr }}" onchange="validateTransactionDate(this); calculateNewMaturityDate()">
                                        </div>
                                        <div class="form-row">
                                            <label>New Tenure (Months)</label>
                                            <input type="number" id="newTenure" name="newTenure" min="1" onchange="calculateNewMaturityDate()">
                                        </div>
                                        <div class="form-row">
                                            <label>New Maturity Date</label>
                                            <input type="date" id="newMaturityDate" name="newMaturityDate" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- WITHDRAW MESSAGE (Free FD with Withdrawable = No) -->
                                <div id="noWithdrawMessage" style="display: {{ $showWithdrawMessage ? 'block' : 'none' }};">
                                    <div class="info-message no-withdraw">
                                        <span class="info-icon">🚫</span>
                                        <span><strong>This FD is not withdrawable</strong><br>
                                        Withdrawal is not permitted for this Fixed Deposit.</span>
                                    </div>
                                </div>
                                
                                <!-- WITHDRAW OPTION -->
                                <div id="withdrawOptionContainer" style="display: {{ $showWithdrawOption ? 'block' : 'none' }};">
                                    <div class="form-row" id="withdrawRadioRow">
                                        <label id="withdrawLabel">Transaction Type</label>
                                        <div class="radio-group">
                                            <div class="radio-item">
                                                <input type="radio" id="transWithdraw" name="transactionType" value="Withdraw" onchange="handleTransactionTypeChange()">
                                                <label for="transWithdraw">Withdraw</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Withdraw Fields -->
                                    <div id="withdrawFields" style="display: none;">
                                        <div class="form-row">
                                            <label>Transaction Date</label>
                                            <input type="date" id="transactionDate" name="transactionDate[]" min="{{ $maturityDateStr }}" onchange="validateTransactionDate(this)">
                                        </div>
                                        <div class="form-row">
                                            <label>Withdraw Amount (RM)</label>
                                            <input type="number" id="withdrawAmount" name="withdrawAmount" step="0.01">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Fixed Deposit Certification -->
                        <div class="section-box">
                            <div class="section-title">Fixed Deposit Certification</div>
                            <div class="form-row">
                                <label>Certificate No.</label>
                                <input type="text" id="certificateNo" name="certificateNo" value="{{ $fd->cert_no ?? '' }}">
                            </div>
                            <div class="form-row">
                                <label>FD Certificate</label>
                                <div class="file-upload-wrapper">
                                    <label for="fdCertificate" class="file-upload-btn">
                                        <span class="file-icon">📁</span>
                                        <span class="file-name" id="fileName">Choose File</span>
                                        <input type="file" id="fdCertificate" name="fdCertificate" accept=".jpg,.jpeg,.png,.pdf">
                                    </label>
                                    <div class="file-info">JPEG, PNG, PDF</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reminder Section -->
                    <div class="reminder-section">
                        <div class="reminder-item">
                            <span class="reminder-label">Get a reminder 7 days before maturity dates</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="reminderMaturity" id="reminderMaturity" value="on" {{ $reminderMaturity === 'on' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="reminder-item">
                            <span class="reminder-label">Get a reminder for incomplete FD Details</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="reminderIncomplete" id="reminderIncomplete" value="on" {{ $reminderIncomplete === 'on' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-update" onclick="showUpdateModal()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Calculation Modal -->
    <div class="modal-overlay" id="calculationModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeCalculationModal()">×</button>
            <h2 class="modal-title">Calculations</h2>
            <div class="calculation-result">
                <label>Expected Total Profit (RM)</label>
                <input type="text" id="totalProfit" readonly>
            </div>
            <div class="calculation-result">
                <label>Expected Total Interest (RM)</label>
                <input type="text" id="totalInterest" readonly>
            </div>
        </div>
    </div>

    <!-- Update Confirmation Modal -->
    <div class="modal-overlay" id="updateModal">
        <div class="modal-content">
            <div class="modal-icon">⚠️</div>
            <div class="modal-message">
                Are you sure you want to update this fixed deposit information?
            </div>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-no" onclick="closeUpdateModal()">No</button>
                <button class="modal-btn modal-btn-yes" onclick="confirmUpdate()">Yes</button>
            </div>
        </div>
    </div>

    <script>
        // ========== MATURITY DATE CONSTANT ==========
        const MATURITY_DATE = @json($maturityDateStr);
        
        // ========== TRANSACTION DATE VALIDATION ==========
        function validateTransactionDate(field) {
            const status = document.getElementById('fdStatus').value;
            
            // Only validate when status is Matured
            if (status !== 'Matured') return true;
            
            const selectedDate = field.value;
            if (!selectedDate) return true;
            
            if (selectedDate < MATURITY_DATE) {
                showFieldError(field.id, '❌ Date cannot be before maturity date (' + formatDate(MATURITY_DATE) + ')');
                field.value = ''; // Clear invalid date
                return false;
            }
            
            // Clear any existing error
            field.classList.remove('field-error');
            const existingError = field.parentElement.parentElement.querySelector('.error-message');
            if (existingError) existingError.remove();
            
            return true;
        }
        
        // Format date for display (yyyy-mm-dd to dd/mm/yyyy)
        function formatDate(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            return dateStr;
        }
        
        // Validate all transaction dates before form submission
        function validateAllTransactionDates() {
            const status = document.getElementById('fdStatus').value;
            if (status !== 'Matured') return true;
            
            const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;
            
            if (transactionType === 'Withdraw') {
                const transDateField = document.getElementById('transactionDate');
                if (transDateField && transDateField.value) {
                    if (!validateTransactionDate(transDateField)) return false;
                }
            } else if (transactionType === 'Reinvest') {
                const reinvestTransDateField = document.getElementById('reinvestTransactionDate');
                const newStartDateField = document.getElementById('newStartDate');
                
                if (reinvestTransDateField && reinvestTransDateField.value) {
                    if (!validateTransactionDate(reinvestTransDateField)) return false;
                }
                if (newStartDateField && newStartDateField.value) {
                    if (!validateTransactionDate(newStartDateField)) return false;
                }
            }
            
            return true;
        }

        // ========== UPDATE CONFIRMATION MODAL FUNCTIONS ==========
        function showUpdateModal() {
            // Validate form before showing modal
            const form = document.getElementById('updateFDForm');
            
            // Check basic form validation
            if (!form.checkValidity()) {
                // Show "This field is required." for empty required fields
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(function(field) {
                    if (!field.value || field.value.trim() === '') {
                        showFieldError(field.id, 'This field is required.');
                    }
                });
                return;
            }

            // Validate deposit amount - digits only and > 0
            const depositAmountVal = document.getElementById('depositAmount').value;
            if (depositAmountVal) {
                const depositNum = parseFloat(depositAmountVal);
                if (isNaN(depositNum)) {
                    showFieldError('depositAmount', 'Deposit amount must contain only digits without letters and symbols.');
                    return;
                } else if (depositNum <= 0) {
                    showFieldError('depositAmount', 'Deposit amount must be greater than zero.');
                    return;
                }
            }

            // Run custom validations
            clearFieldErrors();
            
            const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
            const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;
            
            // Validate transaction dates (must be >= maturity date when status is Matured)
            if (!validateAllTransactionDates()) {
                return;
            }
            
            // Validate pledge value
            if (fdType === 'Pledge') {
                const pledgeValue = document.getElementById('pledgeValue')?.value;
                if (pledgeValue) {
                    if (!validatePledgeValue()) {
                        return;
                    }
                }
            }
            
            // Validate withdrawal
            if (transactionType === 'Withdraw') {
                if (!validateWithdrawAmount()) {
                    return;
                }
            }
            
            // Show the modal
            document.getElementById('updateModal').classList.add('active');
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').classList.remove('active');
        }

        function confirmUpdate() {
            closeUpdateModal();
            document.getElementById('updateFDForm').submit();
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('updateModal').addEventListener('click', function(e) {
                if (e.target === this) closeUpdateModal();
            });
        });

        // ========== FD TYPE AND STATUS FUNCTIONS ==========
        // Handle FD Type change (Pledge/Free)
        function handleFDTypeChange() {
            const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
            document.getElementById('pledgeFields').style.display = fdType === 'Pledge' ? 'block' : 'none';
            document.getElementById('freeFields').style.display = fdType === 'Free' ? 'block' : 'none';
            
            // Update transaction options when FD type changes
            updateTransactionOptions();
        }

        // Handle Status change - Show/Hide Transaction section
        function handleStatusChange() {
            const status = document.getElementById('fdStatus').value;
            const transactionSection = document.getElementById('transactionSection');
            
            if (status === 'Matured') {
                transactionSection.style.display = 'block';
                updateTransactionOptions(); // Update options when showing
            } else {
                transactionSection.style.display = 'none';
                // Reset transaction fields when hiding
                const withdrawRadio = document.getElementById('transWithdraw');
                const reinvestRadio = document.getElementById('transReinvest');
                if (withdrawRadio) withdrawRadio.checked = false;
                if (reinvestRadio) reinvestRadio.checked = false;
                
                const withdrawFields = document.getElementById('withdrawFields');
                const reinvestFields = document.getElementById('reinvestFields');
                if (withdrawFields) withdrawFields.style.display = 'none';
                if (reinvestFields) reinvestFields.style.display = 'none';
            }
        }

        // Update transaction options based on current FD type and settings
        function updateTransactionOptions() {
            const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
            const isFreeFD = (fdType === 'Free');
            const isPledgeFD = (fdType === 'Pledge');
            
            console.log('🔄 Updating transaction options for:', fdType);
            
            // ==================== HANDLE PLEDGE FD FIRST ====================
            const pledgeContainer = document.getElementById('pledgeMessagesContainer');
            
            if (isPledgeFD) {
                // Show Pledge FD messages
                if (pledgeContainer) {
                    pledgeContainer.style.display = 'block';
                    console.log('✅ Showing Pledge FD messages');
                }
                
                // Hide ALL Free FD options and messages
                const autoRenewMsg = document.getElementById('autoRenewMessage');
                const reinvestContainer = document.getElementById('reinvestOptionContainer');
                const noWithdrawMsg = document.getElementById('noWithdrawMessage');
                const withdrawContainer = document.getElementById('withdrawOptionContainer');
                
                if (autoRenewMsg) autoRenewMsg.style.display = 'none';
                if (reinvestContainer) reinvestContainer.style.display = 'none';
                if (noWithdrawMsg) noWithdrawMsg.style.display = 'none';
                if (withdrawContainer) withdrawContainer.style.display = 'none';
                
                console.log('✅ Hidden all Free FD options');
                return; // EXIT EARLY - Don't process Free FD logic
            }
            
            // ==================== HANDLE FREE FD ====================
            // Hide Pledge messages for Free FD
            if (pledgeContainer) {
                pledgeContainer.style.display = 'none';
            }
            
            // Get Free FD settings
            const autoRenewalSelect = document.getElementById('autoRenewalStatus');
            const withdrawableSelect = document.getElementById('withdrawableStatus');
            const autoRenewal = autoRenewalSelect ? autoRenewalSelect.value : 'No';
            const withdrawable = withdrawableSelect ? withdrawableSelect.value : 'No';
            
            console.log('📊 Free FD settings:', {
                autoRenewal: autoRenewal,
                withdrawable: withdrawable
            });
            
            // Calculate what to show for Free FD
            const showAutoRenewMessage = (autoRenewal === 'Yes');
            const showReinvestOption = (autoRenewal === 'No');
            const showNoWithdrawMessage = (withdrawable === 'No');
            const showWithdrawOption = (withdrawable === 'Full' || withdrawable === 'Partial');
            
            console.log('📊 Display logic:', {
                showAutoRenewMessage: showAutoRenewMessage,
                showReinvestOption: showReinvestOption,
                showNoWithdrawMessage: showNoWithdrawMessage,
                showWithdrawOption: showWithdrawOption
            });
            
            // Update visibility for Free FD
            const autoRenewMsg = document.getElementById('autoRenewMessage');
            const reinvestContainer = document.getElementById('reinvestOptionContainer');
            const noWithdrawMsg = document.getElementById('noWithdrawMessage');
            const withdrawContainer = document.getElementById('withdrawOptionContainer');
            
            if (autoRenewMsg) {
                autoRenewMsg.style.display = showAutoRenewMessage ? 'block' : 'none';
                console.log('Auto-renew message:', showAutoRenewMessage ? 'visible' : 'hidden');
            }
            
            if (reinvestContainer) {
                reinvestContainer.style.display = showReinvestOption ? 'block' : 'none';
                console.log('Reinvest option:', showReinvestOption ? 'visible' : 'hidden');
            }
            
            if (noWithdrawMsg) {
                noWithdrawMsg.style.display = showNoWithdrawMessage ? 'block' : 'none';
                console.log('No-withdraw message:', showNoWithdrawMessage ? 'visible' : 'hidden');
            }
            
            if (withdrawContainer) {
                withdrawContainer.style.display = showWithdrawOption ? 'block' : 'none';
                console.log('Withdraw option:', showWithdrawOption ? 'visible' : 'hidden');
            }
            
            // Update withdraw label
            const withdrawLabel = document.getElementById('withdrawLabel');
            if (withdrawLabel) {
                withdrawLabel.textContent = showReinvestOption ? '' : 'Transaction Type';
            }
            
            // Reset radio buttons and hide fields when options change
            const withdrawRadio = document.getElementById('transWithdraw');
            const reinvestRadio = document.getElementById('transReinvest');
            if (withdrawRadio) withdrawRadio.checked = false;
            if (reinvestRadio) reinvestRadio.checked = false;
            
            const withdrawFields = document.getElementById('withdrawFields');
            const reinvestFields = document.getElementById('reinvestFields');
            if (withdrawFields) withdrawFields.style.display = 'none';
            if (reinvestFields) reinvestFields.style.display = 'none';
            
            console.log('✅ Transaction options updated successfully');
        }

        // Handle Transaction Type change (Withdraw/Reinvest)
        function handleTransactionTypeChange() {
            const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;
            const withdrawFields = document.getElementById('withdrawFields');
            const reinvestFields = document.getElementById('reinvestFields');
            
            console.log('💳 Transaction type changed to:', transactionType);
            
            if (withdrawFields && reinvestFields) {
                if (transactionType === 'Withdraw') {
                    withdrawFields.style.display = 'block';
                    reinvestFields.style.display = 'none';
                    console.log('✅ Showing withdraw fields');
                } else if (transactionType === 'Reinvest') {
                    withdrawFields.style.display = 'none';
                    reinvestFields.style.display = 'block';
                    console.log('✅ Showing reinvest fields');
                } else {
                    withdrawFields.style.display = 'none';
                    reinvestFields.style.display = 'none';
                }
            } else {
                console.error('❌ Could not find withdrawFields or reinvestFields');
            }
        }

        // Calculate maturity date for original FD
        function calculateMaturityDate() {
            const startDate = document.getElementById('startDate').value;
            const tenure = parseInt(document.getElementById('tenure').value) || 0;

            if (startDate && tenure > 0) {
                const start = new Date(startDate);
                const originalDay = start.getDate();
                start.setMonth(start.getMonth() + tenure);
                if (start.getDate() !== originalDay) start.setDate(0);

                const year = start.getFullYear();
                const month = String(start.getMonth() + 1).padStart(2, '0');
                const day = String(start.getDate()).padStart(2, '0');
                document.getElementById('maturityDate').value = year + '-' + month + '-' + day;
            }
        }

        // Calculate new maturity date for Reinvest
        function calculateNewMaturityDate() {
            const newStartDate = document.getElementById('newStartDate')?.value;
            const newTenure = parseInt(document.getElementById('newTenure')?.value) || 0;

            if (newStartDate && newTenure > 0) {
                const start = new Date(newStartDate);
                const originalDay = start.getDate();
                start.setMonth(start.getMonth() + newTenure);
                if (start.getDate() !== originalDay) start.setDate(0);

                const year = start.getFullYear();
                const month = String(start.getMonth() + 1).padStart(2, '0');
                const day = String(start.getDate()).padStart(2, '0');
                const newMaturityField = document.getElementById('newMaturityDate');
                if (newMaturityField) {
                    newMaturityField.value = year + '-' + month + '-' + day;
                }
            }
        }

        // Calculate interest
        function calculateInterest() {
            const depositAmount = parseFloat(document.getElementById('depositAmount').value) || 0;
            const interestRate = parseFloat(document.getElementById('interestRate').value) || 0;
            const tenure = parseFloat(document.getElementById('tenure').value) || 0;

            if (depositAmount && interestRate && tenure) {
                const interest = (depositAmount * interestRate * tenure) / (100 * 12);
                const totalAmount = depositAmount + interest;
                document.getElementById('totalProfit').value = totalAmount.toFixed(2);
                document.getElementById('totalInterest').value = interest.toFixed(2);
                document.getElementById('calculationModal').classList.add('show');
            } else {
                showNotification('Please fill in Deposit Amount, Interest Rate, and Tenure to calculate.', false);
            }
        }

        // Close calculation modal
        function closeCalculationModal() {
            document.getElementById('calculationModal').classList.remove('show');
        }

        document.getElementById('calculationModal').addEventListener('click', function(e) {
            if (e.target === this) closeCalculationModal();
        });

        // Show notification
        function showNotification(message, isSuccess) {
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();

            const notification = document.createElement('div');
            notification.className = 'notification ' + (isSuccess ? 'success' : 'error');
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('show'), 10);
            setTimeout(() => {
                notification.classList.remove('show');
                notification.classList.add('hide');
                setTimeout(() => notification.remove(), 400);
            }, 3000);
        }

        // Reminder toggle handlers
        document.getElementById('reminderMaturity').addEventListener('change', function() {
            showNotification(this.checked ? 'Fixed Deposit Maturity Reminder turned on!' : 'Fixed Deposit Maturity Reminder turned off!', this.checked);
        });

        document.getElementById('reminderIncomplete').addEventListener('change', function() {
            showNotification(this.checked ? 'Incomplete Fixed Deposit Details Reminder turned on!' : 'Incomplete Fixed Deposit Details Reminder turned off!', this.checked);
        });

        // File upload display
        document.getElementById('fdCertificate').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose File';
            document.getElementById('fileName').textContent = fileName;
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-expand Fixed Deposits menu in sidebar
            const fdDropdown = document.getElementById('fdDropdown');
            const fdNavItem = document.getElementById('fdNavItem');
            if (fdDropdown && fdNavItem) {
                fdDropdown.classList.add('show');
                fdNavItem.classList.add('open');
            }
            
            // Initialize transaction options on load
            updateTransactionOptions();
            
            console.log('Page loaded - Initial settings:', {
                reminderMaturity: @json($reminderMaturity),
                reminderIncomplete: @json($reminderIncomplete),
                fdType: @json($fdTypeDisplay),
                autoRenewal: @json($autoRenewalStatus),
                withdrawable: @json($withdrawableStatus)
            });
        });
        
     // Show inline error message
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            
            // Add error styling
            field.classList.add('field-error');
            
            // Remove existing error message
            const existingError = field.parentElement.parentElement.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message
            const errorSpan = document.createElement('span');
            errorSpan.className = 'error-message';
            errorSpan.textContent = message;
            field.parentElement.parentElement.appendChild(errorSpan);
            
            // Focus on field
            field.focus();
            
            // Remove error styling when user starts typing
            field.addEventListener('input', function() {
                field.classList.remove('field-error');
                const error = field.parentElement.parentElement.querySelector('.error-message');
                if (error) error.remove();
            }, { once: true });
        }
        
        // Clear all field errors
        function clearFieldErrors() {
            document.querySelectorAll('.field-error').forEach(el => {
                el.classList.remove('field-error');
            });
            document.querySelectorAll('.error-message').forEach(el => {
                el.remove();
            });
        }
        
        // Validate withdraw amount
        function validateWithdrawAmount() {
            const withdrawAmount = parseFloat(document.getElementById('withdrawAmount')?.value || 0);
            const withdrawableStatus = document.getElementById('withdrawableStatus')?.value;
            const remainingBalance = {{ $remainingBalanceJs }};
            const depositAmount = {{ $depositAmountJs }};
            
            if (withdrawAmount <= 0) return true;
            
            // Check remaining balance
            if (withdrawAmount > remainingBalance) {
                showFieldError('withdrawAmount', 
                    '❌ Withdrawal (RM ' + withdrawAmount.toFixed(2) + ') exceeds balance (RM ' + remainingBalance.toFixed(2) + ')');
                return false;
            }
            
            // Check half limit for Partial
            if (withdrawableStatus === 'Partial') {
                const halfBalance = remainingBalance / 2;
                if (withdrawAmount > halfBalance) {
                    showFieldError('withdrawAmount', 
                        '❌ Partial withdrawal max RM ' + halfBalance.toFixed(2) + ' (half of balance RM ' + remainingBalance.toFixed(2) + ')');
                    return false;
                }
            }
            
            return true;
        }
        
        // WITHDRAW FIELD LOGIC - FIXED
        function updateWithdrawAmountLimit() {
            const withdrawAmountField = document.getElementById('withdrawAmount');
            const withdrawableStatusField = document.getElementById('withdrawableStatus');
            
            if (!withdrawAmountField || !withdrawableStatusField) return;
            
            const withdrawableStatus = withdrawableStatusField.value;  // Get current value
            const remainingBalance = {{ $remainingBalanceJs }};
            const depositAmount = {{ $depositAmountJs }};
            
            let maxWithdraw = remainingBalance;
            let placeholderText = '';

            // ==== CONDITION BASED ON withdrawableStatus ====
            if (withdrawableStatus === 'Full') {
                maxWithdraw = remainingBalance;
                placeholderText = 'Max: RM ' + remainingBalance.toFixed(2);
            } else if (withdrawableStatus === 'Partial') {
                const halfBalance = remainingBalance / 2;  // Half of BALANCE
                maxWithdraw = halfBalance;
                placeholderText = 'Max: RM ' + halfBalance.toFixed(2) + ' (half)';
            }

            withdrawAmountField.max = maxWithdraw;
            withdrawAmountField.placeholder = placeholderText;

            // revalidate current input
            if (withdrawAmountField.value) {
                validateWithdrawAmount();
            }
        }


        // initialize on load



        
        // Validate pledge value
        function validatePledgeValue() {
            const pledgeInput = document.getElementById('pledgeValue');
            const pledgeValue = parseFloat(pledgeInput?.value || 0);
            const collateralStatus = document.getElementById('collateralStatus')?.value;
            const depositAmount = {{ $depositAmountJs }};
            
            if (pledgeValue <= 0) {
                showFieldError('pledgeValue', 'Pledge value must be greater than zero.');
                return false;
            }
            
            if (collateralStatus === 'Active') {
                if (pledgeValue > depositAmount) {
                    showFieldError('pledgeValue', 
                        'Pledge value cannot exceed RM ' + depositAmount.toFixed(2) + ' (full deposit amount).');
                    return false;
                }
            } else if (collateralStatus === 'Partial') {
                const halfDeposit = depositAmount / 2;
                if (pledgeValue > halfDeposit) {
                    showFieldError('pledgeValue', 
                        'Pledge value cannot exceed RM ' + halfDeposit.toFixed(2) + ' (half of deposit amount) for Partial collateral.');
                    return false;
                }
            }
            
            return true;
        }
        
        // Update pledge value when collateral status changes (mirrors CreateFD behaviour)
        function updatePledgeValueLimit() {
            clearFieldErrors();
            const collateralStatus = document.getElementById('collateralStatus')?.value;
            const depositAmount = {{ $depositAmountJs }};
            const pledgeValueInput = document.getElementById('pledgeValue');
            let pledgeHint = document.getElementById('pledgeValueHint');

            // Create hint div if not present
            if (!pledgeHint) {
                pledgeHint = document.createElement('div');
                pledgeHint.id = 'pledgeValueHint';
                pledgeHint.style.cssText = 'font-size:12px; color:#666; margin-top:4px; display:none;';
                pledgeValueInput.parentNode.appendChild(pledgeHint);
            }

            if (!pledgeValueInput) return;

            if (collateralStatus === 'Active') {
                // Auto-fill full deposit amount, read-only
                pledgeValueInput.value = depositAmount > 0 ? depositAmount.toFixed(2) : '';
                pledgeValueInput.removeAttribute('max');
                pledgeValueInput.readOnly = true;
                pledgeValueInput.style.cursor = 'not-allowed';
                pledgeHint.style.display = 'none';
            } else if (collateralStatus === 'Partial') {
                // Max is half of deposit, editable
                const halfDeposit = (depositAmount / 2).toFixed(2);
                pledgeValueInput.value = '';
                pledgeValueInput.setAttribute('max', halfDeposit);
                pledgeValueInput.readOnly = false;
                pledgeValueInput.style.cursor = 'auto';
                if (depositAmount > 0) {
                    pledgeHint.textContent = 'Maximum pledge value for Partial: RM ' + parseFloat(halfDeposit).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    pledgeHint.style.display = 'block';
                } else {
                    pledgeHint.textContent = 'Please enter Deposit Amount first.';
                    pledgeHint.style.display = 'block';
                }
            } else {
                pledgeValueInput.value = '';
                pledgeValueInput.removeAttribute('max');
                pledgeValueInput.readOnly = false;
                pledgeValueInput.style.cursor = 'auto';
                pledgeHint.style.display = 'none';
            }
        }
        
        // Form submit validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('updateFDForm');
            
            form.addEventListener('submit', function(e) {
                clearFieldErrors();
                
                const fdType = document.querySelector('input[name="fdType"]:checked')?.value;
                const transactionType = document.querySelector('input[name="transactionType"]:checked')?.value;
                
                // Validate transaction dates (must be >= maturity date when status is Matured)
                if (!validateAllTransactionDates()) {
                    e.preventDefault();
                    return false;
                }
                
                // Validate pledge value
                if (fdType === 'Pledge') {
                    const pledgeValue = document.getElementById('pledgeValue')?.value;
                    if (pledgeValue && parseFloat(pledgeValue) > 0) {
                        if (!validatePledgeValue()) {
                            e.preventDefault();
                            return false;
                        }
                    }
                }
                
                // Validate withdrawal
                if (transactionType === 'Withdraw') {
                    if (!validateWithdrawAmount()) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            
            // Add onchange handlers
            const pledgeValueField = document.getElementById('pledgeValue');
            if (pledgeValueField) {
                pledgeValueField.addEventListener('change', validatePledgeValue);
            }
            
            const withdrawAmountField = document.getElementById('withdrawAmount');
            if (withdrawAmountField) {
                withdrawAmountField.addEventListener('change', validateWithdrawAmount);
            }
            
            const collateralStatusField = document.getElementById('collateralStatus');
            if (collateralStatusField) {
                collateralStatusField.addEventListener('change', updatePledgeValueLimit);
                // Run on page load to apply correct state for existing collateral status
                updatePledgeValueLimit();
            }
            
            // Initialize and handle withdrawable status changes
            updateWithdrawAmountLimit();
            
            const withdrawableStatusField = document.getElementById("withdrawableStatus");
            if (withdrawableStatusField) {
                withdrawableStatusField.addEventListener("change", function() {
                    updateWithdrawAmountLimit();
                });
            }
        });
    </script>
</body>
</html>
