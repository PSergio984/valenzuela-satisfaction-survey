<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Check if user is trying to edit themselves
        $currentUser = Auth::user();
        if ($currentUser && $this->record->id === $currentUser->id) {
            Notification::make()
                ->warning()
                ->title('Access Denied')
                ->body('You cannot edit your own account.')
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }

        // Use policy to check if user can edit this record
        if ($currentUser && !$currentUser->can('update', $this->record)) {
            Notification::make()
                ->warning()
                ->title('Access Denied')
                ->body('You do not have permission to edit this user.')
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    if ($this->record->hasRole('super_admin')) {
                        throw new \Exception('Cannot delete super admin users.');
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
