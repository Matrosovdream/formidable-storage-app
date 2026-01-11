<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipmentParcel extends Model
{
    protected $table = 'frm_easypost_shipment_labels';

    protected $fillable = [
        'easypost_id',
        'easypost_shipment_id',
        'entry_id',
        'site_id',
        'length',
        'width',
        'height',
        'weight',
    ]; 

    public function site() {
        return $this->belongsTo(Site::class);
    }

}