<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipment;


class FrmEpShipmentRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipment;

    }

    public function updateShipmentsMultiple( array $data, array $site ): bool {
        return true;
    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'easypost_shipment_id' => $item->easypost_shipment_id,
            'entry_id' => $item->entry_id,
            'site_id' => $item->site_id,
            'is_return' => $item->is_return,
            'status' => $item->status,
            'tracking_code' => $item->tracking_code,
            'tracking_url' => $item->tracking_url,
            'refund_status' => $item->refund_status,
            'mode' => $item->mode,
            'Model' => $item
        ];
        return $res;
    }

}