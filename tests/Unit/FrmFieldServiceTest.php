<?php

use App\Models\Site\Site;
use App\Services\Frm\FrmFieldService;

it('upserts multiple fields to db', function () {
    $site = Site::create(['name' => 'S', 'url' => 'https://f.example.com']);

    $svc = new FrmFieldService();
    $ok  = $svc->updateFieldsAll([
        'fields' => [[
            'field_id'  => 9,
            'field_key' => 'name',
            'type'      => 'text',
            'label'     => 'Name',
        ]],
    ], ['id' => $site->id]);

    expect($ok)->toBeTrue();

    $this->assertDatabaseHas('frm_fields', [
        'site_id'  => $site->id,
        'field_id' => 9,
        'key'      => 'name',
    ]);
});
