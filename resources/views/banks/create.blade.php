<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Bank - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
     @vite([
         'resources/css/pages/banks/create.css',
         'resources/css/app.css',
         'resources/js/app.js',
         'resources/js/pages/banks/create.js',
     ])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Register Bank'])

         <div class="page-content">
            <div class="form-card">
                <h2>Register Bank</h2>
                
                
				@if (session('error'))
				    <div style="background-color: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fecaca; text-align: center;">
				        <strong>Error:</strong> {{ session('error') }}
				    </div>
				@endif

                <form id="createBankForm" action="{{ route('banks.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="action" value="create">

                    <div class="form-group">
                        <label for="bankName">Bank Name</label>
                        <input type="text" id="bankName" name="bankName" placeholder="Enter bank name" value="{{ old('bankName') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="bankPhone">Bank Phone Number</label>
                        <input type="text" id="bankPhone" name="bankPhone" placeholder="Enter head office contact number" value="{{ old('bankPhone') }}" required maxlength="15" pattern="[0-9\-\+\(\)\s]+" title="Only numbers, spaces, +, -, and () are allowed">
                    </div>

                    <div class="form-group">
                        <label for="bankAddress">Bank Address</label>
                        <textarea id="bankAddress" name="bankAddress" rows="4" placeholder="Enter office branch address" required>{{ old('bankAddress') }}</textarea>
                    </div>

                    <button type="button" class="submit-btn" onclick="showConfirmation()">Register Bank</button>
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
                Are you sure you want to submit this bank information?
            </div>
            <div class="confirmation-buttons">
                <button class="confirmation-btn confirmation-btn-no" onclick="closeConfirmation()">No</button>
                <button class="confirmation-btn confirmation-btn-yes" onclick="confirmCreate()">Yes</button>
            </div>
        </div>
    </div>

</body>
</html>
