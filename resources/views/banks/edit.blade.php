@php $error = session('error'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bank - Fixed Deposit Tracking System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
     @vite([
         'resources/css/pages/banks/edit.css',
         'resources/css/app.css',
         'resources/js/app.js',
         'resources/js/pages/banks/edit.js',
     ])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Edit Bank'])
         <div class="page-content">
            <div class="form-card">
                <h2>Edit Bank Information</h2>

                @if ($error)
                    <div class="error-message">
                        {{ $error }}
                    </div>
                @endif

                <form id="editBankForm" action="{{ route('banks.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="bankId" value="{{ $bank->bank_id }}">

                    <div class="form-group">
                        <label for="bankName">Bank Name</label>
                        <input type="text" id="bankName" name="bankName" 
                               value="{{ $bank->bank_name }}" 
                               disabled>
                        <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 5px;">
                            * Bank name cannot be changed
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="bankPhone">Bank Phone Number</label>
                        <input type="text" id="bankPhone" name="bankPhone" 
                               placeholder="Enter head office contact number" 
                               value="{{ old('bankPhone', $bank->bank_phone) }}" 
                               required maxlength="15" pattern="[0-9\-\+\(\)\s]+" title="Only numbers, spaces, +, -, and () are allowed">
                    </div>

                    <div class="form-group">
                        <label for="bankAddress">Bank Address</label>
                        <textarea id="bankAddress" name="bankAddress" rows="4" 
                                  placeholder="Enter office branch address" 
                                  required>{{ old('bankAddress', $bank->bank_address) }}</textarea>
                    </div>

                    <div class="button-group">
                        <button type="button" class="cancel-btn" onclick="window.location.href='{{ route('banks.list') }}'">Cancel</button>
                        <button type="button" class="submit-btn" onclick="showConfirmation()">Update Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage"></div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal" id="confirmationModal">
        <div class="confirmation-content">
            <div class="confirmation-icon">⚠️</div>
            <div class="confirmation-message">
                Are you sure you want to update this bank information?
            </div>
            <div class="confirmation-buttons">
                <button class="confirmation-btn confirmation-btn-no" onclick="closeConfirmation()">No</button>
                <button class="confirmation-btn confirmation-btn-yes" onclick="confirmUpdate()">Yes</button>
            </div>
        </div>
    </div>

</body>
</html>
