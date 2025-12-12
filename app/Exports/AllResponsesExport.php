<?php

namespace App\Exports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllResponsesExport implements WithMultipleSheets
{
    /**
     * @return array<int, SurveyResponsesSheet>
     */
    public function sheets(): array
    {
        $sheets = [];

        // Get all surveys that have responses, ordered by title
        $surveys = Survey::whereHas('responses')
            ->with(['questions' => fn ($q) => $q->orderBy('order'), 'responses.answers'])
            ->orderBy('title')
            ->get();

        foreach ($surveys as $survey) {
            $sheets[] = new SurveyResponsesSheet($survey);
        }

        return $sheets;
    }
}
