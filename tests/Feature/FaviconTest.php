<?php

test('favicon assets are publicly available', function () {
    expect(file_exists(public_path('favicon.ico')))->toBeTrue()
        ->and(filesize(public_path('favicon.ico')))->toBeGreaterThan(0)
        ->and(file_exists(public_path('favicon-32x32.png')))->toBeTrue()
        ->and(file_exists(public_path('favicon-16x16.png')))->toBeTrue()
        ->and(file_exists(public_path('apple-touch-icon.png')))->toBeTrue();
});

test('the home page includes favicon link tags', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(asset('favicon.ico'), false);
    $response->assertSee(asset('favicon-32x32.png'), false);
    $response->assertSee(asset('apple-touch-icon.png'), false);
});
