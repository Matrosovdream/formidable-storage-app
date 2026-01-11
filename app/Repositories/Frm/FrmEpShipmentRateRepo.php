<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipmentRate;


class FrmEpShipmentRateRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipmentRate;

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
            'mode' => $item->mode,
            'service' => $item->service,
            'carrier' => $item->carrier,
            'rate' => $item->rate,
            'currency' => $item->currency,
            'retail_rate' => $item->retail_rate,
            'retail_currency' => $item->retail_currency,
            'list_rate' => $item->list_rate,
            'list_currency' => $item->list_currency,
            'billing_type' => $item->billing_type,
            'delivery_days' => $item->delivery_days,
            'delivery_date' => $item->delivery_date,
            'delivery_date_guaranteed' => $item->delivery_date_guaranteed,
            'est_delivery_days' => $item->est_delivery_days,
            'Model' => $item
        ];
        return $res;
    }

}