<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipmentHistory extends Model
{
    protected $table = 'frm_easypost_shipment_history';

    protected $fillable = [
        'easypost_shipment_id',
        'user_id',
        'site_id',
        'change_type',
        'description',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

}