<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipmentHistory;


class FrmEpShipmentHistoryRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipmentHistory;

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'easypost_shipment_id' => $item->easypost_shipment_id,
            'user_id' => $item->user_id,
            'site_id' => $item->site_id,
            'change_type' => $item->change_type,
            'description' => $item->description,
            'Model' => $item
        ];
        return $res;
    }

}