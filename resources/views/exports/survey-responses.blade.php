<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Report: {{ $survey->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .summary {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary h2 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 10px;
        }
        .summary-grid {
            display: flex;
            gap: 20px;
        }
        .summary-item {
            flex: 1;
        }
        .summary-item .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }
        .question-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .question-header {
            background: #e5e7eb;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
        }
        .question-header h3 {
            font-size: 13px;
            color: #1f2937;
            margin-bottom: 3px;
        }
        .question-header .type {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .question-content {
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 15px;
            border-radius: 0 0 5px 5px;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-label {
            color: #374151;
        }
        .stat-value {
            font-weight: bold;
            color: #3b82f6;
        }
        .bar-chart {
            margin-top: 10px;
        }
        .bar-item {
            margin-bottom: 8px;
        }
        .bar-label {
            font-size: 11px;
            color: #374151;
            margin-bottom: 3px;
        }
        .bar-container {
            background: #e5e7eb;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .bar-fill {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 30px;
        }
        .rating-average {
            text-align: center;
            padding: 15px;
        }
        .rating-average .score {
            font-size: 36px;
            font-weight: bold;
            color: #3b82f6;
        }
        .rating-average .max {
            font-size: 14px;
            color: #6b7280;
        }
        .sample-answers {
            list-style: none;
        }
        .sample-answers li {
            padding: 8px 12px;
            background: #f9fafb;
            border-left: 3px solid #3b82f6;
            margin-bottom: 8px;
            font-style: italic;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .no-responses {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $survey->title }}</h1>
        @if($survey->description)
            <p>{{ $survey->description }}</p>
        @endif
        <p style="margin-top: 10px;">Report generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>Survey Summary</h2>
        <table width="100%">
            <tr>
                <td width="25%">
                    <div class="summary-item">
                        <div class="label">Total Responses</div>
                        <div class="value">{{ $survey->responses->count() }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-item">
                        <div class="label">Questions</div>
                        <div class="value">{{ $survey->questions->count() }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-item">
                        <div class="label">Status</div>
                        <div class="value">{{ $survey->is_active ? 'Active' : 'Inactive' }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-item">
                        <div class="label">Period</div>
                        <div class="value" style="font-size: 12px;">
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
                    <span class="type">{{ ucfirst($stats['type']) }} • {{ $stats['total_responses'] }} responses</span>
                </div>
                <div class="question-content">
                    @if($stats['type'] === 'rating')
                        <div class="rating-average">
                            <span class="score">{{ $stats['average'] ?? 0 }}</span>
                            <span class="max">/ 5</span>
                        </div>
                        @if(!empty($stats['distribution']))
                            <div class="bar-chart">
                                @php $maxCount = max($stats['distribution'] ?: [1]); @endphp
                                @for($i = 5; $i >= 1; $i--)
                                    @php $count = $stats['distribution'][$i] ?? 0; @endphp
                                    <div class="bar-item">
                                        <div class="bar-label">{{ $i }} star{{ $i > 1 ? 's' : '' }}</div>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: {{ $maxCount > 0 ? ($count / $maxCount * 100) : 0 }}%">
                                                {{ $count }}
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        @endif

                    @elseif(in_array($stats['type'], ['radio', 'select', 'checkbox']))
                        @if(!empty($stats['distribution']))
                            @php
                                $total = array_sum($stats['distribution']);
                                $maxCount = max($stats['distribution'] ?: [1]);
                            @endphp
                            <div class="bar-chart">
                                @foreach($stats['distribution'] as $option => $count)
                                    <div class="bar-item">
                                        <div class="bar-label">{{ $option }}</div>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: {{ $maxCount > 0 ? ($count / $maxCount * 100) : 0 }}%">
                                                {{ $count }} ({{ round($count / $total * 100) }}%)
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No responses yet</p>
                        @endif

                    @else
                        @if(!empty($stats['sample_answers']))
                            <p style="margin-bottom: 10px; font-size: 11px; color: #6b7280;">Sample responses:</p>
                            <ul class="sample-answers">
                                @foreach($stats['sample_answers'] as $answer)
                                    <li>"{{ Str::limit($answer, 200) }}"</li>
                                @endforeach
                            </ul>
                        @else
                            <p>No text responses yet</p>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>Customer Satisfaction Survey System • Confidential Report</p>
    </div>
</body>
</html>
