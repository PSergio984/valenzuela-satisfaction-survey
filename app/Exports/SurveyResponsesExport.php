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

class SurveyResponsesExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected Survey $survey
    ) {
        $this->survey->load(['questions' => fn ($q) => $q->orderBy('order'), 'responses.answers']);
    }

    public function collection(): Collection
    {
        return $this->survey->responses->map(function ($response) {
            $row = [
                $response->id,
                $response->submitted_at?->format('Y-m-d H:i:s') ?? 'Not submitted',
                $response->respondent_name ?? 'Anonymous',
                $response->respondent_email ?? 'Not provided',
                $response->ip_address ?? 'N/A',
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
            'IP Address',
            'Duration',
        ];

        foreach ($this->survey->questions as $question) {
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
        $lastColumn = $this->getLastColumn();

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
                $sheet->setCellValue('A2', 'Exported on '.now()->format('F d, Y \a\t g:i A').' • Total Responses: '.$this->survey->responses->count());
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
            $letter = chr(65 + ($columnNumber % 26)).$letter;
            $columnNumber = intdiv($columnNumber, 26);
        }

        return $letter;
    }
}
