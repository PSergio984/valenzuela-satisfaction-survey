<?php

namespace App\Exports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllResponsesExport implements WithMultipleSheets
{
    public function __construct(
        protected ?int $surveyId = null
    ) {}

    /**
     * @return array<int, SurveyResponsesSheet>
     */
    public function sheets(): array
    {
        $sheets = [];

        $query = Survey::whereHas('responses')
            ->with(['questions' => fn ($q) => $q->orderBy('order'), 'responses.answers'])
            ->orderBy('title');

        // Filter by survey if provided
        if ($this->surveyId) {
            $query->where('id', $this->surveyId);
        }

        $surveys = $query->get();

        foreach ($surveys as $survey) {
            $sheets[] = new SurveyResponsesSheet($survey);
        }

        return $sheets;
    }
}
