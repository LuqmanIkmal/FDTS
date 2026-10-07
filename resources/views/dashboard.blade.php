<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fixed Deposit Tracking System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/vv-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/pages/dashboard.css',
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/dashboard.js',
    ])
</head>
<body>
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('partials.header', ['pageTitle' => 'Dashboard'])

        <div class="dashboard-content">

            <!-- Filters: scope every figure below -->
            <form class="db-filters" id="filters" onsubmit="return false">
                <div class="db-field">
                    <label for="fPeriod">Placement period</label>
                    <select id="fPeriod"></select>
                </div>
                <div class="db-field">
                    <label for="fBank">Bank</label>
                    <select id="fBank"></select>
                </div>
                <div class="db-field">
                    <label for="fType">FD type</label>
                    <select id="fType"></select>
                </div>
                <div class="db-field">
                    <label for="fStatus">Status</label>
                    <select id="fStatus"></select>
                </div>
                <div class="db-filter-actions">
                    <span class="db-asof" id="asOf"></span>
                    <button type="button" class="db-btn" id="btnReset">Reset filters</button>
                </div>
                <div class="db-chips" id="chips" aria-live="polite"></div>
            </form>

            @if ($dataLoadFailed)
            <div class="db-alert error" role="alert">
                <strong>Could not load dashboard data.</strong>
                The database connection failed. Check that the MySQL database is running, then refresh this page.
            </div>
            @endif

            <div class="db-alert warning" id="overdueAlert" role="status" hidden></div>

            <!-- KPI tiles -->
            <section class="db-kpis" aria-label="Key figures">
                <div class="db-kpi hero">
                    <div class="db-kpi-label">Active principal</div>
                    <div class="db-kpi-value" id="kActivePrincipal">-</div>
                    <div class="db-kpi-sub" id="kActivePrincipalSub"></div>
                </div>
                <div class="db-kpi">
                    <div class="db-kpi-label">Expected interest (active FDs)</div>
                    <div class="db-kpi-value" id="kInterest">-</div>
                    <div class="db-kpi-sub" id="kInterestSub"></div>
                </div>
                <div class="db-kpi">
                    <div class="db-kpi-label">Weighted average rate</div>
                    <div class="db-kpi-value" id="kRate">-</div>
                    <div class="db-kpi-sub" id="kRateSub"></div>
                </div>
                <div class="db-kpi">
                    <div class="db-kpi-label">Maturing in next 90 days</div>
                    <div class="db-kpi-value" id="kMaturing">-</div>
                    <div class="db-kpi-sub" id="kMaturingSub"></div>
                </div>
                <div class="db-kpi">
                    <div class="db-kpi-label">Total placed</div>
                    <div class="db-kpi-value" id="kPlaced">-</div>
                    <div class="db-kpi-sub" id="kPlacedSub"></div>
                </div>
                <div class="db-kpi">
                    <div class="db-kpi-label">Total withdrawn</div>
                    <div class="db-kpi-value" id="kWithdrawn">-</div>
                    <div class="db-kpi-sub" id="kWithdrawnSub"></div>
                </div>
            </section>

            <!-- Row 1: placements + bank concentration -->
            <div class="db-grid">
                <section class="db-card">
                    <div class="db-card-head">
                        <div>
                            <h2>New placements</h2>
                            <p class="db-card-desc" id="placementsDesc">Deposit amount placed, by start date</p>
                        </div>
                        <div class="db-legend" id="placementsLegend"></div>
                    </div>
                    <div class="db-chart">
                        <canvas id="placementsChart" role="img" aria-label="Column chart of deposit amounts placed over time, split by FD type"></canvas>
                        <div class="db-empty" id="placementsEmpty" hidden>No placements match these filters.</div>
                    </div>
                </section>

                <section class="db-card">
                    <div class="db-card-head">
                        <div>
                            <h2>Active principal by bank</h2>
                            <p class="db-card-desc">Share of money currently placed. Click a bank to filter.</p>
                        </div>
                    </div>
                    <div class="db-chart" id="bankChartBox">
                        <canvas id="bankChart" role="img" aria-label="Bar chart of active principal by bank"></canvas>
                        <div class="db-empty" id="bankEmpty" hidden>No active FDs match these filters.</div>
                    </div>
                    <p class="db-note" id="concentrationNote"></p>
                </section>
            </div

            <!-- Row 2: maturity ladder + status mix -->
            <div class="db-grid">
                <section class="db-card">
                    <div class="db-card-head">
                        <div>
                            <h2>Maturity ladder: next 12 months</h2>
                            <p class="db-card-desc">Cash returning (principal + interest) from active FDs. Click a month to list those FDs.</p>
                        </div>
                        <div class="db-legend" id="ladderLegend"></div>
                    </div>
                    <div class="db-chart">
                        <canvas id="ladderChart" role="img" aria-label="Column chart of maturity value by month for the next 12 months, split by FD type"></canvas>
                        <div class="db-empty" id="ladderEmpty" hidden>No active FDs mature in the next 12 months.</div>
                    </div>
                </section>

                <section class="db-card">
                    <div class="db-card-head">
                        <div>
                            <h2>Portfolio by status</h2>
                            <p class="db-card-desc">Number of FDs and amount placed. Click a status to filter.</p>
                        </div>
                    </div>
                    <div class="db-status-list" id="statusList"></div>
                </section>
            </div>

            <!-- Bank comparison -->
            <section class="db-card">
                <div class="db-card-head">
                    <div>
                        <h2>Bank comparison</h2>
                        <p class="db-card-desc">Rates and exposure per bank for the current filters. Click a column to sort, or a row to filter.</p>
                    </div>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table" id="bankTable">
                        <thead>
                            <tr>
                                <th data-key="bank"><button type="button" class="db-sort">Bank <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="count"><button type="button" class="db-sort">FDs <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="active"><button type="button" class="db-sort">Active <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="principal"><button type="button" class="db-sort">Active principal <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="share"><button type="button" class="db-sort">Share <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="rate"><button type="button" class="db-sort">Avg rate <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="interest"><button type="button" class="db-sort">Expected interest <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="next"><button type="button" class="db-sort">Next maturity <span class="arrow">&#9650;&#9660;</span></button></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>

            <!-- FD list -->
            <section class="db-card">
                <div class="db-card-head">
                    <div>
                        <h2>Fixed deposits in view</h2>
                        <p class="db-card-desc" id="fdTableDesc"></p>
                    </div>
                    <button type="button" class="db-btn primary" id="btnExport">Export CSV</button>
                </div>
                <div class="db-table-wrap">
                    <table class="db-table" id="fdTable">
                        <thead>
                            <tr>
                                <th data-key="id"><button type="button" class="db-sort">FD <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th data-key="bank"><button type="button" class="db-sort">Bank <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th data-key="type"><button type="button" class="db-sort">Type <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th data-key="status"><button type="button" class="db-sort">Status <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="principal"><button type="button" class="db-sort">Principal <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="rate"><button type="button" class="db-sort">Rate <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="tenure"><button type="button" class="db-sort">Tenure <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th data-key="start"><button type="button" class="db-sort">Start <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th data-key="maturity"><button type="button" class="db-sort">Maturity <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="daysLeft"><button type="button" class="db-sort">Days left <span class="arrow">&#9650;&#9660;</span></button></th>
                                <th class="num" data-key="maturityValue"><button type="button" class="db-sort">Maturity value <span class="arrow">&#9650;&#9660;</span></button></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="db-table-foot">
                    <span id="fdTableCount"></span>
                    <button type="button" class="db-btn" id="btnShowAll" hidden>Show all</button>
                </div>
            </section>

        </div>
    </div>

    <!-- FD data for the charts (JSON, built by DashboardController) -->
    <script type="application/json" id="fdData">{!! json_encode($fdData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>

    {{-- Values for resources/js/pages/dashboard.js --}}
    <script>
        window.pageData = {
            fdViewUrl: '{{ route('fd.view') }}',
        };
    </script>
</body>
</html>
