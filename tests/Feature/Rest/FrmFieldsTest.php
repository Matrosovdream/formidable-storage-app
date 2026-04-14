<?php

use Tests\Concerns\UsesRestToken;

uses(UsesRestToken::class);

beforeEach(function () {
    $this->createSiteWithToken();
});

it('rejects unauthenticated /fields/update-all', function () {
    $this->postJson('/api/rest/v1/fields/update-all', [])->assertStatus(401);
});

it('upserts fields into DB via update-all', function () {
    $payload = [
        'fields' => [
            [
                'field_id'  => 101,
                'field_key' => 'first_name',
                'type'      => 'text',
                'label'     => 'First Name',
            ],
            [
                'field_id'  => 102,
                'field_key' => 'email',
                'type'      => 'email',
                'label'     => 'Email',
            ],
        ],
    ];

    $this->postJson('/api/rest/v1/fields/update-all', $payload, $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('frm_fields', [
        'site_id'  => $this->restSite->id,
        'field_id' => 101,
        'key'      => 'first_name',
        'type'     => 'text',
        'label'    => 'First Name',
    ]);
    $this->assertDatabaseHas('frm_fields', [
        'site_id'  => $this->restSite->id,
        'field_id' => 102,
        'key'      => 'email',
    ]);
});

it('appends a new row on each call for the same field_id (insert-only)', function () {
    $this->postJson('/api/rest/v1/fields/update-all', [
        'fields' => [[
            'field_id'  => 55,
            'field_key' => 'old_key',
            'type'      => 'text',
            'label'     => 'Old',
        ]],
    ], $this->restHeaders())->assertOk();

    $this->postJson('/api/rest/v1/fields/update-all', [
        'fields' => [[
            'field_id'  => 55,
            'field_key' => 'new_key',
            'type'      => 'textarea',
            'label'     => 'New',
        ]],
    ], $this->restHeaders())->assertOk();

    $this->assertDatabaseHas('frm_fields', [
        'site_id'  => $this->restSite->id,
        'field_id' => 55,
        'key'      => 'old_key',
        'label'    => 'Old',
    ]);
    $this->assertDatabaseHas('frm_fields', [
        'site_id'  => $this->restSite->id,
        'field_id' => 55,
        'key'      => 'new_key',
        'label'    => 'New',
    ]);

    expect(
        \App\Models\Frm\FrmField::where('site_id', $this->restSite->id)
            ->where('field_id', 55)
            ->count()
    )->toBe(2);
});
