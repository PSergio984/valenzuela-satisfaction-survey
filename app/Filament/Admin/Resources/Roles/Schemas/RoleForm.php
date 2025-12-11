<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    /**
     * Define permission categories with their prefixes.
     */
    protected static array $permissionCategories = [
        'Questions' => ['create_questions', 'view_questions', 'edit_questions', 'delete_questions'],
        'Surveys' => ['create_surveys', 'view_surveys', 'edit_surveys', 'delete_surveys'],
        'Responses' => ['view_responses', 'delete_responses', 'export_responses'],
        'Users' => ['create_users', 'view_users', 'edit_users', 'delete_users'],
        'Roles' => ['create_roles', 'view_roles', 'edit_roles', 'delete_roles'],
        'Permissions' => ['view_permissions', 'assign_permissions'],
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Use snake_case for role names (e.g., super_admin)'),
                    ]),

                Section::make('Permissions')
                    ->description('Select permissions for this role')
                    ->schema(static::buildPermissionSchema()),
            ]);
    }

    protected static function buildPermissionSchema(): array
    {
        $schema = [];

        foreach (static::$permissionCategories as $category => $permissionNames) {
            $schema[] = Fieldset::make($category.' Permissions')
                ->schema([
                    CheckboxList::make('permissions')
                        ->relationship(
                            name: 'permissions',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereIn('name', $permissionNames)->orderBy('name')
                        )
                        ->getOptionLabelFromRecordUsing(fn (Permission $record) => str($record->name)
                            ->replace('_', ' ')
                            ->title()
                            ->toString())
                        ->columns(2)
                        ->bulkToggleable(),
                ])
                ->columns(1);
        }

        return $schema;
    }
}
