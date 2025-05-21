<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2, h3 {
            text-align: center;
            margin: 0;
        }
        .dashed-divider {
            width: 100%;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .meta-left,
        .meta-right {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 4px;
            vertical-align: top;
            border-bottom: 1px dotted #000;
        }
        th {
        text-align: left;
        }
        .divider-col {
            width: 2%;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }
        .bold {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Humphrey Foundation Learning Center</h2>
    <h3>Attendance For : {{ $school_year ?? '2025-2026' }}</h3>

    <div class="dashed-divider"></div>

    <div class="meta-row">
        <div class="meta-left">
            <p><strong>Name:</strong> {{ $student_name ?? 'Bengco, Collin Matthew' }}</p>
            <p><strong>Students:</strong> {{ $grade ?? 'Grade 1' }}</p>
        </div>
        <div class="meta-right">
            <p><strong>Report Name:</strong> {{ $reportType ?? 'Daily Attendance' }}</p>
            <p><strong>Created By:</strong> Admin</p>
            <p><strong>Date Created:</strong> {{ now()->format('m/d/Y g:i A') }}</p>
            <p><strong>Date From:</strong> {{ $fromDate ?? '' }}</p>
            <p><strong>Date To:</strong> {{ $toDate ?? '' }}</p>
        </div>
    </div>

    <div class="dashed-divider"></div>

    <table>
        <thead>
            <tr class="bold">
                <th>DATE</th>
                <th>TIME RECORD</th>
                <th>STATUS</th>
                <th></th>
                <th>DATE</th>
                <th>TIME RECORD</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $half = ceil($records->count() / 2);
                $leftCol = $records->slice(0, $half)->values();
                $rightCol = $records->slice($half)->values();
                $rows = max($leftCol->count(), $rightCol->count());
            @endphp

            @for ($i = 0; $i < $rows; $i++)
                <tr>
                    {{-- Left column --}}
                    @php $left = $leftCol[$i] ?? null; @endphp
                    <td style="width: 15%;">
                        {{ $left ? $left->DATERECORD : '' }}
                    </td>
                    <td style="width: 15%;">
                        @if ($left && $left->STATUS === 'ABSENT')
                            ----------
                        @elseif ($left)
                            {{ $left->TIMEIN }}<br>
                            {{ $left->TIMEOUT }}<br>
                        @endif
                    </td>
                    <td style="width: 10%;">
                        @if ($left && $left->STATUS === 'ABSENT')
                            ABSENT
                        @elseif ($left)
                            @if ($type === 'daily')
                                INITIAL
                                <br>
                                LAST
                            @else
                                IN
                                <br>
                                OUT
                            @endif
                        @endif
                    </td>

                    <td class="divider-col"></td>

                    {{-- Right column --}}
                    @php $right = $rightCol[$i] ?? null; @endphp
                    <td style="width: 15%;">
                        {{ $right ? $right->DATERECORD : '' }}
                    </td>
                    <td style="width: 15%;">
                        @if ($right && $right->STATUS === 'ABSENT')
                            ----------
                        @elseif ($right)
                            {{ $right->TIMEIN }}<br>
                            {{ $right->TIMEOUT }}<br>
                        @endif
                    </td>
                    <td style="width: 10%;">
                        @if ($right && $right->STATUS === 'ABSENT')
                            ABSENT
                        @elseif ($right)
                            @if ($type === 'daily')
                                INITIAL
                                <br>
                                LAST
                            @else
                                IN
                                <br>
                                OUT
                            @endif
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>
