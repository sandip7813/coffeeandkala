<?php

use App\Actions\Quotes\EnsureQuoteScheduledForDate;
use App\Http\Controllers\Admin\ArtisanController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FeatureArticleController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\JournalArticleController;
use App\Http\Controllers\Admin\MetaController;
use App\Http\Controllers\Admin\OurStoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PoemController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\QuoteScheduleController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\GalleryController as FrontendGalleryController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\OurStoryController as FrontendOurStoryController;
use App\Http\Controllers\PoetryController;
use App\Http\Controllers\StudioController as FrontendStudioController;
use App\Models\HomeSectionArticle;
use App\Models\HomeSectionMedia;
use App\Models\Meta;
use App\Support\FeatureCatalog;
use App\Support\HomeMediaSections;
use App\Support\HomeSections;
use App\Support\JournalCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function (EnsureQuoteScheduledForDate $ensureQuoteScheduledForDate) {
    $quote = $ensureQuoteScheduledForDate->handle(Carbon::today())->quote;

    $homeSections = collect(HomeSectionArticle::SECTIONS)
        ->mapWithKeys(fn (string $section) => [$section => HomeSections::picks($section)]);

    return view('frontend.home', [
        'quote' => $quote,
        'latestPieces' => $homeSections[HomeSectionArticle::SECTION_LATEST_PIECES],
        'theSelection' => $homeSections[HomeSectionArticle::SECTION_THE_SELECTION],
        'homeFeatures' => $homeSections[HomeSectionArticle::SECTION_FEATURES],
        'homeJournal' => $homeSections[HomeSectionArticle::SECTION_JOURNAL],
        'homeGallery' => HomeMediaSections::picks(HomeSectionMedia::SECTION_GALLERY),
        'homeStudio' => HomeMediaSections::picks(HomeSectionMedia::SECTION_STUDIO),
        'meta' => Meta::forPage('home'),
    ]);
})->name('home');

Route::get('/our-story', [FrontendOurStoryController::class, 'index'])->name('about');

Route::get('/gallery', [FrontendGalleryController::class, 'index'])->name('gallery');

Route::get('/studio', [FrontendStudioController::class, 'index'])->name('studio');

Route::get('/journal', [JournalController::class, 'index'])->name('journal');
Route::get('/journal/{category}', [JournalController::class, 'show'])
    ->whereIn('category', JournalCatalog::categorySlugs())
    ->name('journal.category');
Route::get('/journal/{category}/{article}', [JournalController::class, 'showArticle'])
    ->whereIn('category', JournalCatalog::categorySlugs())
    ->name('journal.article');

Route::get('/features', [FeatureController::class, 'index'])->name('features');
Route::get('/features/{category}', [FeatureController::class, 'show'])
    ->whereIn('category', FeatureCatalog::slugs())
    ->name('features.show');
Route::get('/features/{category}/{article}', [FeatureController::class, 'showArticle'])
    ->whereIn('category', FeatureCatalog::slugs())
    ->name('features.article');

Route::get('/poetry', [PoetryController::class, 'index'])->name('poetry');
Route::get('/poetry/{poem}', [PoetryController::class, 'show'])->name('poetry.show');

