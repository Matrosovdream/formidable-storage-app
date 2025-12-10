<?php

namespace App\Services\Frm;

use App\Models\Frm\FrmEmailLog;

class FrmEmailLogRepo extends FrmEmailLogService {

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEmailLog();

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'entry_id' => $item->entry_id,
            'site_id' => $item->site_id,
            'form_id' => $item->form_id,
            'subject' => $item->subject,
            'message_id' => $item->message_id,
            'email_from' => $item->email_from,
            'email_to' => $item->email_to,
            'people' => $item->people,
            'headers' => $item->headers,
            'error_text' => $item->error_text,
            'content_plain' => $item->content_plain,
            'content_html' => $item->content_html,
            'status' => $item->status,
            'date_sent' => $item->date_sent,
            'mailer' => $item->mailer,
            'attachments' => $item->attachments,
            'initiator_name' => $item->initiator_name,
            'initiator_file' => $item->initiator_file,
            'original_log_id' => $item->original_log_id,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'Model' => $item
        ];
        return $res;
    }

}