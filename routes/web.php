<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FeatureController;
use App\Support\FeatureCatalog;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.home');
})->name('home');

Route::get('/our-story', function () {
    return view('frontend.about');
})->name('about');

Route::get('/gallery', function () {
    $plates = [
        [
            'id' => 'plate-01',
            'number' => '01',
            'title' => 'The Last Light Over the Northern Rail Corridor at Dusk',
            'description' => 'A silhouette held against gold — the quiet choreography of leaving and arriving.',
            'location' => 'Northern Rail Corridor',
            'src' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=400',
        ],
        [
            'id' => 'plate-02',
            'number' => '02',
            'title' => 'Crimson Passage',
            'description' => 'Prayer flags and stone walls: a single figure walking into the afternoon light.',
            'location' => 'Old Quarter, Himalayan Foothills',
            'src' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=400',
        ],
        [
            'id' => 'plate-03',
            'number' => '03',
            'title' => 'Lone Oar',
            'description' => 'Mist on still water. One boat, one rower, and the soft insistence of dawn.',
            'location' => 'Backwaters at First Light',
            'src' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=400',
        ],
        [
            'id' => 'plate-04',
            'number' => '04',
            'title' => 'Weathered Light',
            'description' => 'A face that has kept seasons. Eyes that refuse to look away from the story.',
            'location' => 'Portrait Study',
            'src' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=400',
        ],
        [
            'id' => 'plate-05',
            'number' => '05',
            'title' => 'Morning Ritual',
            'description' => 'Steam rising between pages — where the day begins before the world asks for anything.',
            'location' => 'Studio Kitchen',
            'src' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=400',
        ],
        [
            'id' => 'plate-06',
            'number' => '06',
            'title' => 'Market Breath',
            'description' => 'Spice, cloth, and conversation layered like a living collage.',
            'location' => 'Bazaar Lane',
            'src' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=400',
        ],
        [
            'id' => 'plate-07',
            'number' => '07',
            'title' => 'Valley After Rain',
            'description' => 'Clouds lift like a curtain. The land remembers every drop.',
            'location' => 'Western Ghats',
            'src' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=400',
        ],
        [
            'id' => 'plate-08',
            'number' => '08',
            'title' => 'Ink & Ember',
            'description' => 'A desk mid-thought — typewriter keys waiting for the next true sentence.',
            'location' => 'Writer\'s Corner',
            'src' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=400',
        ],
        [
            'id' => 'plate-09',
            'number' => '09',
            'title' => 'Lantern Hour',
            'description' => 'Night softens the edges. Light becomes a companion rather than a spectacle.',
            'location' => 'Festival Street',
            'src' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=400',
        ],
        [
            'id' => 'plate-10',
            'number' => '10',
            'title' => 'Empty Road',
            'description' => 'Horizon as invitation. The kind of silence that makes room for wondering.',
            'location' => 'Desert Highway',
            'src' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=400',
        ],
        [
            'id' => 'plate-11',
            'number' => '11',
            'title' => 'Rain on Glass',
            'description' => 'The city dissolves into watercolor — travel as a soft blur of elsewhere.',
            'location' => null,
            'src' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=400',
        ],
        [
            'id' => 'plate-12',
            'number' => '12',
            'title' => 'Circle of Hands',
            'description' => 'Shared tables, shared stories — the community the frame cannot fully hold.',
            'location' => 'Gathering Table',
            'src' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=400',
        ],
    ];

    return view('frontend.gallery', ['plates' => $plates]);
})->name('gallery');

