<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipmentAddress extends Model
{
    protected $table = 'frm_easypost_shipment_addresses';

    protected $fillable = [
        'easypost_id',
        'easypost_shipment_id',
        'entry_id',
        'site_id',
        'address_type',
        'name',
        'company',
        'street1',
        'street2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'email'
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

}