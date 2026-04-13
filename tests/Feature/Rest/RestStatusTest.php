<?php

use Tests\Concerns\UsesRestToken;

uses(UsesRestToken::class);

it('returns 401 without a bearer token', function () {
    $this->getJson('/api/rest/v1/status')->assertStatus(401);
});

it('returns 401 with an invalid token', function () {
    $this->getJson('/api/rest/v1/status', [
        'Authorization' => 'Bearer invalid-token',
    ])->assertStatus(401);
});

it('returns status payload with a valid token', function () {
    $this->createSiteWithToken();

    $this->getJson('/api/rest/v1/status', $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('status', 'OK')
        ->assertJsonStructure(['status', 'version', 'timestamp']);
});
