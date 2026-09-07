<?php

use App\Models\Article;
use App\Models\User;

beforeEach(function () {
    seedRbac();
});

test('the features list can be filtered by creator', function () {
    $viewer = User::factory()->superAdmin()->create();
    $creatorA = User::factory()->create(['first_name' => 'Amara']);
    $creatorB = User::factory()->create(['first_name' => 'Bilal']);

    $keep = Article::factory()->feature()->create(['title' => 'By Amara', 'created_by' => $creatorA->id]);
    Article::factory()->feature()->create(['title' => 'By Bilal', 'created_by' => $creatorB->id]);

    $response = $this->actingAs($viewer)
        ->get(route('admin.features.index', ['created_by' => $creatorA->id]));

    $response->assertOk();
    $response->assertSee($keep->title);
    $response->assertDontSee('By Bilal');
    // The select2 field keeps showing the picked name, not just the raw id.
    $response->assertSee($creatorA->full_name, false);
});

test('the features creator search endpoint matches by name', function () {
    $viewer = User::factory()->superAdmin()->create();
    $match = User::factory()->create(['first_name' => 'Zanele', 'last_name' => 'Okoye']);

    $response = $this->actingAs($viewer)
        ->getJson(route('admin.features.search-creators', ['q' => 'Zanele']));

    $response->assertOk();
    $response->assertJson([
        'results' => [
            ['id' => $match->id, 'text' => "{$match->full_name} ({$match->email})"],
        ],
    ]);
});

test('the features creator search endpoint requires at least 3 characters', function () {
    $viewer = User::factory()->superAdmin()->create();

    $this->actingAs($viewer)
        ->getJson(route('admin.features.search-creators', ['q' => 'Za']))
        ->assertOk()
        ->assertJson(['results' => []]);
});

test('a user without view-features cannot use the creator search endpoint', function () {
    $viewer = userWithPermission('view-journals');

    $this->actingAs($viewer)
        ->getJson(route('admin.features.search-creators', ['q' => 'Zanele']))
        ->assertForbidden();
});

test('the journals list can be filtered by creator', function () {
    $viewer = User::factory()->superAdmin()->create();
    $creator = User::factory()->create(['first_name' => 'Nomvula']);

    $keep = Article::factory()->journal()->create(['title' => 'By Nomvula', 'created_by' => $creator->id]);
    Article::factory()->journal()->create(['title' => 'Someone else']);

    $response = $this->actingAs($viewer)
        ->get(route('admin.journals.index', ['created_by' => $creator->id]));

    $response->assertOk();
    $response->assertSee($keep->title);
    $response->assertDontSee('Someone else');
});
