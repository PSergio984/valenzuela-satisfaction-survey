<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Survey Responses Report' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 25px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 9px;
        }
        .summary {
            background: #f3f4f6;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .summary h2 {
            font-size: 12px;
            color: #374151;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .summary-table {
            width: 100%;
        }
        .summary-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #3b82f6;
        }
        .survey-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .survey-header {
            background: #1e40af;
            color: white;
            padding: 10px 12px;
            margin-bottom: 0;
        }
        .survey-header h3 {
            font-size: 12px;
            margin-bottom: 2px;
        }
        .survey-header span {
            font-size: 9px;
            opacity: 0.9;
        }
        .responses-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        .responses-table th {
            background: #e5e7eb;
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            color: #374151;
            border: 1px solid #d1d5db;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .responses-table td {
            padding: 5px 4px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .responses-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .stats-section {
            background: #eff6ff;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
            border-radius: 0 4px 4px 0;
        }
        .stats-section h2 {
            font-size: 11px;
            color: #1e40af;
            margin-bottom: 8px;
        }
        .stats-grid {
            width: 100%;
        }
        .stats-grid td {
            padding: 6px;
            text-align: center;
            border-right: 1px solid #bfdbfe;
        }
        .stats-grid td:last-child {
            border-right: none;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
        }
        .stat-label {
            font-size: 8px;
            color: #6b7280;
        }
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        .no-responses {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
            background: #f9fafb;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'All Survey Responses Report' }}</h1>
        <p>Generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>Report Summary</h2>
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Total Responses</div>
                    <div class="summary-value">{{ $totalResponses }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Surveys</div>
                    <div class="summary-value">{{ $stats['surveyCount'] }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Date Range</div>
                    <div class="summary-value" style="font-size: 10px;">
                        @if($stats['dateRange']['from'] && $stats['dateRange']['to'])
                            {{ $stats['dateRange']['from'] }} - {{ $stats['dateRange']['to'] }}
                        @else
                            N/A
                        @endif
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-label">Overall Avg Rating</div>
                    <div class="summary-value">
                        @if($stats['overallAverage'])
                            {{ $stats['overallAverage'] }}/5
                        @else
                            N/A
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($stats['averageRatings']))
    <div class="stats-section">
        <h2>Rating Statistics by Question</h2>
        <table class="stats-grid">
            <tr>
                @foreach(array_slice($stats['averageRatings'], 0, 5) as $question => $average)
                <td>
                    <div class="stat-value">{{ $average }}/5</div>
                    <div class="stat-label" title="{{ $question }}">{{ Str::limit($question, 30) }}</div>
                </td>
                @endforeach
            </tr>
        </table>
    </div>
    @endif

    @if($totalResponses === 0)
        <div class="no-responses">
            <p>No responses have been submitted yet.</p>
        </div>
    @else
        @foreach($responsesBySurvey as $surveyId => $responses)
            @php
                $survey = $responses->first()->survey;
            @endphp
            <div class="survey-section">
                <div class="survey-header">
                    <h3>{{ $survey->title ?? 'Unknown Survey' }}</h3>
                    <span>{{ $responses->count() }} responses</span>
                </div>
                <table class="responses-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th style="width: 70px;">Date</th>
                            <th style="width: 80px;">Respondent</th>
                            @foreach($responses->first()->answers as $answer)
                                <th>{{ $answer->question->question ?? 'Q' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $index => $response)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $response->submitted_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td>{{ $response->respondent_name ?? 'Anonymous' }}</td>
                            @foreach($response->answers as $answer)
                                <td>
                                    @if($answer->selected_options)
                                        {{ implode(', ', $answer->selected_options) }}
                                    @else
                                        {{ $answer->value ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif

    <div class="footer">
        <p>Survey System</p>
        <p style="margin-top: 3px;">&copy; {{ date('Y') }} All rights reserved &bull; Confidential Report</p>
    </div>
</body>
</html>
