<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Export</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
            background: #ffffff;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #f3f4f6;
            border-radius: 4px;
        }

        .meta-info .item {
            text-align: center;
        }

        .meta-info .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-info .value {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
        }

        .stats-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #eff6ff;
            border-radius: 4px;
            border-left: 4px solid #3b82f6;
        }

        .stats-section h2 {
            font-size: 12px;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: table;
            width: 100%;
        }

        .stats-grid .stat-item {
            display: table-cell;
            padding: 8px;
            text-align: center;
            border-right: 1px solid #bfdbfe;
        }

        .stats-grid .stat-item:last-child {
            border-right: none;
        }

        .stats-grid .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }

        .stats-grid .stat-label {
            font-size: 9px;
            color: #6b7280;
        }

        .table-container {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead {
            background: #1e40af;
            color: white;
        }

        th {
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        td {
            padding: 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            word-wrap: break-word;
            max-width: 150px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        tr:hover {
            background: #f3f4f6;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }

        .page-break {
            page-break-after: always;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">Survey Responses Export Report</div>
    </div>

    <div class="meta-info">
        <div class="item">
            <div class="label">Total Responses</div>
            <div class="value">{{ $totalResponses }}</div>
        </div>
        <div class="item">
            <div class="label">Date Range</div>
            <div class="value">
                @if($stats['dateRange']['from'] && $stats['dateRange']['to'])
                    {{ $stats['dateRange']['from'] }} - {{ $stats['dateRange']['to'] }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="item">
            <div class="label">Generated</div>
            <div class="value">{{ $generatedAt }}</div>
        </div>
    </div>

    @if($stats['overallAverage'] !== null)
    <div class="stats-section">
        <h2>Rating Statistics</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['overallAverage'] }}/5</div>
                <div class="stat-label">Overall Average Rating</div>
            </div>
            @foreach(array_slice($stats['averageRatings'], 0, 4) as $question => $average)
            <div class="stat-item">
                <div class="stat-value">{{ $average }}/5</div>
                <div class="stat-label" title="{{ $question }}">{{ Str::limit($question, 25) }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    @foreach(array_slice($headers, 0, 8) as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    @foreach(array_slice($headers, 0, 8) as $header)
                    <td>{{ Str::limit($row[$header] ?? '', 50) }}</td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ min(count($headers), 8) }}" style="text-align: center; padding: 20px; color: #9ca3af;">
                        No responses to display.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(count($headers) > 8)
    <div class="page-break"></div>
    <div class="header">
        <h1>{{ $title }} - Additional Details</h1>
        <div class="subtitle">Question Responses</div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Respondent</th>
                    @foreach(array_slice($headers, 8) as $header)
                    <th>{{ Str::limit($header, 30) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>
                    <td>{{ $row['ID'] ?? '' }}</td>
                    <td>{{ $row['Respondent Name'] ?? 'Anonymous' }}</td>
                    @foreach(array_slice($headers, 8) as $header)
                    <td>{{ Str::limit($row[$header] ?? '', 50) }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Generated by Valenzuela Satisfaction Survey System</p>
        <p>© {{ date('Y') }} City of Valenzuela. All rights reserved.</p>
    </div>
</body>
</html>
