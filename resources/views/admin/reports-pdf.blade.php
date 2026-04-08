<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 35px 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1a1a1a; font-size: 11px; line-height: 1.5; }

        .header { text-align: center; border-bottom: 3px solid #1392EC; padding-bottom: 12px; margin-bottom: 8px; }
        .header h1 { font-size: 20px; color: #1392EC; margin: 0 0 3px; letter-spacing: 1px; }
        .header h2 { font-size: 13px; color: #555; margin: 0 0 2px; font-weight: normal; }
        .header p { font-size: 9px; color: #888; margin: 2px 0 0; }

        .report-title { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 15px 0 5px; text-transform: uppercase; letter-spacing: 1px; }
        .report-meta { text-align: center; font-size: 10px; color: #666; margin-bottom: 5px; }

        .filters-box { background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 4px; padding: 8px 12px; margin-bottom: 18px; }
        .filters-box .label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 3px; }
        .filters-box .value { font-size: 10px; color: #333; }

        .section-title { font-size: 14px; font-weight: bold; color: #1392EC; border-bottom: 2px solid #1392EC; padding-bottom: 4px; margin: 22px 0 10px; }
        .sub-title { font-size: 12px; font-weight: bold; color: #333; margin: 14px 0 6px; }

        /* KPI Grid */
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .kpi-grid td { text-align: center; padding: 10px 8px; border: 1px solid #e0e0e0; }
        .kpi-grid .kpi-value { font-size: 20px; font-weight: bold; color: #1392EC; display: block; }
        .kpi-grid .kpi-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #888; display: block; margin-top: 2px; }

        /* Data Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10px; }
        .data-table th { background: #1392EC; color: #fff; padding: 6px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-table td { padding: 5px 10px; border-bottom: 1px solid #eee; }
        .data-table tr:nth-child(even) td { background: #f8f9fa; }
        .data-table .text-right { text-align: right; }
        .data-table .text-center { text-align: center; }

        .no-data { color: #999; font-style: italic; font-size: 10px; padding: 8px 0; }

        /* Demographics layout */
        .demo-grid { width: 100%; border-collapse: collapse; }
        .demo-grid td { vertical-align: top; padding: 0 8px 10px 0; width: 50%; }

        .footer { margin-top: 30px; padding-top: 10px; border-top: 2px solid #1392EC; font-size: 8px; color: #999; text-align: center; }

        .page-break { page-break-before: always; }

        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(19, 146, 236, 0.03); font-weight: bold; letter-spacing: 10px; z-index: -1; }
    </style>
</head>
<body>
    <div class="watermark">UV TOLEDO CLINIC</div>

    {{-- ═══ HEADER ═══ --}}
    <div class="header">
        <h1>UV Toledo Clinic</h1>
        <h2>University Health Services</h2>
        <p>University of the Visayas &mdash; Toledo Campus</p>
    </div>

    <div class="report-title">Reports &amp; Analytics Report</div>
    <div class="report-meta">Generated on: {{ $generatedAt }}</div>

    @if(!empty($filtersApplied))
    <div class="filters-box">
        <div class="label">Filters Applied</div>
        @foreach($filtersApplied as $filter)
            <div class="value">{{ $filter }}</div>
        @endforeach
    </div>
    @endif

    {{-- ═══ KPI SUMMARY ═══ --}}
    <div class="section-title">Key Performance Indicators</div>
    <table class="kpi-grid">
        <tr>
            <td>
                <span class="kpi-value">{{ $summary['total'] }}</span>
                <span class="kpi-label">Total Appointments</span>
            </td>
            <td>
                <span class="kpi-value">{{ $summary['completed'] }}</span>
                <span class="kpi-label">Completed Consultations</span>
            </td>
            <td>
                <span class="kpi-value">{{ $summary['unique_students'] }}</span>
                <span class="kpi-label">Students Served</span>
            </td>
            <td>
                <span class="kpi-value">{{ $certSummary['approved'] }}</span>
                <span class="kpi-label">Medical Certificates</span>
            </td>
            <td>
                <span class="kpi-value">{{ $completionRate }}%</span>
                <span class="kpi-label">Completion Rate</span>
            </td>
        </tr>
    </table>

    {{-- ═══ OVERVIEW ═══ --}}
    <div class="section-title">Overview</div>

    <div class="sub-title">Daily Visit Trends</div>
    @if($dailyTrend->sum('total') > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-center">Total</th>
                <th class="text-center">Completed</th>
                <th class="text-center">Appointments</th>
                <th class="text-center">Certificates</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyTrend as $day)
                @if($day['total'] > 0)
                <tr>
                    <td>{{ $day['date'] }}</td>
                    <td class="text-center">{{ $day['total'] }}</td>
                    <td class="text-center">{{ $day['completed'] }}</td>
                    <td class="text-center">{{ $day['appointments'] }}</td>
                    <td class="text-center">{{ $day['certificates'] }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No visit data recorded for this period.</p>
    @endif

    <div class="sub-title">Staff Consultation Counts</div>
    @if($byStaff->sum('total') > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Staff</th>
                <th class="text-center">Total</th>
                <th class="text-center">Completed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byStaff as $staff)
                @if($staff['total'] > 0)
                <tr>
                    <td>{{ $staff['staff'] }}</td>
                    <td class="text-center">{{ $staff['total'] }}</td>
                    <td class="text-center">{{ $staff['completed'] }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No staff consultation data recorded for this period.</p>
    @endif

    {{-- ═══ BY SERVICE ═══ --}}
    <div class="page-break"></div>
    <div class="section-title">By Service</div>
    @if($byService->sum('total') > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Service</th>
                <th class="text-center">Total Requests</th>
                <th class="text-center">Completed</th>
                <th class="text-center">Completion Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byService as $svc)
                <tr>
                    <td>{{ $svc['service'] }}@if(!empty($svc['is_certificate'])) <span style="color: #888; font-size: 9px;">(Certificate)</span>@endif</td>
                    <td class="text-center">{{ $svc['total'] }}</td>
                    <td class="text-center">{{ $svc['completed'] }}</td>
                    <td class="text-center">{{ $svc['completion_rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No service data recorded for this period.</p>
    @endif

    {{-- ═══ COMMON CASES ═══ --}}
    <div class="section-title">Common Cases</div>

    <div class="sub-title">Top Appointment Reasons</div>
    @if($topReasons->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Reason</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topReasons as $reason => $count)
            <tr>
                <td>{{ ucfirst($reason) }}</td>
                <td class="text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No appointment reasons recorded for this period.</p>
    @endif

    <div class="sub-title">Top Diagnoses</div>
    @if($topDiagnoses->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Diagnosis</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topDiagnoses as $diag)
            <tr>
                <td>{{ ucfirst($diag->diagnosis) }}</td>
                <td class="text-center">{{ $diag->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No diagnosis data recorded for this period.</p>
    @endif

    <div class="sub-title">Top Certificate Purposes</div>
    @if($topCertReasons->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th>Purpose</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topCertReasons as $purpose => $count)
            <tr>
                <td>{{ ucfirst($purpose) }}</td>
                <td class="text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No certificate request data recorded for this period.</p>
    @endif

    {{-- ═══ STUDENT DEMOGRAPHICS ═══ --}}
    <div class="page-break"></div>
    <div class="section-title">Student Demographics</div>

    <table class="demo-grid">
        <tr>
            {{-- Gender Distribution --}}
            <td>
                @if($byGender->count() > 0)
                <div class="sub-title">Gender Distribution</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gender</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byGender as $gender => $count)
                        <tr>
                            <td>{{ ucfirst($gender ?: 'Unknown') }}</td>
                            <td class="text-center">{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>

            {{-- Age Groups --}}
            <td>
                @if($byAge->count() > 0)
                <div class="sub-title">Age Groups</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Age Range</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byAge as $range => $count)
                        <tr>
                            <td>{{ $range }}</td>
                            <td class="text-center">{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Department Distribution --}}
    @if($byDepartment->count() > 0)
    <div class="sub-title">Department Distribution</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Department</th>
                <th class="text-center">Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byDepartment as $dept => $count)
            <tr>
                <td>{{ $dept }}</td>
                <td class="text-center">{{ $count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="demo-grid">
        <tr>
            {{-- Year Level Distribution --}}
            <td>
                @if($byYearLevel->count() > 0)
                <div class="sub-title">Year Level Distribution</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Year Level</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byYearLevel as $level => $count)
                        <tr>
                            <td>{{ $level }}</td>
                            <td class="text-center">{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>

            {{-- Program Distribution --}}
            <td>
                @if($byProgram->count() > 0)
                <div class="sub-title">Program Distribution</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th class="text-center">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byProgram as $program => $count)
                        <tr>
                            <td>{{ $program }}</td>
                            <td class="text-center">{{ $count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- ═══ FOOTER ═══ --}}
    <div class="footer">
        <p>This report was generated from UV Clinic Health Services system.</p>
        <p>{{ $generatedAt }}</p>
    </div>
</body>
</html>
