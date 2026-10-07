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
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/update.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/update.js',
    ])
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
                        <div class="two-col">
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
                            <div style="background: #f7f8fa; border: 1px solid #e2e5ea; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <div class="two-col">
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;">💰 Remaining Balance</div>
                                        <div style="font-size: 15px; font-weight: 600; color: #14171c;">
                                            RM {{ number_format((float) ($fd->remaining_balance ?? 0), 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;">📤 Total Withdrawn</div>
                                        <div style="font-size: 15px; font-weight: 600; color: #14171c;">
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

    {{-- Values for resources/js/pages/fd/update.js --}}
    <script>
        window.pageData = {
            maturityDate: @json($maturityDateStr),
            reminderMaturity: @json($reminderMaturity),
            reminderIncomplete: @json($reminderIncomplete),
            fdType: @json($fdTypeDisplay),
            autoRenewal: @json($autoRenewalStatus),
            withdrawable: @json($withdrawableStatus),
            remainingBalance: {{ $remainingBalanceJs }},
            depositAmount: {{ $depositAmountJs }},
        };
    </script>
</body>
</html>
