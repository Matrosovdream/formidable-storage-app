<?php

use App\Models\Site\Site;
use App\Models\Site\SiteToken;
use App\Models\User;

beforeEach(function () {
    $this->user = User::create([
        'name'     => 'Admin',
        'email'    => 'admin@example.com',
        'password' => bcrypt('secret123'),
    ]);
    $this->actingAs($this->user);
});

it('lists sites', function () {
    Site::create(['name' => 'A', 'url' => 'https://a.example.com']);
    Site::create(['name' => 'B', 'url' => 'https://b.example.com']);

    $this->getJson('/api/sites/list')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(2, 'data');
});

it('views a site by id', function () {
    $site = Site::create(['name' => 'View', 'url' => 'https://view.example.com']);

    $this->getJson('/api/sites/view/' . $site->id)
        ->assertOk()
        ->assertJsonPath('data.name', 'View')
        ->assertJsonPath('data.url', 'https://view.example.com');
});

it('stores a site and persists row + token', function () {
    $this->postJson('/api/sites/store', [
        'name' => 'New Site',
        'url'  => 'https://new.example.com',
    ])->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseHas('sites', [
        'name' => 'New Site',
        'url'  => 'https://new.example.com',
    ]);

    $site = Site::where('url', 'https://new.example.com')->first();
    $this->assertDatabaseHas('site_tokens', ['site_id' => $site->id]);
});

it('deletes a site and its token', function () {
    $site = Site::create(['name' => 'Del', 'url' => 'https://del.example.com']);
    SiteToken::create(['site_id' => $site->id, 'token' => 'delete-token']);

    $this->deleteJson('/api/sites/delete/' . $site->id)
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('sites', ['id' => $site->id]);
    $this->assertDatabaseMissing('site_tokens', ['site_id' => $site->id]);
});

it('returns create payload endpoint', function () {
    $this->getJson('/api/sites/create')->assertOk();
});
