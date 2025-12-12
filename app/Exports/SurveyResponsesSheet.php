<?php

namespace App\Exports;

use App\Models\Survey;
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

class SurveyResponsesSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected Survey $survey
    ) {}

    public function collection(): Collection
    {
        return $this->survey->responses->map(function ($response) {
            $row = [
                $response->id,
                $response->submitted_at?->format('Y-m-d H:i:s') ?? 'Not submitted',
                $response->respondent_name ?? 'Anonymous',
                $response->respondent_email ?? 'Not provided',
                $response->formatted_time_to_complete ?? 'N/A',
            ];

            foreach ($this->survey->questions as $question) {
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
        });
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        $headers = [
            'ID',
            'Submitted At',
            'Respondent Name',
            'Respondent Email',
            'Duration',
        ];

        foreach ($this->survey->questions as $question) {
            $headers[] = $question->question;
        }

        return $headers;
    }

    public function title(): string
    {
        // Sanitize the title for Excel sheet name (max 31 chars, no special chars)
        $title = $this->survey->title;
        $title = preg_replace('/[\/\\\?\*\[\]:]/u', '', $title); // Remove invalid chars
        $title = mb_substr($title, 0, 31); // Max 31 characters for Excel sheet names

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
                $lastRow = $this->survey->responses->count() + 1;

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
            },
        ];
    }

    protected function getLastColumn(): string
    {
        $columnCount = 6 + $this->survey->questions->count(); // 6 base columns + questions

        return $this->getColumnLetter($columnCount);
    }

    protected function getColumnLetter(int $columnNumber): string
    {
        $letter = '';

        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)) . $letter;
            $columnNumber = intdiv($columnNumber, 26);
        }

        return $letter;
    }
}
