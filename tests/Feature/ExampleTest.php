<?php

use App\Support\BrandLogo;

test('the home page returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(BrandLogo::path(), false);
    $response->assertSee('IN THE HEART OF JAIPUR', false);
    $response->assertSee('Stay Close', false);
});
