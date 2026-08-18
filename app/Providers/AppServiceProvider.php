<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // The admin panel is Bootstrap 5 (AdminLTE); Laravel's default Tailwind
        // pagination view renders unstyled/broken inside it.
        Paginator::useBootstrapFive();

        Gate::define('manage-brand', fn (?User $user): bool => $user?->isSuperAdmin() === true);
        Gate::define('manage-artisan', fn (?User $user): bool => $user?->isSuperAdmin() === true);
    }
}
