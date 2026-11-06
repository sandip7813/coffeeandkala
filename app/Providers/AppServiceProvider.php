<?php

namespace App\Providers;

use App\Models\User;
use App\Support\UploadFailureMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator as ValidationValidator;

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

        // `@seo($meta, $title, $description, $keywords)` — sets a frontend
        // page's title/meta description/meta keywords sections in one call
        // (falling back to the given defaults when $meta is null or has no
        // value saved), instead of three separate @section calls with the
        // same fallback chain repeated in every page (see App\Support\SeoMeta).
        Blade::directive('seo', fn (string $expression): string => "<?php \App\Support\SeoMeta::set({$expression}); ?>");

        // A file PHP itself rejects (e.g. larger than upload_max_filesize)
        // fails the implicit `uploaded` rule with a generic "failed to
        // upload" message — say why instead (see App\Support\UploadFailureMessage).
        Validator::replacer('uploaded', fn (string $message, string $attribute, string $rule, array $parameters, ValidationValidator $validator): string => UploadFailureMessage::for($message, $attribute, $validator));
    }
}
