<?php

namespace App\Services\Data;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataGeneratorService
{
    protected int $chunkSize = 200;

    protected string $loremSource = 'dolore consectetur veniam et aliqua aliqua ullamco sit sed lorem ut magna ut dolor nostrud incididunt laboris et elit ullamco ut enim ipsum veniam labore lorem quis consectetur ad ipsum elit magna do quis ipsum nostrud ullamco laboris lorem veniam elit laboris nisi exercitation dolor eiusmod ad incididunt dolore quis exercitation elit labore ut enim do quis minim sed exercitation eiusmod ad laboris dolor minim quis incididunt minim nisi nisi exercitation ipsum adipiscing sit ullamco do sit elit adipiscing consectetur labore do nisi incididunt laboris enim do nostrud';

    public function generateEmails(int $site_id, int $amount, int $length): array
    {
        $tStart = microtime(true);

        $body = $this->makeBody($length);
        $now  = now();

        $rows = [];
        for ($i = 0; $i < $amount; $i++) {
            $msgId = Str::uuid()->toString();
            $rows[] = [
                'entry_id'        => random_int(1, 9999),
                'site_id'         => $site_id,
                'form_id'         => random_int(1, 100),
                'subject'         => 'Generated email #' . ($i + 1) . ' ' . substr($body, 0, 24),
                'message_id'      => $msgId,
                'email_from'      => 'from+' . random_int(1, 9999) . '@example.com',
                'email_to'        => 'to+' . random_int(1, 9999) . '@example.com',
                'people'          => null,
                'headers'         => null,
                'error_text'      => null,
                'content_plain'   => $body,
                'content_html'    => '<p>' . $body . '</p>',
                'status'          => random_int(0, 2),
                'date_sent'       => $now->copy()->subSeconds(random_int(0, 60 * 60 * 24 * 30)),
                'mailer'          => 'smtp',
                'attachments'     => 0,
                'initiator_name'  => 'generator',
                'initiator_file'  => null,
                'original_log_id' => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        $tGenEnd = microtime(true);

        $uniqueBy  = ['site_id', 'message_id'];
        $updateCols = ['entry_id', 'form_id', 'subject', 'email_from', 'email_to', 'people', 'headers', 'error_text', 'content_plain', 'content_html', 'status', 'date_sent', 'mailer', 'attachments', 'initiator_name', 'initiator_file', 'original_log_id', 'updated_at'];

        DB::transaction(function () use ($rows, $uniqueBy, $updateCols) {
            foreach (array_chunk($rows, $this->chunkSize) as $chunk) {
                DB::table('frm_emails_log')->upsert($chunk, $uniqueBy, $updateCols);
            }
        });

        $tInsEnd = microtime(true);

        return [
            'kind'    => 'emails',
            'site_id' => $site_id,
            'count'   => $amount,
            'length'  => $length,
            'timings' => [
                'generation_ms' => round(($tGenEnd - $tStart) * 1000, 3),
                'insertion_ms'  => round(($tInsEnd - $tGenEnd) * 1000, 3),
                'total_ms'      => round(($tInsEnd - $tStart) * 1000, 3),
            ],
        ];
    }

    public function generateFields(int $site_id, int $amount): array
    {
        $tStart = microtime(true);

        $now = now();
        $base = (int) (microtime(true) * 1000) % 2000000000;

        $rows = [];
        for ($i = 0; $i < $amount; $i++) {
            $rows[] = [
                'site_id'    => $site_id,
                'field_id'   => $base + $i,
                'key'        => 'field_' . ($base + $i),
                'type'       => collect(['text', 'textarea', 'email', 'number', 'select'])->random(),
                'label'      => 'Field ' . ($base + $i),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $tGenEnd = microtime(true);

        $uniqueBy  = ['site_id', 'field_id'];
        $updateCols = ['key', 'type', 'label', 'updated_at'];

        DB::transaction(function () use ($rows, $uniqueBy, $updateCols) {
            foreach (array_chunk($rows, $this->chunkSize) as $chunk) {
                DB::table('frm_fields')->upsert($chunk, $uniqueBy, $updateCols);
            }
        });

        $tInsEnd = microtime(true);

        return [
            'kind'    => 'fields',
            'site_id' => $site_id,
            'count'   => $amount,
            'timings' => [
                'generation_ms' => round(($tGenEnd - $tStart) * 1000, 3),
                'insertion_ms'  => round(($tInsEnd - $tGenEnd) * 1000, 3),
                'total_ms'      => round(($tInsEnd - $tStart) * 1000, 3),
            ],
        ];
    }

    public function generateEntryUpdates(int $site_id, int $amount): array
    {
        $tStart = microtime(true);

        $fieldIds = DB::table('frm_fields')->where('site_id', $site_id)->pluck('id')->all();

        if (empty($fieldIds)) {
            $placeholderId = DB::table('frm_fields')->insertGetId([
                'site_id'    => $site_id,
                'field_id'   => (int) (microtime(true) * 1000) % 2000000000,
                'key'        => 'auto_placeholder',
                'type'       => 'text',
                'label'      => 'Auto placeholder',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $fieldIds = [$placeholderId];
        }

        $updateTypeIds = DB::table('frm_entry_update_types')->pluck('id')->all();

        $now = now();
        $rows = [];
        for ($i = 0; $i < $amount; $i++) {
            $rows[] = [
                'entry_id'       => random_int(1, 9999),
                'site_id'        => $site_id,
                'field_id'       => $fieldIds[array_rand($fieldIds)],
                'user_id'        => random_int(1, 50),
                'update_type_id' => !empty($updateTypeIds) ? $updateTypeIds[array_rand($updateTypeIds)] : null,
                'old_value'      => 'old-' . Str::random(6),
                'new_value'      => 'new-' . Str::random(6),
                'change_date'    => $now->copy()->subSeconds(random_int(0, 60 * 60 * 24 * 30)),
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        $tGenEnd = microtime(true);

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, $this->chunkSize) as $chunk) {
                DB::table('frm_entry_history')->insert($chunk);
            }
        });

        $tInsEnd = microtime(true);

        return [
            'kind'    => 'entry_updates',
            'site_id' => $site_id,
            'count'   => $amount,
            'timings' => [
                'generation_ms' => round(($tGenEnd - $tStart) * 1000, 3),
                'insertion_ms'  => round(($tInsEnd - $tGenEnd) * 1000, 3),
                'total_ms'      => round(($tInsEnd - $tStart) * 1000, 3),
            ],
        ];
    }

    protected function makeBody(int $length): string
    {
        $src = $this->loremSource;
        if ($length <= strlen($src)) {
            return rtrim(substr($src, 0, $length));
        }

        $out = '';
        while (strlen($out) < $length) {
            $out .= ($out === '' ? '' : ' ') . $src;
        }
        return substr($out, 0, $length);
    }
}
