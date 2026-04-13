<?php

namespace Tests\Concerns;

use App\Models\Site\Site;
use App\Models\Site\SiteToken;

trait UsesRestToken
{
    protected Site $restSite;
    protected string $restToken;

    protected function createSiteWithToken(array $overrides = []): array
    {
        $this->restSite = Site::create(array_merge([
            'name' => 'Test Site',
            'url'  => 'https://test-' . uniqid() . '.example.com',
        ], $overrides));

        $this->restToken = bin2hex(random_bytes(16));

        SiteToken::create([
            'site_id' => $this->restSite->id,
            'token'   => $this->restToken,
        ]);

        return [
            'site'  => $this->restSite,
            'token' => $this->restToken,
        ];
    }

    protected function restHeaders(?string $token = null): array
    {
        return [
            'Authorization' => 'Bearer ' . ($token ?? $this->restToken),
            'Accept'        => 'application/json',
        ];
    }
}
