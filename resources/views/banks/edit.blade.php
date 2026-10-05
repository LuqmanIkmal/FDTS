@php $error = session('error'); @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bank - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
     <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
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

        .user-info { text-align: right; }

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
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .page-content {
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 500px;
        }

        .form-card h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 1.5rem;
            text-align: center;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #003f5c;
            outline: none;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            color: #7f8c8d;
            cursor: not-allowed;
        }

        .error-message {
            background: #ffe5e5;
            color: #c0392b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            display: {{ $error ? 'block' : 'none' }};
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .submit-btn {
            background: #003f5c;
            color: white;
            border: none;
            padding: 15px;
            flex: 1;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover { background: #002d42; }

        .cancel-btn {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 15px;
            flex: 1;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 16px;
        }

        .cancel-btn:hover { background: #7f8c8d; }

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

        .confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .confirmation-modal.active {
            display: flex;
        }
        
        .confirmation-content {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            min-width: 400px;
        }
        
        .confirmation-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .confirmation-message {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .confirmation-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .confirmation-btn {
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        
        .confirmation-btn-no {
            background: #95a5a6;
            color: white;
        }

        .confirmation-btn-no:hover {
            background: #7f8c8d;
        }
        
        .confirmation-btn-yes {
            background: #003f5c;
            color: white;
        }

        .confirmation-btn-yes:hover {
            background: #002d42;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <small style="color: #7f8c8d; font-size: 12px; display: block; margin-top: 5px;">
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

    <script>
        // Phone number validation - digits only
        const bankPhoneInput = document.getElementById('bankPhone');
        
        // Phone validation happens on Update Bank click
        bankPhoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Auto-expand Bank dropdown in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const bankDropdown = document.getElementById('bankDropdown');
            const bankNavItem = document.getElementById('bankNavItem');
            
            if (bankDropdown && bankNavItem) {
                bankDropdown.classList.add('show');
                bankNavItem.classList.add('open');
            }
        });

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            let errEl = document.getElementById(fieldId + 'Error');
            if (!errEl) {
                errEl = document.createElement('div');
                errEl.id = fieldId + 'Error';
                errEl.style.cssText = 'color:#e74c3c; font-size:13px; margin-top:4px;';
                field.parentNode.appendChild(errEl);
            }
            errEl.textContent = message;
            errEl.style.display = 'block';
        }

        function clearFieldErrors() {
            ['bankPhoneError','bankAddressError'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
        }

        function showConfirmation() {
            clearFieldErrors();
            const bankPhone = document.getElementById('bankPhone').value.trim();
            const bankAddress = document.getElementById('bankAddress').value.trim();
            let hasError = false;

            if (!bankPhone) {
                showFieldError('bankPhone', 'This field is required.'); hasError = true;
            } else if (!/^[0-9]+$/.test(bankPhone)) {
                showFieldError('bankPhone', 'Phone number must contain only digits.'); hasError = true;
            }
            if (!bankAddress) {
                showFieldError('bankAddress', 'This field is required.'); hasError = true;
            }
            if (hasError) return;

            document.getElementById('confirmationModal').classList.add('active');
        }

        function closeConfirmation() {
            document.getElementById('confirmationModal').classList.remove('active');
        }

        function confirmUpdate() {
            closeConfirmation();
            document.getElementById('editBankForm').submit();
        }

        // Close modal when clicking outside
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmation();
            }
        });
    </script>
</body>
</html>
