<?php

namespace App\Filament\Admin\Resources\Questions;

use App\Filament\Admin\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Admin\Resources\Questions\Pages\EditQuestion;
use App\Filament\Admin\Resources\Questions\Pages\ListQuestions;
use App\Filament\Admin\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Admin\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Surveys';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return QuestionForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
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
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
        ];
    }
}


