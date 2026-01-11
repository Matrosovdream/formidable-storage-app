<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipmentRate extends Model
{
    protected $table = 'frm_easypost_shipment_rates';

    protected $fillable = [
        'easypost_id',
        'easypost_shipment_id',
        'entry_id',
        'site_id',
        'mode',
        'service',
        'carrier',
        'rate',
        'currency',
        'retail_rate',
        'retail_currency',
        'list_rate',
        'list_currency',
        'billing_type',
        'delivery_days',
        'delivery_date',
        'delivery_date_guaranteed',
        'est_delivery_days',
    ]; 

    public function site() {
        return $this->belongsTo(Site::class);
    }

}