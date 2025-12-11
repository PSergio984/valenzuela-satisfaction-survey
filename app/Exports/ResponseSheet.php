<?php

namespace App\Exports;

use App\Models\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResponseSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected Response $response,
        protected int $sheetNumber
    ) {}

    public function collection(): Collection
    {
        $row = [
            $this->response->id,
            $this->response->survey->title ?? 'N/A',
            $this->response->submitted_at?->format('Y-m-d H:i:s') ?? 'Not submitted',
            $this->response->respondent_name ?? 'Anonymous',
            $this->response->respondent_email ?? 'Not provided',
            $this->response->formatted_time_to_complete ?? 'N/A',
        ];

        // Get all answers ordered by question order
        $orderedAnswers = $this->response->answers->sortBy(function ($answer) {
            return $answer->question?->order ?? 999;
        });

        foreach ($orderedAnswers as $answer) {
            if ($answer->selected_options) {
                $row[] = implode(', ', $answer->selected_options);
            } else {
                $row[] = $answer->value ?? '';
            }
        }

        return collect([$row]);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        $headers = [
            'Response ID',
            'Survey',
            'Submitted At',
            'Respondent Name',
            'Respondent Email',
            'Duration',
        ];

        // Get all answers ordered by question order
        $orderedAnswers = $this->response->answers->sortBy(function ($answer) {
            return $answer->question?->order ?? 999;
        });

        foreach ($orderedAnswers as $answer) {
            $headers[] = $answer->question?->question ?? 'Unknown Question';
        }

        return $headers;
    }

    public function title(): string
    {
        // Create a unique sheet name
        $surveyName = $this->response->survey?->title ?? 'Survey';
        $respondentName = $this->response->respondent_name ?? 'Anonymous';

        // Sanitize for Excel (max 31 chars, no special chars)
        $title = "{$surveyName} - {$respondentName}";
        $title = preg_replace('/[\/\\\?\*\[\]:]/u', '', $title);
        $title = mb_substr($title, 0, 31);

        return $title;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the header row
            1 => [
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

    /**
     * @return array<int, mixed>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->getLastColumn();

                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Add borders to all cells with data
                $sheet->getStyle("A1:{$lastColumn}2")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                // Style the data row
                $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }

    protected function getLastColumn(): string
    {
        $columnCount = 6 + $this->response->answers->count(); // 6 base columns + answers

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
