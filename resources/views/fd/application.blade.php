<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fixed Deposit Application Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/fd/application.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/fd/application.js',
    ])
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

    {{-- Values for resources/js/pages/fd/application.js --}}
    <script>
        window.pageData = {
            // FD data (draft from session, or the saved FD in view mode)
            fdData: {{ \Illuminate\Support\Js::from([
                'accountNumber' => $app['accountNumber'],
                'bankName' => $app['bankName'],
                'depositAmount' => $app['depositAmount'],
                'interestRate' => $app['interestRate'],
                'tenure' => $app['tenure'],
            ]) }},
            bankAddress: @json($bankAddress ?? ''),
            reminderMaturity: @json($app['reminderMaturity'] ?? ''),
            reminderIncomplete: @json($app['reminderIncomplete'] ?? ''),
        };
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
