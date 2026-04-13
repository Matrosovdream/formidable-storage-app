<?php

use App\Models\Site\Site;
use App\Models\Site\SiteToken;
use App\Services\Site\SiteTokenService;

it('validates existing token', function () {
    $site = Site::create(['name' => 'S', 'url' => 'https://s.example.com']);
    SiteToken::create(['site_id' => $site->id, 'token' => 'abc-token']);

    $svc = new SiteTokenService();

    expect($svc->validateToken('abc-token'))->toBeTrue();
    expect($svc->validateToken('missing'))->toBeFalse();
});

it('creates or updates a token for a site', function () {
    $site = Site::create(['name' => 'S', 'url' => 'https://t.example.com']);

    $svc = new SiteTokenService();
    $svc->createToken($site->id);

    $this->assertDatabaseHas('site_tokens', ['site_id' => $site->id]);
});
