@php
    // Format dates for display
    $startDateStr = $fd->start_date ? $fd->start_date->format('d/m/Y') : '-';
    $maturityDateStr = $fd->maturity_date ? $fd->maturity_date->format('d/m/Y') : '-';

    // Format numbers
    $depositAmountStr = $fd->deposit_amount !== null ? number_format((float) $fd->deposit_amount, 2) : '-';
    $interestRateStr = $fd->interest_rate !== null ? rtrim(rtrim((string) $fd->interest_rate, '0'), '.') : '-';

    $pledgeValueStr = $pledgeValue !== null ? number_format((float) $pledgeValue, 2) : '-';

    // Convert fdType for display
    $fdTypeDisplay = $fd->fd_type === 'FREEFD' ? 'Free' : 'Pledge';

    // Convert status for display
    $statusDisplay = $fd->status ? $fd->display_status : '-';

    // Transaction section is only shown for matured FDs
    $isMatured = strcasecmp($statusDisplay, 'Matured') === 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Fixed Deposit FD{{ $fd->fd_id }} - Fixed Deposit Tracking System</title>
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

        .form-row .form-value {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            background-color: #f0f0f0;
            color: #2c3e50;
        }

        .radio-display {
            display: flex;
            gap: 30px;
            flex: 1;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-item input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #1a4d5e;
            pointer-events: none;
        }

        .radio-item label {
            width: auto;
            font-size: 13px;
        }

        .file-display {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-display-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f0f0f0;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            font-size: 13px;
            flex: 1;
        }

        .file-icon { font-size: 18px; color: #7f8c8d; }
        .file-name { color: #2c3e50; font-size: 13px; }
        .file-info { font-size: 11px; color: #7f8c8d; }

        .conditional-fields {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #d0d0d0;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
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

        .btn-edit {
            background: #1a4d5e;
            color: white;
        }

        .btn-edit:hover {
            background: #153d4a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 77, 94, 0.3);
        }

        .btn-print {
            background: #003f5c;
            color: white;
        }

        .btn-print:hover {
            background: #002d42;
        }


        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }
        .btn-download {
            background: #003f5c;
            color: white;
        }

        .btn-download:hover {
            background: #002d42;
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge.pending {
            background: #ffe5e5;
            color: #c0392b;
        }

        .status-badge.ongoing {
            background: #fff9e5;
            color: #d68910;
        }

        .status-badge.matured {
            background: #e5f5f0;
            color: #0c7a5a;
        }

        @media print {
            .sidebar, .header, .form-actions, .back-button {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0;
            }

            .form-container {
                box-shadow: none;
                border: 1px solid #e0e0e0;
            }

            body {
                background: white;
            }
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
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'View Fixed Deposit'])

        <div class="page-content">
            <a href="{{ route('fd.list') }}" class="back-button">
                <span class="back-icon">←</span>
                <span>Back</span>
            </a>

            <div class="form-container">
                <!-- Row 1: Reference & Type -->
                <div class="section-grid">
                    <div class="section-box">
                        <div class="section-title">Fixed Deposit Reference</div>
                        <div class="form-row">
                            <label>Fixed Deposit ID</label>
                            <div class="form-value">FD{{ $fd->fd_id }}</div>
                        </div>
                        <div class="form-row">
                            <label>Account Number</label>
                            <div class="form-value">{{ $fd->acc_number ?? '-' }}</div>
                        </div>
                        <div class="form-row">
                            <label>Referral Number</label>
                            <div class="form-value">{{ $fd->referral_number ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-title">Fixed Deposit Type</div>
                        <div class="form-row">
                            <label>FD Type</label>
                            <div class="radio-display">
                                <div class="radio-item">
                                    <input type="radio" id="typePledge" name="fdType" value="Pledge" {{ $fdTypeDisplay === 'Pledge' ? 'checked' : '' }} disabled>
                                    <label for="typePledge">Pledge FD</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="typeFree" name="fdType" value="Free" {{ $fdTypeDisplay === 'Free' ? 'checked' : '' }} disabled>
                                    <label for="typeFree">Free FD</label>
                                </div>
                            </div>
                        </div>

                        <!-- Pledge FD Fields -->
                        <div id="pledgeFields" class="conditional-fields" style="display: {{ $fdTypeDisplay === 'Pledge' ? 'block' : 'none' }};">
                            <div class="form-row">
                                <label>Collateral Status</label>
                                <div class="form-value">{{ $collateralStatus }}</div>
                            </div>
                            <div class="form-row">
                                <label>Pledge Value (RM)</label>
                                <div class="form-value">{{ $pledgeValueStr }}</div>
                            </div>
                        </div>

                        <!-- Free FD Fields -->
                        <div id="freeFields" class="conditional-fields" style="display: {{ $fdTypeDisplay === 'Free' ? 'block' : 'none' }};">
                            <div class="form-row">
                                <label>Auto Renewal Status</label>
                                <div class="form-value">{{ $autoRenewalStatus }}</div>
                            </div>
                            <div class="form-row">
                                <label>Withdrawable Status</label>
                                <div class="form-value">{{ $withdrawableStatus }}</div>
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
                                <div class="form-value">{{ $fd->bank_name ?? '-' }}</div>
                            </div>
                            <div class="form-row">
                                <label>Start Date</label>
                                <div class="form-value">{{ $startDateStr }}</div>
                            </div>
                            <div class="form-row">
                                <label>Maturity Date</label>
                                <div class="form-value">{{ $maturityDateStr }}</div>
                            </div>
                            <div class="form-row">
                                <label>Interest Rate (%)</label>
                                <div class="form-value">{{ $interestRateStr }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="form-row">
                                <label>Deposit Amount (RM)</label>
                                <div class="form-value">{{ $depositAmountStr }}</div>
                            </div>
                            <div class="form-row">
                                <label>Tenure (Months)</label>
                                <div class="form-value">{{ $fd->tenure }}</div>
                            </div>
                            <div class="form-row">
                                <label>Status</label>
                                <div class="form-value">{{ $statusDisplay }} </div>
                            </div>
                            <div class="form-row">
                                <label>Created By</label>
                                <div class="form-value">{{ $createdBy ?? 'Admin' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Transaction & Certification -->
                <div class="section-grid">
                    <!-- Row 4: Calculation Results -->
                    <div class="section-box">
                        <div class="section-title">Expected Calculations</div>
                        <div class="form-row">
                            <label>Expected Total Profit (RM)</label>
                            <div class="form-value">{{ $totalProfit }}</div>
                        </div>
                        <div class="form-row">
                            <label>Expected Total Interest (RM)</label>
                            <div class="form-value">{{ $totalInterest }}</div>
                        </div>
                    </div>


                    <!-- Certification -->
                    <div class="section-box">
                        <div class="section-title">Fixed Deposit Certification</div>
                        <div class="form-row">
                            <label>Certificate No.</label>
                            <div class="form-value">{{ $fd->cert_no ?? '-' }}</div>
                        </div>
                        <div class="form-row">
                            <label>FD Certificate</label>
                            <div class="file-display">
                                @if ($fd->hasCertificateFile())
                                    @if ($certIsPdf)
                                <a href="{{ route('fd.certificate', ['id' => $fd->fd_id, 'download' => 1]) }}" class="cert-download-btn">
                                    📄 Download Certificate (PDF)
                                </a>
                                    @else
                                <img src="{{ route('fd.certificate', ['id' => $fd->fd_id]) }}" alt="FD Certificate" style="max-width:100%; max-height:300px; border-radius:8px; border:1px solid #ddd; margin-top:8px;">
                                    @endif
                                @else
                                <div class="file-display-box">
                                    <span class="file-icon">📁</span>
                                    <span class="file-name">No file uploaded</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transaction Section - Only show if Matured -->
                    <div class="section-box" id="transactionSection" style="display: {{ $isMatured ? 'block' : 'none' }};">
                        <div class="section-title">Fixed Deposit Transaction</div>
                        
                        <!-- Balance Summary for Matured FDs -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
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
                </div>

                

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="button" class="btn btn-edit" onclick="editFD()">Edit</button>
                    <button type="button" class="btn btn-secondary" onclick="viewApplicationForm()">View Application Form</button>
                    <button type="button" class="btn btn-print" onclick="window.print()">Print Report</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Edit FD - open the update page
        function editFD() {
            window.location.href = '{{ route('fd.update', ['id' => $fd->fd_id]) }}';
        }
        // View Application Form
        function viewApplicationForm() {
            window.location.href = '{{ route('fd.application.view', ['id' => $fd->fd_id]) }}';
        }


        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-expand Fixed Deposits menu
            const fdDropdown = document.getElementById('fdDropdown');
            const fdNavItem = document.getElementById('fdNavItem');
            if (fdDropdown && fdNavItem) {
                fdDropdown.classList.add('show');
                fdNavItem.classList.add('open');
            }
        });
    </script>
</body>
</html>
