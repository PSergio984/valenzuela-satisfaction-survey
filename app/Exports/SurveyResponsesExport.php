<?php

namespace App\Exports;

use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveyResponsesExport implements FromQuery, ShouldAutoSize, ShouldQueue, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    protected ?Survey $survey = null;

    protected $questions = null;

    public function __construct(
        protected int $surveyId,
        protected int $userId,
        protected string $filename,
        protected ?array $responseIds = null
    ) {}

    public function getSurveyId(): int
    {
        return $this->surveyId;
    }

    public function query()
    {
        return Response::query()
            ->where('survey_id', $this->surveyId)
            ->when($this->responseIds, fn ($query) => $query->whereIn('id', $this->responseIds))
            ->whereNotNull('submitted_at')
            ->with('answers');
    }

    public function map($response): array
    {
        $this->ensureSurveyLoaded();

        $row = [
            $response->id,
            $response->submitted_at?->format('Y-m-d H:i:s') ?? 'Not submitted',
            $response->respondent_name ?? 'Anonymous',
            $response->respondent_email ?? 'Not provided',
            $response->formatted_time_to_complete ?? 'N/A',
        ];

        foreach ($this->questions as $question) {
            $answer = $response->answers->firstWhere('question_id', $question->id);
            if ($answer) {
                if ($answer->selected_options) {
                    $row[] = implode(', ', $answer->selected_options);
                } else {
                    $row[] = $answer->value ?? '';
                }
            } else {
                $row[] = '';
            }
        }

        return $row;
    }

    public function headings(): array
    {
        $this->ensureSurveyLoaded();

        $headers = [
            'ID',
            'Submitted At',
            'Respondent Name',
            'Respondent Email',
            'Duration',
        ];

        foreach ($this->questions as $question) {
            $headers[] = $question->question;
        }

        return $headers;
    }

    public function title(): string
    {
        return 'Survey Responses';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the header row (will be on row 3 after AfterSheet modifications)
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'], // Blue-800
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->ensureSurveyLoaded();

                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->getLastColumn();
                $responsesCount = Response::where('survey_id', $this->surveyId)->whereNotNull('submitted_at')->count();
                $lastRow = $responsesCount + 1;

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Add borders to all cells with data
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                // Alternate row colors for readability
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
                            ],
                        ]);
                    }
                }

                // Center align all data cells
                $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze the header row
                $sheet->freezePane('A2');

                // Add title row at the top
                $sheet->insertNewRowBefore(1, 2);

                // Merge cells for title
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', $this->survey->title);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Add subtitle with export date
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'Exported on '.now()->format('F d, Y \a\t g:i A').' • Total Responses: '.$responsesCount);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '6B7280'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Send notification
                $user = User::find($this->userId);
                $survey = Survey::find($this->surveyId);

                if ($user) {
                    Notification::make()
                        ->title('Export Ready')
                        ->body("The export for survey '{$survey->title}' is ready for download.")
                        ->sendToDatabase($user);
                }
            },
        ];
    }

    protected function ensureSurveyLoaded(): void
    {
        if ($this->survey === null) {
            $this->survey = Survey::findOrFail($this->surveyId);
            $this->questions = $this->survey->questions()->orderBy('order')->get();
        }
    }

    protected function getLastColumn(): string
    {
        $columnCount = 5 + $this->questions->count(); // 5 base columns + questions

        return $this->getColumnLetter($columnCount);
    }

    protected function getColumnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)).$letter;
            $columnNumber = intdiv($columnNumber, 26);
        }

        return $letter;
    }
}
