<?php

namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEpShipmentAddress;


class FrmEpShipmentAddressRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEpShipmentAddress;

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
            'address_type' => $item->address_type,
            'name' => $item->name,
            'company' => $item->company,
            'street1' => $item->street1,
            'street2' => $item->street2,
            'city' => $item->city,
            'state' => $item->state,
            'zip' => $item->zip,
            'country' => $item->country,
            'phone' => $item->phone,
            'email' => $item->email,
            'Model' => $item
        ];
        return $res;
    }

}