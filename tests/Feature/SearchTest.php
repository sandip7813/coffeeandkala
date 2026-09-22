<?php

use App\Models\Article;
use App\Models\MediaFile;
use App\Models\Poem;

it('returns no suggestions for a query shorter than 3 characters', function () {
    Poem::factory()->active()->create(['title' => 'Rain on Tin Roofs']);

    $response = $this->getJson('/search/suggest?q=ra');

    $response->assertOk()->assertJson(['sections' => []]);
});

it('returns categorized suggestions matching the title across sections', function () {
    Poem::factory()->active()->create(['title' => 'Rain on Tin Roofs']);
    Poem::factory()->active()->create(['title' => 'Sunlit Mornings']);
    Article::factory()->journal()->active()->create(['title' => 'Rainy Season Diaries']);
    MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->active()->create(['title' => 'Rain Drops']);

    $response = $this->getJson('/search/suggest?q=rain');

    $response->assertOk();

    $sections = collect($response->json('sections'))->keyBy('key');

    expect($sections->has('poetry'))->toBeTrue();
    expect($sections['poetry']['total'])->toBe(1);
    expect($sections['poetry']['items'][0]['title'])->toBe('Rain on Tin Roofs');

    expect($sections->has('journal'))->toBeTrue();
    expect($sections['journal']['items'][0]['title'])->toBe('Rainy Season Diaries');

    expect($sections->has('gallery'))->toBeTrue();
    expect($sections['gallery']['items'][0]['title'])->toBe('Rain Drops');

    expect($sections->has('features'))->toBeFalse();
    expect($sections->has('studio'))->toBeFalse();
});

it('excludes inactive content from suggestions', function () {
    Poem::factory()->inactive()->create(['title' => 'Rain on Tin Roofs']);
    Poem::factory()->active()->create(['title' => 'Rain and Ruin']);

    $response = $this->getJson('/search/suggest?q=rain');

    $sections = collect($response->json('sections'))->keyBy('key');

    expect($sections['poetry']['total'])->toBe(1);
    expect($sections['poetry']['items'][0]['title'])->toBe('Rain and Ruin');
});

it('points gallery and studio suggestions at the index page with an item query string', function () {
    $media = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->active()->create(['title' => 'Studio Rainlight']);

    $response = $this->getJson('/search/suggest?q=rain');

    $item = collect($response->json('sections'))->firstWhere('key', 'studio')['items'][0];

    expect($item['url'])->toBe(route('studio', ['item' => $media->uuid]));
    expect($item['target'])->toBe('lightbox');
});

it('renders the search results page grouped and paginated by section', function () {
    Poem::factory()->active()->count(3)->create(['title' => fn () => 'Rain '.fake()->unique()->word()]);

    $response = $this->get('/search?q=rain');

    $response->assertOk();
    $response->assertViewIs('frontend.search');
    $response->assertViewHas('poetry', fn ($poetry) => $poetry->total() === 3);
});

it('shows an empty state when the search page query has no matches', function () {
    $response = $this->get('/search?q=zzznomatch');

    $response->assertOk()->assertSee('No results found');
});

it('finds matches on the search results page even below the autocomplete minimum length', function () {
    Poem::factory()->active()->create(['title' => 'Thin Air']);

    $response = $this->get('/search?q=th');

    $response->assertOk();
    $response->assertViewHas('poetry', fn ($poetry) => $poetry->total() === 1);
    $response->assertDontSee('No results found');
});
