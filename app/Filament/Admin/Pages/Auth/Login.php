<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $layout = 'filament-panels::components.layout.base';

    public function getView(): string
    {
        return 'filament.admin.auth.login';
    }
}

