<?php

namespace App\Providers;

use App\Filament\Admin\Widgets\LatestResponsesWidget;
use App\Filament\Admin\Widgets\PremiumResponsesChart;
use App\Filament\Admin\Widgets\RatingsChart;
use App\Filament\Admin\Widgets\SurveyStatsWidget;
use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the RolePolicy for Spatie's Role model
        Gate::policy(Role::class, RolePolicy::class);

        // Super admin bypass - grant all permissions to super_admin role
        Gate::before(function ($user, $ability, $args) {
            if (isset($args[0]) && $args[0] instanceof \Spatie\Permission\Models\Role && $args[0]->name === 'super_admin') {
                if (in_array($ability, ['update', 'delete', 'forceDelete', 'replicate'])) {
                    return null;
                }
            }
            return $user->hasRole('super_admin') ? true : null;
        });

        // Register Observers
        \App\Models\Answer::observe(\App\Observers\AnswerObserver::class);
    }
}
