<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Survey Report: {{ $survey->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            font-size: 22px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 10px;
        }
        .summary {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 25px;
        }
        .summary h2 {
            font-size: 13px;
            color: #374151;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .summary-table {
            width: 100%;
        }
        .summary-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        .summary-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #3b82f6;
        }
        .question-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .question-header {
            background: #e5e7eb;
            padding: 10px 12px;
            border-left: 4px solid #3b82f6;
        }
        .question-header h3 {
            font-size: 12px;
            color: #1f2937;
            margin-bottom: 3px;
        }
        .question-type {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .question-content {
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 12px;
        }
        .rating-display {
            text-align: center;
            padding: 10px;
            margin-bottom: 10px;
        }
        .rating-score {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
        }
        .rating-max {
            font-size: 14px;
            color: #9ca3af;
        }
        .bar-chart {
            margin-top: 8px;
        }
        .bar-table {
            width: 100%;
            border-collapse: collapse;
        }
        .bar-table td {
            padding: 3px 0;
            vertical-align: middle;
        }
        .bar-label-cell {
            width: 80px;
            font-size: 10px;
            color: #374151;
        }
        .bar-container {
            background: #e5e7eb;
            height: 20px;
            position: relative;
        }
        .bar-fill {
            background: #3b82f6;
            height: 20px;
            display: inline-block;
        }
        .bar-value {
            width: 80px;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            padding-left: 8px;
        }
        .sample-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sample-item {
            padding: 8px 10px;
            background: #f9fafb;
            border-left: 3px solid #3b82f6;
            margin-bottom: 6px;
            font-style: italic;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        .no-responses {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $survey->title }}</h1>
        @if($survey->description)
            <p>{{ $survey->description }}</p>
        @endif
        <p style="margin-top: 8px;">Report generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>Survey Summary</h2>
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Total Responses</div>
                    <div class="summary-value">{{ $survey->responses->count() }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Questions</div>
                    <div class="summary-value">{{ $survey->questions->count() }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">{{ $survey->is_active ? 'Active' : 'Inactive' }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Period</div>
                    <div class="summary-value" style="font-size: 11px;">
                        @if($survey->starts_at && $survey->ends_at)
                            {{ $survey->starts_at->format('M d') }} - {{ $survey->ends_at->format('M d, Y') }}
                        @elseif($survey->starts_at)
                            From {{ $survey->starts_at->format('M d, Y') }}
                        @elseif($survey->ends_at)
                            Until {{ $survey->ends_at->format('M d, Y') }}
                        @else
                            Always Open
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($survey->responses->count() === 0)
        <div class="no-responses">
            <p>No responses have been submitted for this survey yet.</p>
        </div>
    @else
        @foreach($statistics as $questionId => $stats)
            <div class="question-section">
                <div class="question-header">
                    <h3>{{ $stats['question'] }}</h3>
                    <span class="question-type">{{ strtoupper($stats['type']) }} • {{ $stats['total_responses'] }} responses</span>
                </div>
                <div class="question-content">
                    @if($stats['type'] === 'rating')
                        <div class="rating-display">
                            <span class="rating-score">{{ $stats['average'] ?? 0 }}</span>
                            <span class="rating-max">/ 5</span>
                        </div>
                        @if(!empty($stats['distribution']))
                            @php
                                $maxCount = max($stats['distribution'] ?: [1]);
                                $totalRatings = array_sum($stats['distribution']);
                            @endphp
                            <div class="bar-chart">
                                <table class="bar-table">
                                    @for($i = 5; $i >= 1; $i--)
                                        @php
                                            $count = $stats['distribution'][$i] ?? 0;
                                            $percentage = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                                            $responsePercent = $totalRatings > 0 ? round($count / $totalRatings * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="bar-label-cell">{{ $i }} star{{ $i > 1 ? 's' : '' }}</td>
                                            <td>
                                                <div class="bar-container">
                                                    <div class="bar-fill" style="width: {{ max($percentage, 2) }}%;"></div>
                                                </div>
                                            </td>
                                            <td class="bar-value">{{ $count }} ({{ $responsePercent }}%)</td>
                                        </tr>
                                    @endfor
                                </table>
                            </div>
                        @endif

                    @elseif(in_array($stats['type'], ['radio', 'select', 'checkbox']))
                        @if(!empty($stats['distribution']))
                            @php
                                $total = array_sum($stats['distribution']);
                                $maxCount = max($stats['distribution'] ?: [1]);
                            @endphp
                            <div class="bar-chart">
                                <table class="bar-table">
                                    @foreach($stats['distribution'] as $option => $count)
                                        @php
                                            $percentage = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                                            $responsePercent = $total > 0 ? round($count / $total * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="bar-label-cell">{{ Str::limit($option, 20) }}</td>
                                            <td>
                                                <div class="bar-container">
                                                    <div class="bar-fill" style="width: {{ max($percentage, 2) }}%;"></div>
                                                </div>
                                            </td>
                                            <td class="bar-value">{{ $count }} ({{ $responsePercent }}%)</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @else
                            <p style="color: #9ca3af; font-style: italic;">No responses yet</p>
                        @endif

                    @else
                        @if(!empty($stats['sample_answers']))
                            <p style="margin-bottom: 8px; font-size: 10px; color: #6b7280;">Sample responses:</p>
                            <ul class="sample-list">
                                @foreach($stats['sample_answers'] as $answer)
                                    <li class="sample-item">"{{ Str::limit($answer, 200) }}"</li>
                                @endforeach
                            </ul>
                        @else
                            <p style="color: #9ca3af; font-style: italic;">No text responses yet</p>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>Survey System</p>
        <p style="margin-top: 3px;">&copy; {{ date('Y') }} All rights reserved &bull; Confidential Report</p>
    </div>
</body>
</html>
