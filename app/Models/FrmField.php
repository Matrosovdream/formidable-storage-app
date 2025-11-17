<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
