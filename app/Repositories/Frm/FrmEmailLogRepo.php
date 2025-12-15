<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEmailLog;
use Illuminate\Support\Facades\DB;

class FrmEmailLogRepo extends AbstractRepo
{

    public $model;

    /**
     * All log fields in the table.
     *
     * @var array
     */
    protected $fields = [
        'entry_id',
        'site_id',
        'form_id',
        'subject',
        'message_id',
        'email_from',
        'email_to',
        'people',
        'headers',
        'error_text',
        'content_plain',
        'content_html',
        'status',
        'date_sent',
        'mailer',
        'attachments',
        'initiator_name',
        'initiator_file',
        'original_log_id',
    ];

    public function __construct()
    {
        $this->model = new FrmEmailLog();
    }

    /**
     * Bulk insert/update logs using upsert
     *
     * @param array $data
     * @param array $site      ['id' => int]
     * @param int   $chunkSize
     *
     * @return int Total affected rows (per Laravel upsert return)
     */
    public function updateLogsMultiple(array $data, array $site, int $chunkSize = 30)
    {

        $site_id = $site['id'] ?? null;
        $logs    = $data ?? [];

        if (! $site_id || empty($logs)) {
            return 0;
        }

        $rows = [];

        foreach ($logs as $logData) {
            // Require at least a message_id to identify the log row
            $messageId = $logData['message_id'] ?? null;
            if (! $messageId) {
                continue;
            }

            $row = [];

            // Fill all known fields from $this->fields
            foreach ($this->fields as $field) {
                if ($field === 'site_id') {
                    // Always override site_id from $site
                    $row['site_id'] = $site_id;
                } else {
                    $row[$field] = $logData[$field] ?? null;
                }
            }

            $rows[] = $row;
        }

        if (empty($rows)) {
            return 0;
        }

        $totalAffected = 0;

        DB::transaction(function () use (&$totalAffected, $rows, $chunkSize) {

            foreach (array_chunk($rows, $chunkSize) as $chunk) {

                // Unique by site_id and message_id
                $uniqueBy = ['site_id', 'message_id'];

                // All fields except the unique key columns will be updated on conflict
                $updateColumns = array_diff($this->fields, $uniqueBy);

                $totalAffected += $this->model
                    ->newQuery()
                    ->upsert($chunk, $uniqueBy, $updateColumns);
            }
        });

        return $totalAffected;
    }

    /**
     * Map model instance to array using the full field list.
     *
     * @param  \App\Models\Frm\FrmEntryHistory|null  $item
     * @return array|null
     */
    public function mapItem($item)
    {
        if (empty($item)) {
            return null;
        }

        $res = [];

        // Always include id
        $res['id'] = $item->id;

        // Map all configured fields
        foreach ($this->fields as $field) {
            $res[$field] = $item->{$field} ?? null;
        }

        // Optionally add raw model
        $res['Model'] = $item;

        return $res;
    }

}
