<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    
    protected $fillable = [
        'name',
        'url'
    ];

    protected $table = 'sites';



}
