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

class AllResponsesExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    protected Collection $responses;

    protected array $allQuestions = [];

    public function __construct()
    {
        $this->responses = Response::with(['survey', 'answers.question'])->get();

        // Collect all unique questions across all surveys
        $this->collectAllQuestions();
    }

    protected function collectAllQuestions(): void
    {
        $questions = [];

        foreach ($this->responses as $response) {
            foreach ($response->answers as $answer) {
                if ($answer->question) {
                    $key = $answer->question_id;
                    if (! isset($questions[$key])) {
                        $questions[$key] = $answer->question->question;
                    }
                }
            }
        }

        $this->allQuestions = $questions;
    }

    public function collection(): Collection
    {
        return $this->responses->map(function ($response) {
            $row = [
                $response->id,
                $response->submitted_at?->format('Y-m-d H:i:s') ?? 'Not submitted',
                $response->respondent_name ?? 'Anonymous',
                $response->respondent_email ?? 'Not provided',
                $response->formatted_time_to_complete ?? 'N/A',
            ];

            // Add answers for each question
            foreach ($this->allQuestions as $questionId => $questionText) {
                $answer = $response->answers->firstWhere('question_id', $questionId);
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

        foreach ($this->allQuestions as $questionText) {
            $headers[] = $questionText;
        }

        return $headers;
    }

    public function title(): string
    {
        return 'All Responses';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the header row (row 3 after title rows are added)
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
                    'wrapText' => true,
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
                $lastRow = $this->responses->count() + 3; // +3 for title rows and header

                // Insert title rows at the top
                $sheet->insertNewRowBefore(1, 2);

                // Set row height for header
                $sheet->getRowDimension(3)->setRowHeight(30);

                // Merge cells for title
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'All Survey Responses Export');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => '1F2937'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(35);

                // Add subtitle with export date
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', 'Exported on '.now()->format('F d, Y \a\t g:i A').' • Total Responses: '.$this->responses->count());
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

                // Add borders to all cells with data
                $sheet->getStyle("A3:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                ]);

                // Alternate row colors for readability
                for ($row = 4; $row <= $lastRow; $row++) {
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
                $sheet->getStyle("A4:{$lastColumn}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Freeze the header row
                $sheet->freezePane('A4');

                // Set column widths for better readability
                $sheet->getColumnDimension('A')->setWidth(8);  // ID
                $sheet->getColumnDimension('B')->setWidth(18); // Submitted At
                $sheet->getColumnDimension('C')->setWidth(20); // Respondent Name
                $sheet->getColumnDimension('D')->setWidth(25); // Email
                $sheet->getColumnDimension('E')->setWidth(12); // Duration
            },
        ];
    }

    protected function getLastColumn(): string
    {
        $columnCount = 5 + count($this->allQuestions); // 5 base columns + questions

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
