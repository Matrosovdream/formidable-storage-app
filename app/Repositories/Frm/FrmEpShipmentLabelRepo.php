<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipmentLabel;


class FrmEpShipmentLabelRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipmentLabel;

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'easypost_id' => $item->easypost_id,
            'easypost_shipment_id' => $item->easypost_shipment_id,
            'entry_id' => $item->entry_id,
            'site_id' => $item->site_id,
            'date_advance' => $item->date_advance,
            'integrated_form' => $item->integrated_form,
            'label_date' => $item->label_date,
            'label_resolution' => $item->label_resolution,
            'label_size' => $item->label_size,
            'label_type' => $item->label_type,
            'label_file_type' => $item->label_file_type,
            'label_url' => $item->label_url,
            'label_pdf_url' => $item->label_pdf_url,
            'label_zpl_url' => $item->label_zpl_url,
            'label_epl2_url' => $item->label_epl2_url,
            'Model' => $item
        ];
        return $res;
    }

}