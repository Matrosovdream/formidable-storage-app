<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrmEntryUpdateType extends Model
{
    
    protected $fillable = [
        'code',
        'title'
    ];

    protected $table = 'frm_entry_update_types';

}
