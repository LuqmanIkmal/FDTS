@php
    // Start date may be up to 5 days in the past
    $minStartDate = now()->subDays(5)->format('Y-m-d');
    $maxStartDate = now()->format('Y-m-d');

    // Draft data from session (if user clicked Back from the Application Form)
    $draft = session('fdDraft', []);
    $accountNumber = $draft['accountNumber'] ?? '';
    $referralNumber = $draft['referralNumber'] ?? '';
    $bankName = $draft['bankName'] ?? '';
    $depositAmount = $draft['depositAmount'] ?? '';
    $interestRate = $draft['interestRate'] ?? '';
    $startDate = $draft['startDate'] ?? '';
    $tenure = $draft['tenure'] ?? '';
    $maturityDate = $draft['maturityDate'] ?? '';
    $certNo = $draft['certNo'] ?? '';
    $fdType = $draft['fdType'] ?? '';

    // Free FD fields
    $autoRenewalStatus = $draft['autoRenewalStatus'] ?? '';
    $withdrawableStatus = $draft['withdrawableStatus'] ?? '';

    // Pledge FD fields
    $pledgeValue = $draft['pledgeValue'] ?? '';
    $collateralStatus = $draft['collateralStatus'] ?? '';

    // Reminder settings
    $reminderMaturity = $draft['reminderMaturity'] ?? '';
    $reminderIncomplete = $draft['reminderIncomplete'] ?? '';
    $fdCertFileName = $draft['fdCertFileName'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Fixed Deposits - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/create.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/create.js',
    ])
