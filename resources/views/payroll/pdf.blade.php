<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslips - {{ $month }}/{{ $year }}</title>
    <style>
        @page {
            size: A4;
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .payslip {
            position: relative;
            min-height: 950px;
            padding: 30px;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ✅ Page break between each payslip, not after last */
        .payslip:not(:last-child) {
            page-break-after: always;
        }

        h2, h4 {
            margin: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            position: relative;
            z-index: 2;
        }

        th, td {
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .section-title {
            background: #f3f3f3;
            padding: 4px;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }

        .right { text-align: right; }
        .small { font-size: 11px; color: #666; }

        .footer {
            margin-top: 50px;
            font-size: 11px;
            text-align: right;
            position: relative;
            z-index: 2;
        }

        /* ✅ FIXED WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            font-weight: bold;
            color: rgba(180, 180, 180, 0.2);
            z-index: 1; /* behind content but above background */
            width: 100%;
            text-align: center;
            pointer-events: none;
            white-space: nowrap;
        }

        .watermark.finalized {
            color: rgba(100, 200, 100, 0.25);
        }

        .watermark.draft {
            color: rgba(200, 100, 100, 0.25);
        }
    </style>
</head>
<body>

@foreach($payrolls as $payroll)
    <div class="payslip">
        <div class="header">
            <h2>{{ config('app.name', 'Laravel') }}</h2>
            <h4>Employee Payslip</h4>
            <p>{{ \Carbon\Carbon::createFromDate(null, (int)$month, 1)->format('F') }} {{ $year }}</p>
        </div>

        <table>
            <tr>
                <th>Employee Name</th>
                <td>{{ $payroll->employee->user->name }}</td>
                <th>Employee ID</th>
                <td>EMP-{{ $payroll->employee->user_id }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $payroll->employee->department->name }}</td>
                <th>Issued By</th>
                <td>{{ auth()->user()->name ?? 'HR Department' }}</td>
            </tr>
            <tr>
                <th>Issued Date</th>
                <td colspan="3">{{ now()->format('d M Y') }}</td>
            </tr>
        </table>

        <p class="section-title">Payroll Summary</p>
        <table>
            <tr>
                <th>Daily Rate</th>
                <td class="right">Rp {{ number_format($payroll->daily_rate, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Days Present</th>
                <td class="right">{{ $payroll->total_present }}</td>
            </tr>
            <tr>
                <th>Days Late</th>
                <td class="right">{{ $payroll->total_late }}</td>
            </tr>
            <tr>
                <th>Days Absent</th>
                <td class="right">{{ $payroll->total_absent }}</td>
            </tr>
            <tr>
                <th>Total Pay</th>
                <td class="right"><strong>Rp {{ number_format($payroll->total_pay, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            @if($payroll->payment_datetime)
                <p>Paid on: {{ \Carbon\Carbon::parse($payroll->payment_datetime)->format('d M Y H:i') }}</p>
            @else
                <p class="small">Status: Unpaid</p>
            @endif
        </div>
    </div>
@endforeach

</body>
</html>
