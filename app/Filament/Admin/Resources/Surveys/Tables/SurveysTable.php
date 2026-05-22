<?php

namespace App\Filament\Admin\Resources\Surveys\Tables;

use App\Models\Survey;
use App\Services\QrCodeService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class SurveysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('questions_count')
                    ->label('Questions')
                    ->counts('questions')
                    ->sortable(),

                TextColumn::make('responses_count')
                    ->label('Submissions')
                    ->counts('responses')
                    ->sortable(),

                TextColumn::make('answers_avg_value')
                    ->label('Avg. Rating')
                    ->getStateUsing(fn (Survey $record) => number_format($record->answers_avg_value ?? 0, 1))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state > 0 => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->actions([
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading(fn (Survey $record): string => "QR Code: {$record->title}")
                    ->modalContent(function (Survey $record): View {
                        $qrService = app(QrCodeService::class);

                        return view('filament.modals.qr-code', [
                            'survey' => $record,
                            'qrCode' => $qrService->generateSvg($record, 100),
                            'qrCodeRaw' => $qrService->generateRawSvg($record, 100),
                            'surveyUrl' => $qrService->getSurveyUrl($record),
                        ]);
                    })
                    ->modalWidth('sm')
                    ->slideOver(false)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
