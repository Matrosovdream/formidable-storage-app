<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipment extends Model
{
    protected $table = 'frm_easypost_shipments';

    protected $fillable = [
        'easypost_shipment_id',
        'entry_id',
        'site_id',
        'is_return',
        'status',
        'tracking_code',
        'tracking_url',
        'refund_status',
        'mode'
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

}