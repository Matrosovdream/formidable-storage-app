<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteToken extends Model
{
    
    protected $fillable = [
        'site_id',
        'token',
        'valid_until'
    ];

    protected $table = 'site_tokens';

    public function site() {
        return $this->belongsTo(Site::class);
    }

}