</head>
<body>
    <!-- Include Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('partials.header', ['pageTitle' => 'Create Fixed Deposit'])

        <!-- Page Content -->
        <div class="page-content">
        
            <a href="{{ route('fd.list') }}" class="back-button">
                <span class="back-icon">←</span>
                <span>Back</span>
            </a>
            
            <!-- Error Message Display -->
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Success Message Display -->
            @if (session('successMessage'))
                <div class="alert alert-success">
                    {{ session('successMessage') }}
                </div>
            @endif

            <div class="form-container">
                <!-- Form submits to FixedDepositController@storeDraft -->
                <form id="createFDForm" action="{{ route('fd.create.submit') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="form-grid">
                        <!-- Account Number -->
                        <div class="form-group">
                            <label for="accountNumber">Account Number</label>
                            <input type="text" id="accountNumber" name="accountNumber" value="{{ $accountNumber }}" required>
                        </div>
                        
                        <!-- Referral Number -->
                        <div class="form-group">
                            <label for="referralNumber">Referral Number</label>
                            <input type="text" id="referralNumber" name="referralNumber" value="{{ $referralNumber }}">
                        </div>
                        
                        <!-- Bank Name -->
                        <div class="form-group">
                            <label for="bankName">Bank Name</label>
                            <select id="bankName" name="bankName" required>
                                <option value="">Select Bank</option>
                                @forelse ($bankList as $bank)
                                <option value="{{ $bank->bank_name }}" {{ $bank->bank_name === $bankName ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                @empty
                                <option value="" disabled>No banks available</option>
                                @endforelse
                            </select>
                        </div>
                        
                        <!-- Deposit Amount -->
                        <div class="form-group">
                            <label for="depositAmount">Deposit Amount (RM)</label>
                            <input type="number" id="depositAmount" name="depositAmount" step="0.01" min="0.01" value="{{ $depositAmount }}" required>
                        </div>
                        
                        <!-- Interest Rate -->
                        <div class="form-group">
                            <label for="interestRate">Interest Rate (%)</label>
                            <input type="number" id="interestRate" name="interestRate" step="0.001" min="0.001" value="{{ $interestRate }}" required>
                            <a href="#" class="calculate-link" onclick="calculateInterest(); return false;">Calculate</a>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group">
                            <label for="startDate">Start Date</label>
                            <input type="date" id="startDate" name="startDate" value="{{ $startDate }}" 
                                   min="{{ $minStartDate }}" max="{{ $maxStartDate }}" 
                                   required onchange="calculateMaturityDate()">
                        </div>
                        
                        <!-- Tenure -->
                        <div class="form-group">
                            <label for="tenure">Tenure (Month)</label>
                            <input type="number" id="tenure" name="tenure" min="1" value="{{ $tenure }}" required onchange="calculateMaturityDate()">
                        </div>

                        <!-- Maturity Date -->
                        <div class="form-group">
                            <label for="maturityDate">Maturity Date</label>
                            <input type="date" id="maturityDate" name="maturityDate" value="{{ $maturityDate }}" readonly required style="background-color: #f5f5f5; cursor: not-allowed;">
                        </div>

                        <!-- FD Certificate Upload -->
                        <div class="form-group">
                            <label for="fdCertificate">FD Certificate</label>
                            <div class="file-upload-wrapper">
                                <label for="fdCertificate" class="file-upload-btn">
                                    <span class="file-icon">📁</span>
                                    <span class="file-text" id="fileText">Choose File</span>
                                    <input type="file" id="fdCertificate" name="fdCertificate">
                                </label>
                            </div>
                            <div class="file-info">JPEG, PNG, PDF</div>
                        </div>

                        <!-- FD Certificate No. -->
                        <div class="form-group">
                            <label for="fdCertificateNo">FD Certificate No.</label>
                            <input type="text" id="fdCertificateNo" name="fdCertificateNo" value="{{ $certNo }}">
                        </div>

                        <!-- FD Type -->
                        <div class="form-group">
                            <label for="fdType">FD Type</label>
                            <select id="fdType" name="fdType" required onchange="handleFDTypeChange()">
                                <option value="">Select FD Type</option>
                                <option value="Free" {{ $fdType === 'Free' ? 'selected' : '' }}>Free</option>
                                <option value="Pledge" {{ $fdType === 'Pledge' ? 'selected' : '' }}>Pledge</option>
                            </select>
                        </div>

                        <!-- Free FD: Auto Renewal Status (Hidden by default) -->
                        <div class="form-group" id="autoRenewalField" style="display: none;">
                            <label for="autoRenewalStatus">Auto Renewal Status</label>
                            <select id="autoRenewalStatus" name="autoRenewalStatus">
                                <option value="">Select Status</option>
                                <option value="Y" {{ $autoRenewalStatus === 'Y' ? 'selected' : '' }}>Yes</option>
                                <option value="N" {{ $autoRenewalStatus === 'N' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Free FD: Withdrawable Status (Hidden by default) -->
                        <div class="form-group" id="withdrawableField" style="display: none;">
                            <label for="withdrawableStatus">Withdrawable Status</label>
                            <select id="withdrawableStatus" name="withdrawableStatus">
                                <option value="">Select Status</option>
                                <option value="Full" {{ $withdrawableStatus === 'Full' ? 'selected' : '' }}>Full</option>
                                <option value="Partial" {{ $withdrawableStatus === 'Partial' ? 'selected' : '' }}>Partial</option>
                                <option value="No" {{ $withdrawableStatus === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Pledge FD: Pledge Value (Hidden by default) -->
                        <div class="form-group" id="pledgeValueField" style="display: none;">
                            <label for="pledgeValue">Pledge Value (RM)</label>
                            <input type="number" id="pledgeValue" name="pledgeValue" step="0.01" min="0.01" value="{{ $pledgeValue }}" placeholder="Enter pledge value">
                            <div id="pledgeValueHint" style="font-size:12px; color:#666; margin-top:4px; display:none;"></div>
                        </div>

                        <!-- Pledge FD: Collateral Status (Hidden by default) -->
                        <div class="form-group" id="collateralField" style="display: none;">
                            <label for="collateralStatus">Collateral Status</label>
                            <select id="collateralStatus" name="collateralStatus" onchange="handleCollateralChange()">
                                <option value="">Select Status</option>
                                <option value="Y" {{ $collateralStatus === 'Y' ? 'selected' : '' }}>Active</option>
                                <option value="N" {{ $collateralStatus === 'N' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reminder Section -->
					<div class="reminder-section">
					    <div class="reminder-item">
					        <span class="reminder-label">Get a reminder 7 days before maturity dates</span>
					        <label class="toggle-switch">
					            <input type="checkbox" name="reminderMaturity" id="reminderMaturity" value="Y" {{ $reminderMaturity === 'Y' ? 'checked' : '' }}>
					            <span class="slider"></span>
					        </label>
					    </div>
					
					    <div class="reminder-item">
					        <span class="reminder-label">Get a reminder for incomplete FD Details</span>
					        <label class="toggle-switch">
					            <input type="checkbox" name="reminderIncomplete" id="reminderIncomplete" value="Y" {{ $reminderIncomplete === 'Y' ? 'checked' : '' }}>
					            <span class="slider"></span>
					        </label>
					    </div>
					</div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Next</button>
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

    {{-- Values for resources/js/pages/fd/create.js --}}
    <script>
        window.pageData = {
            fdCertFileName: @json($fdCertFileName),
        };
    </script>
</body>
</html>
