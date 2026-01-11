<?php

namespace App\Models\Frm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Site\Site;


class FrmEpShipmentLabel extends Model
{
    protected $table = 'frm_easypost_shipment_labels';

    protected $fillable = [
        'easypost_id',
        'easypost_shipment_id',
        'entry_id',
        'site_id',
        'date_advance',
        'integrated_form',
        'label_date',
        'label_resolution',
        'label_size',
        'label_type',
        'label_file_type',
        'label_url',
        'label_pdf_url',
        'label_zpl_url',
        'label_epl2_url',
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

}