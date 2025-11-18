<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;

class FrmField extends Model
{
    
    protected $fillable = [
        'field_id',
        'site_id',
        'key',
        'type',
        'label'
    ];

    protected $table = 'frm_fields';

    public function site() {
        return $this->belongsTo(Site::class);
    }

}
