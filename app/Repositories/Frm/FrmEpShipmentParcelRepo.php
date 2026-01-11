<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipmentParcel;


class FrmEpShipmentParcelRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipmentParcel;

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
            'length' => $item->length,
            'width' => $item->width,
            'height' => $item->height,
            'weight' => $item->weight,
            'Model' => $item
        ];
        return $res;
    }

}