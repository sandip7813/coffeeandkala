<?php

use App\Actions\Quotes\EnsureQuoteScheduledForDate;
use App\Http\Controllers\Admin\ArtisanController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\QuoteScheduleController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PoetryController;
use App\Support\FeatureCatalog;
use App\Support\GalleryCatalog;
use App\Support\JournalCatalog;
use App\Support\PoetryCatalog;
use App\Support\StudioCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function (EnsureQuoteScheduledForDate $ensureQuoteScheduledForDate) {
    $quote = $ensureQuoteScheduledForDate->handle(Carbon::today())->quote;

    return view('frontend.home', compact('quote'));
})->name('home');

Route::get('/our-story', function () {
    return view('frontend.about');
})->name('about');

Route::get('/gallery', function () {
    $plates = GalleryCatalog::all();

    return view('frontend.gallery', ['plates' => $plates]);
})->name('gallery');

Route::get('/studio', function () {
    $works = StudioCatalog::all();

    return view('frontend.studio', ['works' => $works]);
})->name('studio');

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
Route::get('/poetry/{poem}', [PoetryController::class, 'show'])
    ->whereIn('poem', PoetryCatalog::slugs())
    ->name('poetry.show');

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
});
