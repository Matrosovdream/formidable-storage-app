<?php

use App\Models\Site\Site;
use App\Models\Frm\FrmEntryHistory;
use App\Models\Frm\FrmEmailLog;
use App\Models\Frm\FrmField;
use App\Models\Frm\FrmEntryUpdateType;
use App\Models\User;

beforeEach(function () {
    $this->user = User::create([
        'name'     => 'Admin',
        'email'    => 'admin@example.com',
        'password' => bcrypt('secret123'),
    ]);
    $this->actingAs($this->user);

    $this->site = Site::create(['name' => 'S1', 'url' => 'https://s1.example.com']);

    FrmEntryUpdateType::create(['id' => 1, 'code' => 'created', 'title' => 'Created']);
    FrmEntryUpdateType::create(['id' => 2, 'code' => 'updated', 'title' => 'Updated']);
    FrmField::create(['id' => 1, 'field_id' => 1, 'site_id' => $this->site->id, 'key' => 'f1', 'type' => 'text', 'label' => 'F1']);
    FrmField::create(['id' => 2, 'field_id' => 2, 'site_id' => $this->site->id, 'key' => 'f2', 'type' => 'text', 'label' => 'F2']);

    // Entry 100: 2 history rows, 1 email
    FrmEntryHistory::create([
        'entry_id' => 100, 'site_id' => $this->site->id, 'field_id' => 1,
        'update_type_id' => 1, 'old_value' => 'a', 'new_value' => 'b',
        'change_date' => '2026-01-01 10:00:00',
    ]);
    FrmEntryHistory::create([
        'entry_id' => 100, 'site_id' => $this->site->id, 'field_id' => 2,
        'update_type_id' => 2, 'old_value' => 'c', 'new_value' => 'd',
        'change_date' => '2026-02-01 10:00:00',
    ]);
    FrmEmailLog::create([
        'entry_id' => 100, 'site_id' => $this->site->id, 'form_id' => 1,
        'subject' => 'Hello', 'message_id' => 'm-100',
        'email_from' => 'a@b.com', 'email_to' => 'c@d.com',
        'status' => 1, 'date_sent' => '2026-03-01 10:00:00',
        'content_plain' => '', 'content_html' => '',
    ]);

    // Entry 200: 1 history row
    FrmEntryHistory::create([
        'entry_id' => 200, 'site_id' => $this->site->id, 'field_id' => 1,
        'update_type_id' => 1, 'old_value' => null, 'new_value' => 'x',
        'change_date' => '2026-04-01 10:00:00',
    ]);
});

it('lists entries for a site with counts, default sort entry_id desc', function () {
    $res = $this->getJson('/api/data/entries/' . $this->site->id)
        ->assertOk()
        ->assertJsonPath('success', true);

    $items = $res->json('data.items');
    expect($items)->toHaveCount(2);
    expect($items[0]['entry_id'])->toBe(200);
    expect($items[1]['entry_id'])->toBe(100);
    expect($items[1]['updates_count'])->toBe(2);
    expect($items[1]['emails_count'])->toBe(1);
    expect($items[0]['updates_count'])->toBe(1);
    expect($items[0]['emails_count'])->toBe(0);
});

it('filters entries by entry_id', function () {
    $res = $this->getJson('/api/data/entries/' . $this->site->id . '?entry_id=100')
        ->assertOk();

    $items = $res->json('data.items');
    expect($items)->toHaveCount(1);
    expect($items[0]['entry_id'])->toBe(100);
});

it('sorts entries by entry_id ascending', function () {
    $res = $this->getJson('/api/data/entries/' . $this->site->id . '?sort_by=entry_id&sort_dir=asc')
        ->assertOk();

    $items = $res->json('data.items');
    expect($items[0]['entry_id'])->toBe(100);
    expect($items[1]['entry_id'])->toBe(200);
});

it('sorts entries by last_update ascending', function () {
    $res = $this->getJson('/api/data/entries/' . $this->site->id . '?sort_by=last_update&sort_dir=asc')
        ->assertOk();

    $items = $res->json('data.items');
    expect($items[0]['entry_id'])->toBe(100); // last_update 2026-03-01 (email)
    expect($items[1]['entry_id'])->toBe(200); // last_update 2026-04-01
});

it('returns emails for an entry', function () {
    $res = $this->getJson('/api/data/entries/' . $this->site->id . '/100/emails')
        ->assertOk()
        ->assertJsonPath('success', true);

    $items = $res->json('data.items');
    expect($items)->toHaveCount(1);
    expect($items[0]['subject'])->toBe('Hello');
});

it('returns entry updates', function () {
    $this->getJson('/api/data/entries/' . $this->site->id . '/100/updates')
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('requires authentication', function () {
    auth()->logout();
    $this->getJson('/api/data/entries/1')->assertStatus(401);
});