// AdminLTE authentication routes (public registration disabled — admins are created by super admin)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::get('password/force', [ForcePasswordChangeController::class, 'edit'])->name('password.force.edit');
    Route::put('password/force', [ForcePasswordChangeController::class, 'update'])->name('password.force.update');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'password.changed', 'permission:view-dashboard'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    // Temporary diagnostic: confirms the scheduler is actually running on prod.
    // Remove once verified.
    Route::get('scheduler-check', function () {
        $lastRun = Cache::get('scheduler-heartbeat-last-run');

        return response()->json([
            'last_run' => $lastRun,
            'seconds_ago' => $lastRun ? now()->diffInSeconds($lastRun) : null,
            'looks_healthy' => $lastRun && now()->diffInSeconds($lastRun) < 120,
        ]);
    })->name('scheduler.check');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::middleware('permission:manage-users')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::put('users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status.update');
        Route::post('users/{user}/resend-one-time-password', [UserController::class, 'resendOneTimePassword'])->name('users.otp.resend');
        Route::put('users/{user}/photo', [UserController::class, 'updatePhoto'])->name('users.photo.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:manage-permissions')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    Route::middleware('can:manage-artisan')->group(function () {
        Route::get('artisan', [ArtisanController::class, 'index'])->name('artisan.index');
        Route::post('artisan/unlock', [ArtisanController::class, 'unlock'])
            ->middleware('throttle:10,1')
            ->name('artisan.unlock');
        Route::post('artisan', [ArtisanController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('artisan.run');
    });

    // Authorization for each action lives in CategoryController: viewing the
    // list only needs one of the two granular permissions below, so it isn't
    // gated by route middleware here.
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
        ->middleware('can:edit-categories')->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])
        ->middleware('can:edit-categories')->name('categories.update');
    Route::put('categories/{category}/status', [CategoryController::class, 'updateStatus'])
        ->middleware('can:change-category-status')->name('categories.status.update');

    Route::middleware('can:manage-brand')->group(function () {
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo.update');
        Route::put('settings/social', [SettingsController::class, 'updateSocial'])->name('settings.social.update');
        Route::put('settings/contact', [SettingsController::class, 'updateContact'])->name('settings.contact.update');
    });

    Route::middleware('can:manage-home-sections')->group(function () {
        Route::get('home-sections', [HomeSectionController::class, 'edit'])->name('home-sections.edit');
        Route::get('home-sections/search-articles', [HomeSectionController::class, 'searchArticles'])->name('home-sections.search-articles');
        Route::put('home-sections/{section}', [HomeSectionController::class, 'update'])->name('home-sections.update');
        Route::put('home-sections-meta', [HomeSectionController::class, 'updateMeta'])->name('home-sections.meta.update');
    });

    Route::middleware('can:manage-our-story')->group(function () {
        Route::get('our-story', [OurStoryController::class, 'edit'])->name('our-story.edit');
        Route::put('our-story', [OurStoryController::class, 'update'])->name('our-story.update');
        Route::put('our-story/{article}/sections/reorder', [OurStoryController::class, 'reorderSections'])->name('our-story.sections.reorder');
        Route::put('our-story/{article}/sections/{section}/status', [OurStoryController::class, 'updateSectionStatus'])->name('our-story.sections.status.update');
        Route::put('our-story-meta', [OurStoryController::class, 'updateMeta'])->name('our-story.meta.update');
    });

    Route::middleware('can:manage-meta')->group(function () {
        Route::get('meta', [MetaController::class, 'edit'])->name('meta.edit');
        Route::put('meta/page/{key}', [MetaController::class, 'updatePage'])->name('meta.page.update');
        Route::get('meta/content/{type}', [MetaController::class, 'contentList'])->name('meta.content.list');
        Route::put('meta/content/{type}/{id}', [MetaController::class, 'updateContentMeta'])->name('meta.content.update');
    });

    // Each action below is gated by its own granular permission (checked in the
    // controller / form request), so route middleware only applies where a
    // single permission maps cleanly to a single route.
    Route::middleware('can:assign-quote-dates')->group(function () {
        Route::get('quotes/schedule', [QuoteScheduleController::class, 'index'])->name('quotes.schedule.index');
        Route::put('quotes/schedule/{date}', [QuoteScheduleController::class, 'update'])
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('quotes.schedule.update');
        Route::put('quotes/{quote}/assign-dates', [QuoteController::class, 'assignDates'])->name('quotes.assign-dates');
    });

    // Editing and deleting are gated in the controller / form request instead of
    // here: a quote's own creator may always edit or delete it, and the
    // 'edit-quotes'/'delete-quotes' permissions are only required to manage
    // quotes created by someone else.
    Route::get('quotes', [QuoteController::class, 'index'])->middleware('can:view-quotes')->name('quotes.index');
    Route::get('quotes/create', [QuoteController::class, 'create'])->middleware('can:create-quotes')->name('quotes.create');
    Route::post('quotes', [QuoteController::class, 'store'])->middleware('can:create-quotes')->name('quotes.store');
    Route::get('quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');

    // Gallery and Studio share identical functionality (see MediaFileController);
    // each action is gated by its own granular '*-gallery'/'*-studio' permission
    // checked in the controller, and approval is restricted to super admins
    // directly (not a delegable permission), so no route middleware is used here.
    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::get('gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::get('gallery/{media}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
    Route::put('gallery/{media}', [GalleryController::class, 'update'])->name('gallery.update');
    Route::put('gallery/{media}/status', [GalleryController::class, 'updateStatus'])->name('gallery.status.update');
    Route::put('gallery/{media}/approve', [GalleryController::class, 'approve'])->name('gallery.approve');
    Route::put('gallery/{media}/home-section', [GalleryController::class, 'toggleHomeSection'])->name('gallery.home-section.toggle');
    Route::delete('gallery/{media}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('studio', [StudioController::class, 'index'])->name('studio.index');
    Route::get('studio/create', [StudioController::class, 'create'])->name('studio.create');
    Route::post('studio', [StudioController::class, 'store'])->name('studio.store');
    Route::get('studio/{media}/edit', [StudioController::class, 'edit'])->name('studio.edit');
    Route::put('studio/{media}', [StudioController::class, 'update'])->name('studio.update');
    Route::put('studio/{media}/status', [StudioController::class, 'updateStatus'])->name('studio.status.update');
    Route::put('studio/{media}/approve', [StudioController::class, 'approve'])->name('studio.approve');
    Route::put('studio/{media}/home-section', [StudioController::class, 'toggleHomeSection'])->name('studio.home-section.toggle');
    Route::delete('studio/{media}', [StudioController::class, 'destroy'])->name('studio.destroy');

    Route::get('poetry', [PoemController::class, 'index'])->name('poetry.index');
    Route::get('poetry/create', [PoemController::class, 'create'])->name('poetry.create');
    Route::post('poetry', [PoemController::class, 'store'])->name('poetry.store');
    Route::get('poetry/{poem}/edit', [PoemController::class, 'edit'])->name('poetry.edit');
    Route::put('poetry/{poem}', [PoemController::class, 'update'])->name('poetry.update');
    Route::put('poetry/{poem}/status', [PoemController::class, 'updateStatus'])->name('poetry.status.update');
    Route::put('poetry/{poem}/approve', [PoemController::class, 'approve'])->name('poetry.approve');
    Route::delete('poetry/{poem}', [PoemController::class, 'destroy'])->name('poetry.destroy');
    Route::put('poetry-meta', [PoemController::class, 'updateMeta'])->name('poetry.meta.update');

    // Features and Journals share identical functionality (see
    // AbstractArticleController); each action is gated by its own granular
    // '*-features'/'*-journals' permission checked in the controller, and
    // approval is restricted to super admins directly (not a delegable
    // permission), so no route middleware is used here.
    Route::get('features/search-creators', [FeatureArticleController::class, 'searchCreators'])->name('features.search-creators');
    Route::get('features', [FeatureArticleController::class, 'index'])->name('features.index');
    Route::get('features/create', [FeatureArticleController::class, 'create'])->name('features.create');
    Route::post('features', [FeatureArticleController::class, 'store'])->name('features.store');
    Route::get('features/{article}/edit', [FeatureArticleController::class, 'edit'])->name('features.edit');
    Route::put('features/{article}', [FeatureArticleController::class, 'update'])->name('features.update');
    Route::put('features/{article}/status', [FeatureArticleController::class, 'updateStatus'])->name('features.status.update');
    Route::put('features/{article}/sections/reorder', [FeatureArticleController::class, 'reorderSections'])->name('features.sections.reorder');
    Route::put('features/{article}/sections/{section}/status', [FeatureArticleController::class, 'updateSectionStatus'])->name('features.sections.status.update');
    Route::put('features/{article}/approve', [FeatureArticleController::class, 'approve'])->name('features.approve');
    Route::put('features/{article}/home-sections/{section}', [FeatureArticleController::class, 'toggleHomeSection'])->name('features.home-sections.toggle');
    Route::delete('features/{article}', [FeatureArticleController::class, 'destroy'])->name('features.destroy');

    Route::get('journals/search-creators', [JournalArticleController::class, 'searchCreators'])->name('journals.search-creators');
    Route::get('journals', [JournalArticleController::class, 'index'])->name('journals.index');
    Route::get('journals/create', [JournalArticleController::class, 'create'])->name('journals.create');
    Route::post('journals', [JournalArticleController::class, 'store'])->name('journals.store');
    Route::get('journals/{article}/edit', [JournalArticleController::class, 'edit'])->name('journals.edit');
    Route::put('journals/{article}', [JournalArticleController::class, 'update'])->name('journals.update');
    Route::put('journals/{article}/status', [JournalArticleController::class, 'updateStatus'])->name('journals.status.update');
    Route::put('journals/{article}/sections/reorder', [JournalArticleController::class, 'reorderSections'])->name('journals.sections.reorder');
    Route::put('journals/{article}/sections/{section}/status', [JournalArticleController::class, 'updateSectionStatus'])->name('journals.sections.status.update');
    Route::put('journals/{article}/approve', [JournalArticleController::class, 'approve'])->name('journals.approve');
    Route::put('journals/{article}/home-sections/{section}', [JournalArticleController::class, 'toggleHomeSection'])->name('journals.home-sections.toggle');
    Route::delete('journals/{article}', [JournalArticleController::class, 'destroy'])->name('journals.destroy');
});
