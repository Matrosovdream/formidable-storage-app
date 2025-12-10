<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEmailLog extends Model
{
    
    protected $fillable = [
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

    protected $table = 'frm_emails_log';

    public function site() {
        return $this->belongsTo(Site::class);
    }

}