Route::get('/studio', function () {
    $works = [
        [
            'id' => 'work-01',
            'number' => '01',
            'title' => 'Ember Fields',
            'description' => 'Thick pigment laid in blocks — orange, charcoal, and the pale quiet between them.',
            'medium' => 'Acrylic on canvas',
            'src' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=400',
        ],
        [
            'id' => 'work-02',
            'number' => '02',
            'title' => 'Clay Memory',
            'description' => 'A vessel that remembers the wheel — soft curves holding shadow like breath.',
            'medium' => 'Stoneware',
            'src' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=400',
        ],
        [
            'id' => 'work-03',
            'number' => '03',
            'title' => 'Paper Weather',
            'description' => 'Torn edges and wash — colour behaving like cloud over unfinished ground.',
            'medium' => 'Ink & wash on paper',
            'src' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=400',
        ],
        [
            'id' => 'work-04',
            'number' => '04',
            'title' => 'Ochre Gesture',
            'description' => 'One decisive stroke across linen — experiment held still long enough to look.',
            'medium' => 'Oil on linen',
            'src' => 'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=400',
        ],
        [
            'id' => 'work-05',
            'number' => '05',
            'title' => 'Still Life, Unstill',
            'description' => 'Fruit, cloth, and afternoon light negotiating who gets to speak first.',
            'medium' => 'Oil study',
            'src' => 'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?q=80&w=400',
        ],
        [
            'id' => 'work-06',
            'number' => '06',
            'title' => 'Blue Threshold',
            'description' => 'A cool field that opens like a door — colour as invitation, not spectacle.',
            'medium' => 'Mixed media',
            'src' => 'https://images.unsplash.com/photo-1549490349-8643362247b5?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1549490349-8643362247b5?q=80&w=400',
        ],
        [
            'id' => 'work-07',
            'number' => '07',
            'title' => 'Line & Breath',
            'description' => 'Minimal marks on warm paper — the studio learning how little is enough.',
            'medium' => 'Graphite on paper',
            'src' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=400',
        ],
        [
            'id' => 'work-08',
            'number' => '08',
            'title' => 'Fired Silence',
            'description' => 'Ceramic forms in conversation — matte skins catching soft gallery light.',
            'medium' => 'Ceramic sculpture',
            'src' => 'https://images.unsplash.com/photo-1605721911519-3dfeb3be25e7?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1605721911519-3dfeb3be25e7?q=80&w=400',
        ],
        [
            'id' => 'work-09',
            'number' => '09',
            'title' => 'Palette Afternoon',
            'description' => 'Pigments waiting their turn — imagination still wet on the board.',
            'medium' => 'Studio study',
            'src' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=400',
        ],
        [
            'id' => 'work-10',
            'number' => '10',
            'title' => 'Warm Geometry',
            'description' => 'Planes of terracotta and cream — contemporary calm with a handmade edge.',
            'medium' => 'Acrylic on panel',
            'src' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=400',
        ],
        [
            'id' => 'work-11',
            'number' => '11',
            'title' => 'Leaf Shadow',
            'description' => 'Natural light drawing on plaster — the studio itself as a temporary work.',
            'medium' => 'Light study',
            'src' => 'https://images.unsplash.com/photo-1482160549825-59d1b23cb208?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1482160549825-59d1b23cb208?q=80&w=400',
        ],
        [
            'id' => 'work-12',
            'number' => '12',
            'title' => 'After Colour',
            'description' => 'What remains when the brush is rinsed — residue as quiet expression.',
            'medium' => 'Watercolour',
            'src' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=1600',
            'thumb' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=400',
        ],
    ];

    return view('frontend.studio', ['works' => $works]);
})->name('studio');

