<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;

class FrmEntryUpdateType extends Model
{
    
    protected $fillable = [
        'code',
        'title'
    ];

    protected $table = 'frm_entry_update_types';

}
