<?php

use App\Models\Setting;
use App\Support\BrandLogo;

test('default brand logo is the monogram mark', function () {
    expect(BrandLogo::currentKey())->toBe('mark')
        ->and(BrandLogo::path())->toBe('images/logo/gk-mark.png')
        ->and(BrandLogo::width())->toBe(256)
        ->and(BrandLogo::height())->toBe(256)
        ->and(is_file(BrandLogo::absolutePath()))->toBeTrue()
        ->and(is_file(BrandLogo::emailAbsolutePath()))->toBeTrue();
});

test('setting a logo updates the stored preference', function () {
    BrandLogo::set('wordmark');

    expect(BrandLogo::currentKey())->toBe('wordmark')
        ->and(BrandLogo::path())->toBe('images/logo/logo-wordmark.png')
        ->and(Setting::getValue(BrandLogo::SETTING_KEY))->toBe('wordmark')
        ->and(is_file(BrandLogo::absolutePath()))->toBeTrue();
});

test('unknown logo keys fall back to the default', function () {
    Setting::setValue(BrandLogo::SETTING_KEY, 'missing');

    expect(BrandLogo::currentKey())->toBe('mark');
});

test('unknown logo keys cannot be set', function () {
    BrandLogo::set('missing');
})->throws(InvalidArgumentException::class);
