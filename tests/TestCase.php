<?php

namespace Tests;

use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    /**
     * Categories back the live Feature/Journal navigation, so every test
     * that hits those routes needs them seeded — not just the admin ones.
     */
    protected bool $seed = true;

    protected string $seeder = CategorySeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }
}
