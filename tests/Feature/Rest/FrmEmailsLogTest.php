<?php

use App\Models\Frm\FrmEmailLog;
use Tests\Concerns\UsesRestToken;

uses(UsesRestToken::class);

beforeEach(function () {
    $this->createSiteWithToken();
});

it('rejects unauthenticated /emailslog endpoints', function () {
    $this->postJson('/api/rest/v1/emailslog/update-all', [])->assertStatus(401);
    $this->postJson('/api/rest/v1/emailslog/update-all/raw', [])->assertStatus(401);
    $this->postJson('/api/rest/v1/emailslog/list', [])->assertStatus(401);
});

it('upserts email logs via update-all (queued sync)', function () {
    $payload = [
        'items' => [[
            'entry_id'   => 12,
            'form_id'    => 3,
            'subject'    => 'Hello',
            'message_id' => 'msg-1',
            'email_from' => 'a@b.com',
            'email_to'   => 'c@d.com',
            'status'     => 1,
            'date_sent'  => '2026-02-01 12:00:00',
            'mailer'     => 'smtp',
            'attachments' => 0,
        ]],
    ];

    $this->postJson('/api/rest/v1/emailslog/update-all', $payload, $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('frm_emails_log', [
        'site_id'    => $this->restSite->id,
        'message_id' => 'msg-1',
        'subject'    => 'Hello',
        'email_from' => 'a@b.com',
    ]);
});

it('upserts email logs via update-all/raw and returns affected count', function () {
    $this->postJson('/api/rest/v1/emailslog/update-all/raw', [
        'items' => [[
            'message_id' => 'raw-1',
            'subject'    => 'Raw',
            'email_from' => 'x@y.com',
            'email_to'   => 'z@y.com',
            'status'     => 0,
            'attachments' => 0,
        ]],
    ], $this->restHeaders())->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseHas('frm_emails_log', [
        'site_id'    => $this->restSite->id,
        'message_id' => 'raw-1',
        'subject'    => 'Raw',
    ]);
});

it('lists email logs', function () {
    FrmEmailLog::create([
        'entry_id'   => 1,
        'site_id'    => $this->restSite->id,
        'form_id'    => 1,
        'subject'    => 'Sub',
        'message_id' => 'mid-1',
        'status'     => 1,
    ]);

    $this->postJson('/api/rest/v1/emailslog/list', [], $this->restHeaders())
        ->assertOk()
        ->assertJsonPath('success', true);
});
