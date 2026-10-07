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
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/view.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/view.js',
    ])
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
                    <div class="two-col">
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
                        <div class="two-col" style="gap: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 15px;">
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

    {{-- Values for resources/js/pages/fd/view.js --}}
    <script>
        window.pageData = {
            fdUpdateUrl: '{{ route('fd.update', ['id' => $fd->fd_id]) }}',
            applicationUrl: '{{ route('fd.application.view', ['id' => $fd->fd_id]) }}',
        };
    </script>
</body>
</html>
