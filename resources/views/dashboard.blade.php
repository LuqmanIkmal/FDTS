<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fixed Deposit Tracking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --page:           #f5f5f5;
            --surface:        #ffffff;
            --text-primary:   #1f2d3a;
            --text-secondary: #52514e;
            --text-muted:     #898781;
            --gridline:       #eceae4;
            --baseline:       #c3c2b7;
            --hairline:       #e3e3e0;
            --brand:          #0c373f;
            --brand-accent:   #ff9a5a;

            /* Chart colours (validated categorical slots 1-2) */
            --series-1:       #2a78d6;   /* Free FD */
            --series-2:       #eb6834;   /* Pledge FD */
            --series-other:   #898781;
            --single-series:  #2f6f7a;   /* one-series charts (bank principal, status mix) */

            /* Status colours: always shown with an icon + label */
            --status-good:     #0ca30c;
            --status-warning:  #b77900;
            --status-critical: #d03b3b;
            --delta-up:        #006300;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--page);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Header (markup comes from partials/header.blade.php) */
        .header {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .user-info {
            text-align: right;
        }

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
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 24px 40px 48px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Filter bar: one row above everything it scopes */
        .db-filters {
            position: sticky;
            top: 0;
            z-index: 5;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 12px 16px;
            padding: 14px 18px;
            background: var(--surface);
            border: 1px solid var(--hairline);
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .db-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .db-field label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .db-field select {
            min-width: 150px;
            padding: 8px 12px;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            background: white;
            font: inherit;
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
        }

        .db-field select:focus-visible,
        .db-btn:focus-visible,
        .db-chip:focus-visible,
        .db-sort:focus-visible,
        .db-status-row:focus-visible,
        .db-bank-row:focus-visible {
            outline: 2px solid var(--series-1);
            outline-offset: 2px;
        }

        .db-filter-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .db-btn {
            padding: 8px 14px;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            background: white;
            font: inherit;
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
        }

        .db-btn:hover {
            background: #f3f3f1;
        }

        .db-btn.primary {
            background: var(--brand);
            border-color: var(--brand);
            color: white;
        }

        .db-btn.primary:hover {
            background: #134a54;
        }

        .db-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
        }

        .db-chips:empty {
            display: none;
        }

        .db-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border: 1px solid #cfd8da;
            border-radius: 999px;
            background: #eef4f5;
            font: inherit;
            font-size: 13px;
            color: var(--brand);
            cursor: pointer;
        }

        .db-chip span {
            font-weight: 700;
        }

        .db-asof {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Alerts */
        .db-alert {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.5;
            border: 1px solid;
        }

        .db-alert.error {
            background: #fdf0f0;
            border-color: #f1c4c4;
            color: #8a1f1f;
        }

        .db-alert.warning {
            background: #fff8e8;
            border-color: #f2dca6;
            color: #6b4a00;
        }

        .db-alert[hidden] {
            display: none;
        }

        /* KPI tiles */
        .db-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        @media (max-width: 1100px) {
            .db-kpis {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .db-kpis {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .db-kpi {
            background: var(--surface);
            border: 1px solid var(--hairline);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .db-kpi.hero {
            background: var(--brand);
            border-color: var(--brand);
            color: white;
        }

        .db-kpi-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .db-kpi.hero .db-kpi-label,
        .db-kpi.hero .db-kpi-sub {
            color: #cfe0e3;
        }

        .db-kpi-value {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .db-kpi-sub {
            font-size: 12.5px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .db-delta {
            font-weight: 600;
        }

        .db-delta.up {
            color: var(--delta-up);
        }

        .db-delta.down {
            color: var(--status-critical);
        }

        /* Cards and chart grid */
        .db-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
            gap: 20px;
        }

        .db-card {
            background: var(--surface);
            border: 1px solid var(--hairline);
            border-radius: 14px;
            padding: 20px 22px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .db-card-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px 16px;
        }

        .db-card h2 {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .db-card-desc {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .db-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        .db-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .db-swatch {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .db-chart {
            position: relative;
            height: 290px;
        }

        .db-empty {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: var(--text-muted);
            background: var(--surface);
        }

        .db-empty[hidden] {
            display: none;
        }

        .db-note {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* Status mix list */
        .db-status-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .db-status-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 4px 12px;
            padding: 8px 10px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            font: inherit;
            text-align: left;
            color: inherit;
            cursor: pointer;
        }

        .db-status-row:hover {
            background: #f4f5f3;
        }

        .db-status-row.selected {
            background: #eef4f5;
        }

        .db-status-name {
            font-size: 14px;
            font-weight: 600;
        }

        .db-status-figs {
            font-size: 13px;
            color: var(--text-secondary);
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .db-status-bar {
            grid-column: 1 / -1;
            height: 6px;
            border-radius: 3px;
            background: var(--gridline);
            overflow: hidden;
        }

        .db-status-bar > div {
            height: 100%;
            border-radius: 3px;
            background: var(--single-series);
        }

        /* Tables */
        .db-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--hairline);
            border-radius: 10px;
        }

        .db-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .db-table th,
        .db-table td {
            padding: 10px 12px;
            text-align: left;
            white-space: nowrap;
            border-bottom: 1px solid var(--gridline);
        }

        .db-table th {
            background: #f7f7f5;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            position: sticky;
            top: 0;
        }

        .db-table td.num,
        .db-table th.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .db-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .db-table tbody tr:hover {
            background: #fafaf8;
        }

        .db-bank-row {
            cursor: pointer;
        }

        .db-bank-row.selected {
            background: #eef4f5;
        }

        .db-sort {
            border: 0;
            background: none;
            font: inherit;
            color: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .db-sort .arrow {
            font-size: 10px;
            opacity: 0.35;
        }

        th[aria-sort] .db-sort .arrow {
            opacity: 1;
        }

        .db-link {
            color: var(--series-1);
            font-weight: 600;
            text-decoration: none;
        }

        .db-link:hover {
            text-decoration: underline;
        }

        .db-sub {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
        }

        .db-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            background: #f0efec;
            color: var(--text-secondary);
        }

        .db-type-key {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .db-days {
            font-weight: 600;
        }

        .db-days.critical {
            color: var(--status-critical);
        }

        .db-days.warning {
            color: var(--status-warning);
        }

        .db-table-foot {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .db-visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
        }

        @media (max-width: 1200px) {
            .db-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 200px;
            }

            .header {
                padding: 15px 20px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .dashboard-content {
                padding: 16px;
            }

            .db-filter-actions {
                margin-left: 0;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        'use strict';

        var RAW = JSON.parse(document.getElementById('fdData').textContent);

        // ---------- Constants & helpers ----------
        var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var DAY_MS = 86400000;
        var TODAY = new Date();
        TODAY.setHours(0, 0, 0, 0);
        var PAGE_SIZE = 20;

        var rootCss = getComputedStyle(document.documentElement);
        function cssVar(name) { return rootCss.getPropertyValue(name).trim(); }

        var TYPE_ORDER = ['FREEFD', 'PLEDGEFD', 'OTHER'];
        var TYPE_LABEL = { FREEFD: 'Free FD', PLEDGEFD: 'Pledge FD', OTHER: 'Other' };
        var TYPE_COLOR = {
            FREEFD: cssVar('--series-1'),
            PLEDGEFD: cssVar('--series-2'),
            OTHER: cssVar('--series-other')
        };
        var STATUS_ORDER = ['ONGOING', 'PENDING', 'MATURED', 'REINVESTED', 'WITHDRAWN'];

        var moneyFmt = new Intl.NumberFormat('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        var intFmt = new Intl.NumberFormat('en-MY');

        function money(v) { return 'RM ' + moneyFmt.format(v || 0); }

        function compact(v) {
            var a = Math.abs(v || 0);
            if (a >= 1e9) return 'RM ' + trim1(v / 1e9) + 'B';
            if (a >= 1e6) return 'RM ' + trim1(v / 1e6) + 'M';
            if (a >= 1e3) return 'RM ' + trim1(v / 1e3) + 'K';
            return 'RM ' + Math.round(v || 0);
        }

        function trim1(x) { return (Math.round(x * 10) / 10).toString(); }

        function pct(v, digits) { return (v * 100).toFixed(digits == null ? 1 : digits) + '%'; }

        function parseDate(s) {
            if (!s) return null;
            var p = s.split('-');
            return new Date(+p[0], +p[1] - 1, +p[2]);
        }

        function fmtDate(d) {
            return d ? d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear() : '-';
        }

        function monthKey(d) {
            return d.getFullYear() + '-' + (d.getMonth() < 9 ? '0' : '') + (d.getMonth() + 1);
        }

        function monthLabel(key) {
            var p = key.split('-');
            return MONTHS[+p[1] - 1] + ' ' + p[0];
        }

        function addMonths(d, n) { return new Date(d.getFullYear(), d.getMonth() + n, 1); }

        function titleCase(s) {
            return s.charAt(0) + s.slice(1).toLowerCase();
        }

        function el(tag, className, text) {
            var e = document.createElement(tag);
            if (className) e.className = className;
            if (text != null) e.textContent = text;
            return e;
        }

        function sum(rows, f) {
            var t = 0;
            for (var i = 0; i < rows.length; i++) t += f(rows[i]) || 0;
            return t;
        }

        // ---------- Normalise records ----------
        // Interest follows the system's formula: principal x rate% x months / 1200
        var FDS = RAW.map(function (r) {
            var deposit = r.deposit || 0;
            var principal = r.remaining != null ? r.remaining : deposit;
            var rate = r.rate || 0;
            var tenure = r.tenure || 0;
            var interest = principal * rate * tenure / 1200;
            var type = (r.type || '').toUpperCase();
            var maturity = parseDate(r.maturity);
            var status = (r.status || 'UNKNOWN').toUpperCase();
            return {
                id: r.id,
                acc: r.acc || '',
                bank: r.bank || 'Unassigned',
                type: TYPE_LABEL[type] && type !== 'OTHER' ? type : 'OTHER',
                status: status,
                deposit: deposit,
                principal: principal,
                rate: rate,
                tenure: tenure,
                interest: interest,
                maturityValue: principal + interest,
                withdrawn: r.withdrawn || 0,
                start: parseDate(r.start),
                maturity: maturity,
                // Only meaningful while the FD is still running
                daysLeft: maturity && status === 'ONGOING' ? Math.round((maturity - TODAY) / DAY_MS) : null
            };
        });

        function isActive(f) { return f.status === 'ONGOING'; }

        var typesPresent = TYPE_ORDER.filter(function (t) {
            return FDS.some(function (f) { return f.type === t; });
        });
        if (typesPresent.length === 0) typesPresent = ['FREEFD', 'PLEDGEFD'];

        // ---------- Filter state ----------
        var DEFAULT_STATE = { period: 'ALL', bank: 'ALL', type: 'ALL', status: 'ALL', matMonth: null };
        var state = Object.assign({}, DEFAULT_STATE);

        var fPeriod = document.getElementById('fPeriod');
        var fBank = document.getElementById('fBank');
        var fType = document.getElementById('fType');
        var fStatus = document.getElementById('fStatus');

        function addOption(select, value, label) {
            var o = document.createElement('option');
            o.value = value;
            o.textContent = label;
            select.appendChild(o);
        }

        (function buildFilterOptions() {
            addOption(fPeriod, 'ALL', 'All time');
            addOption(fPeriod, 'L12', 'Last 12 months');
            var years = {};
            FDS.forEach(function (f) { if (f.start) years[f.start.getFullYear()] = true; });
            Object.keys(years).map(Number).sort(function (a, b) { return b - a; })
                .forEach(function (y) { addOption(fPeriod, 'Y' + y, String(y)); });

            addOption(fBank, 'ALL', 'All banks');
            var banks = {};
            FDS.forEach(function (f) { banks[f.bank] = true; });
            Object.keys(banks).sort(function (a, b) { return a.localeCompare(b); })
                .forEach(function (b) { addOption(fBank, b, b); });

            addOption(fType, 'ALL', 'All types');
            typesPresent.forEach(function (t) { addOption(fType, t, TYPE_LABEL[t]); });

            addOption(fStatus, 'ALL', 'All statuses');
            statusesPresent().forEach(function (s) { addOption(fStatus, s, titleCase(s)); });
        })();

        function statusesPresent() {
            var seen = {};
            FDS.forEach(function (f) { seen[f.status] = true; });
            return Object.keys(seen).sort(function (a, b) {
                var ia = STATUS_ORDER.indexOf(a), ib = STATUS_ORDER.indexOf(b);
                if (ia === -1) ia = 99;
                if (ib === -1) ib = 99;
                return ia - ib || a.localeCompare(b);
            });
        }

        function periodRange(period) {
            if (period === 'L12') {
                return { from: addMonths(TODAY, -11), to: addMonths(TODAY, 1) };
            }
            if (period.charAt(0) === 'Y') {
                var y = +period.slice(1);
                return { from: new Date(y, 0, 1), to: new Date(y + 1, 0, 1) };
            }
            return null;
        }

        function inPeriod(f, period) {
            var range = periodRange(period);
            if (!range) return true;
            return !!f.start && f.start >= range.from && f.start < range.to;
        }

        // Rows matching every filter except the ones listed in `ignore`.
        // Charts that drive a filter ignore their own filter so the reader
        // still sees the other options, with the selected one highlighted.
        function filtered(ignore, periodOverride) {
            ignore = ignore || {};
            var period = periodOverride || state.period;
            return FDS.filter(function (f) {
                if (!ignore.period && !inPeriod(f, period)) return false;
                if (!ignore.bank && state.bank !== 'ALL' && f.bank !== state.bank) return false;
                if (!ignore.type && state.type !== 'ALL' && f.type !== state.type) return false;
                if (!ignore.status && state.status !== 'ALL' && f.status !== state.status) return false;
                if (!ignore.matMonth && state.matMonth && !(f.maturity && monthKey(f.maturity) === state.matMonth)) return false;
                return true;
            });
        }

        function setState(patch) {
            Object.assign(state, patch);
            fPeriod.value = state.period;
            fBank.value = state.bank;
            fType.value = state.type;
            fStatus.value = state.status;
            fdPage = PAGE_SIZE;
            render();
        }

        fPeriod.addEventListener('change', function () { setState({ period: fPeriod.value }); });
        fBank.addEventListener('change', function () { setState({ bank: fBank.value }); });
        fType.addEventListener('change', function () { setState({ type: fType.value }); });
        fStatus.addEventListener('change', function () { setState({ status: fStatus.value }); });
        document.getElementById('btnReset').addEventListener('click', function () {
            setState(Object.assign({}, DEFAULT_STATE));
        });

        function renderChips() {
            var box = document.getElementById('chips');
            box.textContent = '';
            if (!state.matMonth) return;
            var chip = el('button', 'db-chip');
            chip.type = 'button';
            chip.setAttribute('aria-label', 'Remove filter: maturing in ' + monthLabel(state.matMonth));
            chip.appendChild(document.createTextNode('Maturing in ' + monthLabel(state.matMonth) + ' '));
            chip.appendChild(el('span', null, '×'));
            chip.addEventListener('click', function () { setState({ matMonth: null }); });
            box.appendChild(chip);
        }

        // ---------- Chart.js defaults ----------
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = cssVar('--text-muted');
        Chart.defaults.animation.duration = 350;

        var TOOLTIP = {
            backgroundColor: '#ffffff',
            borderColor: cssVar('--hairline'),
            borderWidth: 1,
            titleColor: cssVar('--text-secondary'),
            titleFont: { weight: '500', size: 12 },
            bodyColor: cssVar('--text-primary'),
            bodyFont: { weight: '600', size: 13 },
            footerColor: cssVar('--text-primary'),
            footerFont: { weight: '700', size: 13 },
            padding: 10,
            cornerRadius: 8,
            boxWidth: 14,
            boxHeight: 2,
            usePointStyle: false,
            caretSize: 5
        };

        function lighten(hex, amount) {
            var n = parseInt(hex.slice(1), 16);
            var r = n >> 16, g = (n >> 8) & 255, b = n & 255;
            r = Math.round(r + (255 - r) * amount);
            g = Math.round(g + (255 - g) * amount);
            b = Math.round(b + (255 - b) * amount);
            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }

        // Total value on each column cap, only when the columns are wide enough
        var stackTotalsPlugin = {
            id: 'stackTotals',
            afterDatasetsDraw: function (chart) {
                var n = chart.data.labels.length;
                if (!n || chart.chartArea.width / n < 46) return;
                var ctx = chart.ctx;
                ctx.save();
                ctx.font = "500 11px 'Inter', system-ui, sans-serif";
                ctx.fillStyle = cssVar('--text-secondary');
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                for (var i = 0; i < n; i++) {
                    var total = 0, topY = null, x = null;
                    chart.data.datasets.forEach(function (ds, di) {
                        var meta = chart.getDatasetMeta(di);
                        if (meta.hidden) return;
                        var v = ds.data[i] || 0;
                        total += v;
                        var bar = meta.data[i];
                        if (bar && v > 0) {
                            topY = topY == null ? bar.y : Math.min(topY, bar.y);
                            x = bar.x;
                        }
                    });
                    if (total > 0 && topY != null) ctx.fillText(compact(total), x, topY - 4);
                }
                ctx.restore();
            }
        };

        // Value label at the tip of each horizontal bar
        var barEndLabelsPlugin = {
            id: 'barEndLabels',
            afterDatasetsDraw: function (chart, args, opts) {
                if (!opts || !opts.labels) return;
                var ctx = chart.ctx;
                var meta = chart.getDatasetMeta(0);
                ctx.save();
                ctx.font = "500 12px 'Inter', system-ui, sans-serif";
                ctx.fillStyle = cssVar('--text-secondary');
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                meta.data.forEach(function (bar, i) {
                    if (opts.labels[i]) ctx.fillText(opts.labels[i], bar.x + 6, bar.y);
                });
                ctx.restore();
            }
        };

        function buildLegend(id) {
            var box = document.getElementById(id);
            box.textContent = '';
            typesPresent.forEach(function (t) {
                var item = el('span', 'db-legend-item');
                var sw = el('span', 'db-swatch');
                sw.style.background = TYPE_COLOR[t];
                item.appendChild(sw);
                item.appendChild(document.createTextNode(TYPE_LABEL[t]));
                box.appendChild(item);
            });
        }
        buildLegend('placementsLegend');
        buildLegend('ladderLegend');

        function stackedColumnChart(canvasId, onPick) {
            return new Chart(document.getElementById(canvasId), {
                type: 'bar',
                data: { labels: [], datasets: [] },
                plugins: [stackTotalsPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 18 } },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: Object.assign({}, TOOLTIP, {
                            callbacks: {
                                label: function (c) { return ' ' + money(c.parsed.y) + '  ' + c.dataset.label; },
                                footer: function (items) {
                                    var t = 0;
                                    items.forEach(function (i) { t += i.parsed.y || 0; });
                                    return 'Total ' + money(t);
                                }
                            }
                        })
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            border: { color: cssVar('--baseline') },
                            ticks: { maxRotation: 0, autoSkip: true }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: cssVar('--gridline') },
                            border: { display: false },
                            ticks: { maxTicksLimit: 6, callback: function (v) { return compact(v); } }
                        }
                    },
                    onClick: onPick ? function (evt, els) { if (els.length) onPick(els[0].index); } : undefined,
                    onHover: onPick ? function (evt, els) {
                        evt.native.target.style.cursor = els.length ? 'pointer' : 'default';
                    } : undefined
                }
            });
        }

        function typeDatasets(keys, rows, keyOf, valueOf, highlightKey) {
            return typesPresent.map(function (t) {
                var totals = {};
                rows.forEach(function (f) {
                    if (f.type !== t) return;
                    var k = keyOf(f);
                    if (k == null) return;
                    totals[k] = (totals[k] || 0) + valueOf(f);
                });
                var base = TYPE_COLOR[t];
                return {
                    label: TYPE_LABEL[t],
                    data: keys.map(function (k) { return totals[k] || 0; }),
                    backgroundColor: keys.map(function (k) {
                        return !highlightKey || k === highlightKey ? base : lighten(base, 0.7);
                    }),
                    hoverBackgroundColor: lighten(base, 0.2),
                    borderColor: '#ffffff',
                    borderWidth: { top: 2 },
                    borderSkipped: 'bottom',
                    borderRadius: 4,
                    maxBarThickness: 44
                };
            });
        }

        // ---------- Placements ----------
        var placementsChart = stackedColumnChart('placementsChart');

        function renderPlacements(rows) {
            var keys = [], labels = [], keyOf, desc;
            var range = periodRange(state.period);
            if (range) {
                for (var d = new Date(range.from); d < range.to; d = addMonths(d, 1)) {
                    keys.push(monthKey(d));
                    labels.push(state.period === 'L12' ? MONTHS[d.getMonth()] + ' ' + String(d.getFullYear()).slice(2) : MONTHS[d.getMonth()]);
                }
                keyOf = function (f) { return f.start ? monthKey(f.start) : null; };
                desc = 'Deposit amount placed per month, by start date';
            } else {
                var ys = rows.filter(function (f) { return f.start; }).map(function (f) { return f.start.getFullYear(); });
                if (ys.length) {
                    var minY = Math.min.apply(null, ys), maxY = Math.max.apply(null, ys);
                    for (var y = minY; y <= maxY; y++) { keys.push(String(y)); labels.push(String(y)); }
                }
                keyOf = function (f) { return f.start ? String(f.start.getFullYear()) : null; };
                desc = 'Deposit amount placed per year, by start date';
            }
            document.getElementById('placementsDesc').textContent = desc;
            placementsChart.data.labels = labels;
            placementsChart.data.datasets = typeDatasets(keys, rows, keyOf, function (f) { return f.deposit; });
            placementsChart.update();
            document.getElementById('placementsEmpty').hidden = sum(rows, function (f) { return f.start ? f.deposit : 0; }) > 0;
        }

        // ---------- Active principal by bank ----------
        var bankKeys = [];
        var bankChart = new Chart(document.getElementById('bankChart'), {
            type: 'bar',
            data: { labels: [], datasets: [] },
            plugins: [barEndLabelsPlugin],
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { right: 118 } },
                plugins: {
                    legend: { display: false },
                    barEndLabels: { labels: [] },
                    tooltip: Object.assign({}, TOOLTIP, {
                        displayColors: false,
                        callbacks: {
                            label: function (c) { return money(c.parsed.x); },
                            afterLabel: function (c) { return bankTooltipExtra[c.dataIndex] || ''; }
                        }
                    })
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: cssVar('--gridline') },
                        border: { display: false },
                        ticks: { maxTicksLimit: 4, callback: function (v) { return compact(v); } }
                    },
                    y: {
                        grid: { display: false },
                        border: { color: cssVar('--baseline') },
                        ticks: {
                            color: cssVar('--text-secondary'),
                            callback: function (v, i) {
                                var s = this.getLabelForValue(v);
                                return s.length > 18 ? s.slice(0, 17) + '…' : s;
                            }
                        }
                    }
                },
                onClick: function (evt, els) {
                    if (!els.length) return;
                    var b = bankKeys[els[0].index];
                    setState({ bank: state.bank === b ? 'ALL' : b });
                },
                onHover: function (evt, els) {
                    evt.native.target.style.cursor = els.length ? 'pointer' : 'default';
                }
            }
        });
        var bankTooltipExtra = [];

        function renderBankChart() {
            var rows = filtered({ bank: true }).filter(isActive);
            var totals = {}, counts = {};
            rows.forEach(function (f) {
                totals[f.bank] = (totals[f.bank] || 0) + f.principal;
                counts[f.bank] = (counts[f.bank] || 0) + 1;
            });
            var grand = sum(rows, function (f) { return f.principal; });
            bankKeys = Object.keys(totals).filter(function (b) { return totals[b] > 0; })
                .sort(function (a, b) { return totals[b] - totals[a]; });

            var base = cssVar('--single-series');
            bankChart.data.labels = bankKeys.slice();
            bankChart.data.datasets = [{
                label: 'Active principal',
                data: bankKeys.map(function (b) { return totals[b]; }),
                backgroundColor: bankKeys.map(function (b) {
                    return state.bank === 'ALL' || state.bank === b ? base : lighten(base, 0.7);
                }),
                hoverBackgroundColor: lighten(base, 0.2),
                borderRadius: 4,
                borderSkipped: 'start',
                maxBarThickness: 26
            }];
            bankChart.options.plugins.barEndLabels.labels = bankKeys.map(function (b) {
                return compact(totals[b]) + ' · ' + pct(grand ? totals[b] / grand : 0, 0);
            });
            bankTooltipExtra = bankKeys.map(function (b) {
                return counts[b] + ' active FD' + (counts[b] === 1 ? '' : 's') + ', ' + pct(grand ? totals[b] / grand : 0) + ' of active principal';
            });

            document.getElementById('bankChartBox').style.height = Math.max(200, bankKeys.length * 38 + 50) + 'px';
            bankChart.resize();
            bankChart.update();
            document.getElementById('bankEmpty').hidden = bankKeys.length > 0;

            var note = document.getElementById('concentrationNote');
            note.textContent = '';
            if (bankKeys.length && grand > 0) {
                var topShare = totals[bankKeys[0]] / grand;
                if (bankKeys.length > 1 && topShare >= 0.4) {
                    note.appendChild(el('strong', null, '⚠ Concentration: '));
                    note.appendChild(document.createTextNode(bankKeys[0] + ' holds ' + pct(topShare, 0) + ' of active principal.'));
                } else if (bankKeys.length > 1) {
                    note.textContent = 'Largest exposure: ' + bankKeys[0] + ' at ' + pct(topShare, 0) + ' of active principal.';
                }
            }
        }

        // ---------- Maturity ladder ----------
        var ladderKeys = [];
        var ladderChart = stackedColumnChart('ladderChart', function (i) {
            var k = ladderKeys[i];
            setState({ matMonth: state.matMonth === k ? null : k });
        });

        function renderLadder() {
            var rows = filtered({ matMonth: true }).filter(isActive);
            ladderKeys = [];
            var labels = [];
            for (var i = 0; i < 12; i++) {
                var d = addMonths(TODAY, i);
                ladderKeys.push(monthKey(d));
                labels.push(MONTHS[d.getMonth()] + ' ' + String(d.getFullYear()).slice(2));
            }
            var inWindow = rows.filter(function (f) {
                return f.maturity && ladderKeys.indexOf(monthKey(f.maturity)) !== -1;
            });
            ladderChart.data.labels = labels;
            ladderChart.data.datasets = typeDatasets(ladderKeys, inWindow,
                function (f) { return monthKey(f.maturity); },
                function (f) { return f.maturityValue; },
                state.matMonth);
            ladderChart.update();
            document.getElementById('ladderEmpty').hidden = inWindow.length > 0;
        }

        // ---------- Status mix ----------
        function renderStatusList() {
            var rows = filtered({ status: true });
            var box = document.getElementById('statusList');
            box.textContent = '';
            var total = sum(rows, function (f) { return f.deposit; });
            var groups = {};
            rows.forEach(function (f) {
                var g = groups[f.status] || (groups[f.status] = { count: 0, amount: 0 });
                g.count++;
                g.amount += f.deposit;
            });
            var list = statusesPresent().filter(function (s) { return groups[s]; });
            if (!list.length) {
                box.appendChild(el('p', 'db-note', 'No FDs match these filters.'));
                return;
            }
            list.forEach(function (s) {
                var g = groups[s];
                var row = el('button', 'db-status-row' + (state.status === s ? ' selected' : ''));
                row.type = 'button';
                row.setAttribute('aria-pressed', state.status === s ? 'true' : 'false');
                row.appendChild(el('span', 'db-status-name', titleCase(s)));
                row.appendChild(el('span', 'db-status-figs', intFmt.format(g.count) + ' FD' + (g.count === 1 ? '' : 's') + ' · ' + compact(g.amount)));
                var bar = el('span', 'db-status-bar');
                var fill = el('div');
                fill.style.width = (total ? (g.amount / total) * 100 : 0) + '%';
                bar.appendChild(fill);
                row.appendChild(bar);
                row.addEventListener('click', function () { setState({ status: state.status === s ? 'ALL' : s }); });
                box.appendChild(row);
            });
        }

        // ---------- KPIs ----------
        function setKpi(id, value, sub) {
            document.getElementById(id).textContent = value;
            var subEl = document.getElementById(id + 'Sub');
            subEl.textContent = '';
            if (sub == null) return;
            if (typeof sub === 'string') subEl.textContent = sub;
            else subEl.appendChild(sub);
        }

        function previousPeriod(period) {
            if (period === 'L12') return 'P12';
            if (period.charAt(0) === 'Y') return 'Y' + (+period.slice(1) - 1);
            return null;
        }

        function renderKpis(rows) {
            var active = rows.filter(isActive);
            var activePrincipal = sum(active, function (f) { return f.principal; });
            setKpi('kActivePrincipal', money(activePrincipal),
                intFmt.format(active.length) + ' active of ' + intFmt.format(rows.length) + ' FDs in view');

            var interest = sum(active, function (f) { return f.interest; });
            setKpi('kInterest', money(interest),
                activePrincipal ? 'Return of ' + pct(interest / activePrincipal, 2) + ' on active principal over the full terms' : 'No active FDs');

            if (activePrincipal > 0) {
                var wRate = sum(active, function (f) { return f.principal * f.rate; }) / activePrincipal;
                var rates = active.map(function (f) { return f.rate; });
                setKpi('kRate', wRate.toFixed(2) + '% p.a.',
                    'Range ' + Math.min.apply(null, rates).toFixed(2) + '% – ' + Math.max.apply(null, rates).toFixed(2) + '%, weighted by principal');
            } else {
                setKpi('kRate', '-', 'No active FDs');
            }

            var soon = active.filter(function (f) { return f.daysLeft != null && f.daysLeft >= 0 && f.daysLeft <= 90; })
                .sort(function (a, b) { return a.daysLeft - b.daysLeft; });
            setKpi('kMaturing', money(sum(soon, function (f) { return f.maturityValue; })),
                soon.length
                    ? intFmt.format(soon.length) + ' FD' + (soon.length === 1 ? '' : 's') + ' · next on ' + fmtDate(soon[0].maturity) + ' (' + soon[0].bank + ')'
                    : 'Nothing matures in the next 90 days');

            var placed = sum(rows, function (f) { return f.deposit; });
            var placedSub = document.createElement('span');
            placedSub.appendChild(document.createTextNode(intFmt.format(rows.length) + ' FDs'));
            var prev = previousPeriod(state.period);
            if (prev) {
                var prevRows;
                if (prev === 'P12') {
                    var from = addMonths(TODAY, -23), to = addMonths(TODAY, -11);
                    prevRows = filtered({ period: true }).filter(function (f) { return f.start && f.start >= from && f.start < to; });
                } else {
                    prevRows = filtered(null, prev);
                }
                var prevPlaced = sum(prevRows, function (f) { return f.deposit; });
                var prevName = prev === 'P12' ? 'previous 12 months' : prev.slice(1);
                placedSub.appendChild(document.createTextNode(' · '));
                if (prevPlaced > 0) {
                    var change = (placed - prevPlaced) / prevPlaced;
                    var up = change >= 0;
                    placedSub.appendChild(el('span', 'db-delta ' + (up ? 'up' : 'down'),
                        (up ? '▲ ' : '▼ ') + pct(Math.abs(change), 0) + (up ? ' up' : ' down')));
                    placedSub.appendChild(document.createTextNode(' vs ' + prevName));
                } else {
                    placedSub.appendChild(document.createTextNode('no placements in ' + prevName));
                }
            }
            setKpi('kPlaced', money(placed), placedSub);

            var withdrawn = sum(rows, function (f) { return f.withdrawn; });
            var withdrawnCount = rows.filter(function (f) { return f.withdrawn > 0; }).length;
            setKpi('kWithdrawn', money(withdrawn),
                withdrawnCount ? 'From ' + intFmt.format(withdrawnCount) + ' FD' + (withdrawnCount === 1 ? '' : 's') + ' · ' + (placed ? pct(withdrawn / placed) + ' of amount placed' : '') : 'No withdrawals');

            // FDs past their maturity date but still marked Ongoing
            var overdue = active.filter(function (f) { return f.daysLeft != null && f.daysLeft < 0; });
            var alertBox = document.getElementById('overdueAlert');
            alertBox.textContent = '';
            if (overdue.length) {
                alertBox.appendChild(el('strong', null, '⚠ ' + overdue.length + ' FD' + (overdue.length === 1 ? ' is' : 's are') + ' past maturity but still Ongoing'));
                alertBox.appendChild(document.createTextNode(' (' + money(sum(overdue, function (f) { return f.maturityValue; })) +
                    '). They should be withdrawn, renewed or reinvested. They are listed first under "Days left" when sorted.'));
                alertBox.hidden = false;
            } else {
                alertBox.hidden = true;
            }
        }

        // ---------- Sortable tables ----------
        function makeSortable(table, sortState, onChange) {
            table.querySelectorAll('th[data-key]').forEach(function (th) {
                th.querySelector('.db-sort').addEventListener('click', function () {
                    var key = th.getAttribute('data-key');
                    if (sortState.key === key) sortState.dir = -sortState.dir;
                    else { sortState.key = key; sortState.dir = th.classList.contains('num') ? -1 : 1; }
                    onChange();
                });
            });
        }

        function paintSortHeaders(table, sortState) {
            table.querySelectorAll('th[data-key]').forEach(function (th) {
                var arrow = th.querySelector('.arrow');
                if (th.getAttribute('data-key') === sortState.key) {
                    th.setAttribute('aria-sort', sortState.dir === 1 ? 'ascending' : 'descending');
                    arrow.textContent = sortState.dir === 1 ? '▲' : '▼';
                } else {
                    th.removeAttribute('aria-sort');
                    arrow.textContent = '▲▼';
                }
            });
        }

        function compareBy(key, dir) {
            return function (a, b) {
                var va = a[key], vb = b[key];
                if (va == null && vb == null) return 0;
                if (va == null) return 1;      // blanks always last
                if (vb == null) return -1;
                if (va instanceof Date) { va = va.getTime(); vb = vb.getTime(); }
                if (typeof va === 'string') return dir * va.localeCompare(vb);
                return dir * (va - vb);
            };
        }

        function td(text, className) {
            return el('td', className, text);
        }

        // ---------- Bank comparison ----------
        var bankSort = { key: 'principal', dir: -1 };
        var bankTable = document.getElementById('bankTable');
        makeSortable(bankTable, bankSort, function () { renderBankTable(); });

        function renderBankTable() {
            var rows = filtered({ bank: true });
            var groups = {};
            rows.forEach(function (f) {
                var g = groups[f.bank] || (groups[f.bank] = { bank: f.bank, count: 0, active: 0, principal: 0, rateW: 0, allDeposit: 0, allRateW: 0, interest: 0, next: null });
                g.count++;
                g.allDeposit += f.deposit;
                g.allRateW += f.deposit * f.rate;
                if (isActive(f)) {
                    g.active++;
                    g.principal += f.principal;
                    g.rateW += f.principal * f.rate;
                    g.interest += f.interest;
                    if (f.maturity && f.daysLeft >= 0 && (!g.next || f.maturity < g.next)) g.next = f.maturity;
                }
            });
            var grand = 0;
            Object.keys(groups).forEach(function (k) { grand += groups[k].principal; });
            var list = Object.keys(groups).map(function (k) {
                var g = groups[k];
                g.share = grand ? g.principal / grand : 0;
                g.rate = g.principal ? g.rateW / g.principal : (g.allDeposit ? g.allRateW / g.allDeposit : null);
                return g;
            }).sort(compareBy(bankSort.key, bankSort.dir));

            var tbody = bankTable.querySelector('tbody');
            tbody.textContent = '';
            if (!list.length) {
                var empty = el('tr');
                var cell = td('No FDs match these filters.');
                cell.colSpan = 8;
                empty.appendChild(cell);
                tbody.appendChild(empty);
            }
            list.forEach(function (g) {
                var tr = el('tr', 'db-bank-row' + (state.bank === g.bank ? ' selected' : ''));
                tr.tabIndex = 0;
                tr.title = state.bank === g.bank ? 'Click to show all banks' : 'Click to filter by ' + g.bank;
                tr.appendChild(td(g.bank));
                tr.appendChild(td(intFmt.format(g.count), 'num'));
                tr.appendChild(td(intFmt.format(g.active), 'num'));
                tr.appendChild(td(money(g.principal), 'num'));
                tr.appendChild(td(pct(g.share), 'num'));
                tr.appendChild(td(g.rate != null ? g.rate.toFixed(2) + '%' : '-', 'num'));
                tr.appendChild(td(money(g.interest), 'num'));
                tr.appendChild(td(fmtDate(g.next), 'num'));
                var pick = function () { setState({ bank: state.bank === g.bank ? 'ALL' : g.bank }); };
                tr.addEventListener('click', pick);
                tr.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(); } });
                tbody.appendChild(tr);
            });
            paintSortHeaders(bankTable, bankSort);
        }

        // ---------- FD table ----------
        // Most urgent first: overdue, then soonest to mature; inactive FDs last
        var fdSort = { key: 'daysLeft', dir: 1 };
        var fdPage = PAGE_SIZE;
        var fdTable = document.getElementById('fdTable');
        var currentRows = [];
        makeSortable(fdTable, fdSort, function () { renderFdTable(currentRows); });

        document.getElementById('btnShowAll').addEventListener('click', function () {
            fdPage = Infinity;
            renderFdTable(currentRows);
        });

        function daysLeftCell(f) {
            var cell = el('td', 'num');
            if (!isActive(f) || f.daysLeft == null) {
                cell.textContent = '-';
                return cell;
            }
            var d = f.daysLeft;
            var span = el('span', 'db-days');
            if (d < 0) {
                span.className += ' critical';
                span.textContent = '⚠ Overdue ' + (-d) + 'd';
            } else if (d <= 7) {
                span.className += ' critical';
                span.textContent = '⚠ ' + d + (d === 1 ? ' day' : ' days');
            } else if (d <= 30) {
                span.className += ' warning';
                span.textContent = '⏱ ' + d + ' days';
            } else {
                span.style.fontWeight = '400';
                span.textContent = intFmt.format(d) + ' days';
            }
            cell.appendChild(span);
            return cell;
        }

        function renderFdTable(rows) {
            currentRows = rows;
            var sorted = rows.slice().sort(compareBy(fdSort.key, fdSort.dir));
            var shown = sorted.slice(0, fdPage);
            var tbody = fdTable.querySelector('tbody');
            tbody.textContent = '';

            if (!shown.length) {
                var empty = el('tr');
                var cell = td('No FDs match these filters.');
                cell.colSpan = 11;
                empty.appendChild(cell);
                tbody.appendChild(empty);
            }

            shown.forEach(function (f) {
                var tr = el('tr');

                var idCell = el('td');
                var link = el('a', 'db-link', 'FD-' + f.id);
                link.href = '{{ route('fd.view') }}?id=' + encodeURIComponent(f.id);
                idCell.appendChild(link);
                if (f.acc) idCell.appendChild(el('span', 'db-sub', f.acc));
                tr.appendChild(idCell);

                tr.appendChild(td(f.bank));

                var typeCell = el('td');
                var key = el('span', 'db-type-key');
                var sw = el('span', 'db-swatch');
                sw.style.background = TYPE_COLOR[f.type];
                key.appendChild(sw);
                key.appendChild(document.createTextNode(TYPE_LABEL[f.type]));
                typeCell.appendChild(key);
                tr.appendChild(typeCell);

                var statusCell = el('td');
                statusCell.appendChild(el('span', 'db-pill', titleCase(f.status)));
                tr.appendChild(statusCell);

                tr.appendChild(td(money(f.principal), 'num'));
                tr.appendChild(td(f.rate.toFixed(2) + '%', 'num'));
                tr.appendChild(td(f.tenure ? f.tenure + ' mo' : '-', 'num'));
                tr.appendChild(td(fmtDate(f.start)));
                tr.appendChild(td(fmtDate(f.maturity)));
                tr.appendChild(daysLeftCell(f));
                tr.appendChild(td(money(f.maturityValue), 'num'));
                tbody.appendChild(tr);
            });

            paintSortHeaders(fdTable, fdSort);

            var desc = [];
            if (state.period !== 'ALL') desc.push('placed ' + (state.period === 'L12' ? 'in the last 12 months' : 'in ' + state.period.slice(1)));
            if (state.bank !== 'ALL') desc.push('at ' + state.bank);
            if (state.type !== 'ALL') desc.push(TYPE_LABEL[state.type]);
            if (state.status !== 'ALL') desc.push(titleCase(state.status));
            if (state.matMonth) desc.push('maturing in ' + monthLabel(state.matMonth));
            document.getElementById('fdTableDesc').textContent =
                desc.length ? 'Showing FDs ' + desc.join(', ') + '.' : 'Showing all FDs. Use the filters or click a chart to narrow the list.';

            document.getElementById('fdTableCount').textContent =
                'Showing ' + intFmt.format(shown.length) + ' of ' + intFmt.format(rows.length) + ' FDs';
            var showAll = document.getElementById('btnShowAll');
            showAll.hidden = shown.length >= rows.length;
            showAll.textContent = 'Show all ' + intFmt.format(rows.length);
        }

        // ---------- CSV export ----------
        function csvCell(v) {
            var s = v == null ? '' : String(v);
            if (/^[=+\-@\t\r]/.test(s)) s = "'" + s;   // stop spreadsheet formula injection
            return /[",\n\r]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
        }

        function isoDate(d) {
            return d ? d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') : '';
        }

        document.getElementById('btnExport').addEventListener('click', function () {
            var header = ['FD ID', 'Account No', 'Bank', 'Type', 'Status', 'Deposit (RM)', 'Current principal (RM)',
                'Rate (% p.a.)', 'Tenure (months)', 'Start date', 'Maturity date', 'Days left',
                'Expected interest (RM)', 'Maturity value (RM)', 'Withdrawn (RM)'];
            var lines = [header.map(csvCell).join(',')];
            currentRows.slice().sort(compareBy(fdSort.key, fdSort.dir)).forEach(function (f) {
                lines.push([f.id, f.acc, f.bank, TYPE_LABEL[f.type], titleCase(f.status),
                    f.deposit.toFixed(2), f.principal.toFixed(2), f.rate.toFixed(2), f.tenure,
                    isoDate(f.start), isoDate(f.maturity), isActive(f) && f.daysLeft != null ? f.daysLeft : '',
                    f.interest.toFixed(2), f.maturityValue.toFixed(2), f.withdrawn.toFixed(2)].map(csvCell).join(','));
            });
            var blob = new Blob(['﻿' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'fd-dashboard-' + isoDate(TODAY) + '.csv';
            document.body.appendChild(a);
            a.click();
            a.remove();
            setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
        });

        // ---------- Render everything ----------
        function render() {
            var rows = filtered();
            renderChips();
            renderKpis(rows);
            renderPlacements(rows);
            renderBankChart();
            renderLadder();
            renderStatusList();
            renderBankTable();
            renderFdTable(rows);
        }

        var now = new Date();
        document.getElementById('asOf').textContent = 'Data as of ' + fmtDate(now) + ', ' +
            String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

        render();

        // Chart labels are measured when drawn; redraw once the web font has loaded
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function () {
                [placementsChart, bankChart, ladderChart].forEach(function (c) { c.update('none'); });
            });
        }
    })();
    </script>
</body>
</html>
