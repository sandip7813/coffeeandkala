<?php

use App\Support\ImageProcessingMemory;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->originalMemoryLimit = ini_get('memory_limit');
});

afterEach(function () {
    ini_set('memory_limit', $this->originalMemoryLimit);
});

test('it raises a memory limit that is below the configured ceiling', function () {
    ini_set('memory_limit', '128M');
    config(['media.processing_memory_limit' => '512M']);

    ImageProcessingMemory::raise();

    expect(ini_get('memory_limit'))->toBe('512M');
});

test('it never lowers a memory limit that is already higher', function (string $current) {
    ini_set('memory_limit', $current);
    config(['media.processing_memory_limit' => '512M']);

    ImageProcessingMemory::raise();

    expect(ini_get('memory_limit'))->toBe($current);
})->with(['1G', '-1']);
