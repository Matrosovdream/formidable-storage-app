<?php

use App\Models\Frm\FrmEntryHistory;
use App\Models\Frm\FrmEntryUpdateType;
use App\Models\Frm\FrmField;
use Tests\Concerns\UsesRestToken;

uses(UsesRestToken::class);

beforeEach(function () {
    $this->createSiteWithToken();

    FrmEntryUpdateType::create(['id' => 1, 'code' => 'created', 'title' => 'Created']);
    FrmEntryUpdateType::create(['id' => 2, 'code' => 'updated', 'title' => 'Updated']);
});

it('rejects unauthenticated /entry/history/update', function () {
    $this->postJson('/api/rest/v1/entry/history/update', [])->assertStatus(401);
});

it('stores created + updated entries via update endpoint', function () {
    FrmField::create(['id' => 1, 'field_id' => 10, 'site_id' => $this->restSite->id, 'key' => 'k', 'type' => 't', 'label' => 'L']);

    $payload = [
        'entry_id' => 500,
        'user_id'  => 7,
        'created'  => [[
            'field_id'    => 1,
            'old_value'   => null,
            'new_value'   => 'hello',
            'change_date' => '2026-01-01 10:00:00',
        ]],
        'updated'  => [[
            'field_id'    => 1,
            'old_value'   => 'hello',
            'new_value'   => 'world',
            'change_date' => '2026-01-01 11:00:00',
        ]],
    ];

    $this->postJson('/api/rest/v1/entry/history/update', $payload, $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('frm_entry_history', [
        'site_id'        => $this->restSite->id,
        'entry_id'       => 500,
        'user_id'        => 7,
        'update_type_id' => 1,
        'new_value'      => 'hello',
    ]);
    $this->assertDatabaseHas('frm_entry_history', [
        'site_id'        => $this->restSite->id,
        'entry_id'       => 500,
        'update_type_id' => 2,
        'old_value'      => 'hello',
        'new_value'      => 'world',
    ]);

    expect(FrmEntryHistory::where('entry_id', 500)->count())->toBe(2);
});

it('returns entry history via /entry/history/view/{id}', function () {
    FrmField::create(['id' => 1, 'field_id' => 10, 'site_id' => $this->restSite->id, 'key' => 'name', 'type' => 'text', 'label' => 'Name']);
    FrmEntryHistory::create([
        'entry_id' => 77,
        'site_id'  => $this->restSite->id,
        'field_id' => 1,
        'user_id'  => 1,
        'update_type_id' => 1,
        'old_value' => null,
        'new_value' => 'abc',
        'change_date' => '2026-01-01 00:00:00',
    ]);

    $res = $this->postJson('/api/rest/v1/entry/history/view/77', [], $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.site_id', $this->restSite->id);

    expect($res->json('data.items'))->toHaveCount(1);
    expect($res->json('data.items.0.new_value'))->toBe('abc');
});
