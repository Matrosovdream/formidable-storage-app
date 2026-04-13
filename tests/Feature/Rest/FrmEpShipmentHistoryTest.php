<?php

use Tests\Concerns\UsesRestToken;

uses(UsesRestToken::class);

beforeEach(function () {
    $this->createSiteWithToken();

    \App\Models\Frm\FrmEntryUpdateType::create(['id' => 1, 'code' => 'created', 'title' => 'Created']);
    \App\Models\Frm\FrmEntryUpdateType::create(['id' => 2, 'code' => 'updated', 'title' => 'Updated']);
});

it('rejects unauthenticated /ep-shipment-history endpoints', function () {
    $this->postJson('/api/rest/v1/ep-shipment-history/update-all', [])->assertStatus(401);
    $this->postJson('/api/rest/v1/ep-shipment-history/list', [])->assertStatus(401);
});

it('accepts a valid token on update-all', function () {
    // The controller currently proxies through FrmEntryHistoryService::updateEntryHistory.
    // Send a minimal valid payload and assert it succeeds.
    \App\Models\Frm\FrmField::create([
        'id' => 1, 'field_id' => 1, 'site_id' => $this->restSite->id,
        'key' => 'k', 'type' => 't', 'label' => 'L',
    ]);

    $this->postJson('/api/rest/v1/ep-shipment-history/update-all', [
        'entry_id' => 1,
        'created'  => [[
            'field_id' => 1, 'old_value' => null, 'new_value' => 'x',
            'change_date' => '2026-01-01 00:00:00',
        ]],
    ], $this->restHeaders())->assertOk()->assertJsonPath('success', true);
});
