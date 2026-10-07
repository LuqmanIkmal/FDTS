<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Lists - Fixed Deposit Tracking System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/banks/index.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/banks/index.js',
    ])
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @include('partials.header', ['pageTitle' => 'Bank List'])

        <div class="page-content">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bank ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bankTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage"></div>

    {{-- Values for resources/js/pages/banks/index.js --}}
    <script>
        window.pageData = {
            banks: @json($banks),
            bankListUrl: '{{ route('banks.list') }}',
            bankEditUrl: '{{ route('banks.edit') }}',
            updateIcon: '{{ asset('images/icons/update-icon.png') }}',
        };
    </script>
</body>
</html>
