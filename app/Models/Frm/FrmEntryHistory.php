<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;

class FrmEntryHistory extends Model
{
    
    protected $fillable = [
        'entry_id',
        'site_id',
        'field_id',
        'update_type_id',
        'value'
    ];

    protected $table = 'frm_entry_history';

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function field() {
        return $this->belongsTo(FrmField::class);
    }

}