Route::get('/journal', function () {
    $entries = [
        [
            'role' => 'lead',
            'tag' => 'Travel Diaries',
            'category_id' => 'travel-diaries',
            'title' => 'Letters from a Slow Train',
            'excerpt' => 'Windows blur into watercolour somewhere between stations. A story finds its pace in the clatter of rails, the steam of a borrowed thermos, and the quiet courage of going nowhere in a hurry. This dispatch follows the long way — the seats that face backwards, the towns that pass without announcement, and the sentences that only arrive when the landscape softens.',
            'caption' => 'Afternoon light along the northern corridor.',
            'date' => '2026-02-28',
            'date_label' => '28 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1400',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Destination Guides',
            'category_id' => 'destination-guides',
            'title' => 'Where the Coast Still Speaks Softly',
            'excerpt' => 'Cliff light, salt air, and the kind of afternoon that asks you to put the map away. A guide for those who prefer the quieter shore.',
            'date' => '2026-01-30',
            'date_label' => '30 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Local Stories',
            'category_id' => 'local-stories',
            'title' => 'The Colour of Quiet Markets',
            'excerpt' => 'Spice, cloth, and conversation — a living collage of mornings that refuse haste, and vendors who know your order before you speak.',
            'date' => '2026-02-14',
            'date_label' => '14 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Life on the Road',
            'category_id' => 'life-on-the-road',
            'title' => 'Brewing Between Pages',
            'excerpt' => 'Steam rising over unfinished sentences. The day begins before the world asks for anything, one pour at a time.',
            'date' => '2026-02-02',
            'date_label' => '02 Feb 2026',
            'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Photography',
            'category_id' => null,
            'title' => 'Valley After Rain',
            'excerpt' => 'Clouds lift like a curtain. The land remembers every drop, and the road shines just long enough to invite another mile.',
            'date' => '2026-01-05',
            'date_label' => '05 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Culture',
            'category_id' => null,
            'title' => 'Lantern Hour in the Old Quarter',
            'excerpt' => 'Night softens the edges. Light becomes a companion rather than a spectacle, and conversation returns to the courtyard.',
            'date' => '2025-12-29',
            'date_label' => '29 Dec 2025',
            'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Travel Diaries',
            'category_id' => null,
            'title' => 'Empty Road, Full Horizon',
            'excerpt' => 'Horizon as invitation. The kind of silence that makes room for wondering what the next town will smell like.',
            'date' => '2025-12-12',
            'date_label' => '12 Dec 2025',
            'image' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Photo Essay',
            'category_id' => null,
            'title' => 'Weathered Light',
            'excerpt' => 'A portrait study from the road — eyes that refuse to look away from the story, hands that have held weather and work.',
            'date' => '2025-11-08',
            'date_label' => '08 Nov 2025',
            'image' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Dispatch',
            'category_id' => null,
            'title' => 'Rain on Glass',
            'excerpt' => 'The city dissolves into watercolour — travel as a soft blur of elsewhere, seen from a window seat.',
            'date' => '2025-10-30',
            'date_label' => '30 Oct 2025',
            'image' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'feature',
            'tag' => 'Local Stories',
            'category_id' => null,
            'title' => 'Market Breath at First Light',
            'excerpt' => 'Spice, cloth, and conversation layered like a living collage — the city waking one stall at a time.',
            'date' => '2025-11-22',
            'date_label' => '22 Nov 2025',
            'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1000',
            'href' => '#',
        ],
        [
            'role' => 'column',
            'tag' => 'Essay',
            'category_id' => null,
            'title' => 'A Note from a Rainy Evening',
            'excerpt' => 'Raindrops, old songs and a notebook. The perfect recipe for clarity when the world softens outside the window.',
            'date' => '2026-03-12',
            'date_label' => '12 Mar 2026',
            'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=900',
            'href' => '#',
        ],
        [
            'role' => 'column',
            'tag' => 'Notes',
            'category_id' => null,
            'title' => 'Midnight Margins',
            'excerpt' => 'Ink still drying. Thoughts that only arrive when the house has gone quiet and the kettle has one last whisper left.',
            'date' => '2026-01-09',
            'date_label' => '09 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=900',
            'href' => '#',
        ],
        [
            'role' => 'brief',
            'tag' => 'Travel Diaries',
            'category_id' => null,
            'title' => 'Holding Soft Light',
            'excerpt' => 'A frame that waits. The kind of silence that makes room for wondering.',
            'date' => '2026-01-21',
            'date_label' => '21 Jan 2026',
            'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=900',
            'href' => '#',
        ],
        [
            'role' => 'brief',
            'tag' => 'Destination Guides',
            'category_id' => null,
            'title' => 'Bicycle Against the Old Door',
            'excerpt' => 'A street corner that remembers every departure.',
            'date' => '2025-12-18',
            'date_label' => '18 Dec 2025',
            'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?q=80&w=900',
            'href' => '#',
        ],
    ];

    return view('frontend.journal', [
        'entries' => $entries,
    ]);
})->name('journal');

Route::get('/features', [FeatureController::class, 'index'])->name('features');
Route::get('/features/{category}', [FeatureController::class, 'show'])
    ->whereIn('category', FeatureCatalog::slugs())
    ->name('features.show');

// AdminLTE authentication routes (public registration disabled — admins are created by super admin)
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission:view-dashboard'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

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
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
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
});
