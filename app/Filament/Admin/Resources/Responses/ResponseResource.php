<?php

namespace App\Filament\Admin\Resources\Responses;

use App\Filament\Admin\Resources\Responses\Pages\ListResponses;
use App\Filament\Admin\Resources\Responses\Pages\ViewResponse;
use App\Filament\Admin\Resources\Responses\Schemas\ResponseForm;
use App\Filament\Admin\Resources\Responses\Tables\ResponsesTable;
use App\Models\Response;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Surveys';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Responses';

    public static function form(Schema $schema): Schema
    {
        return ResponseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResponsesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResponses::route('/'),
            'view' => ViewResponse::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
