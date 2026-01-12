<?php

namespace App\Services\Frm;

use App\Repositories\Frm\FrmEpShipmentRepo;

class FrmEpShipmentService {

    protected $shipmentRepo;

    public function __construct() {
        $this->shipmentRepo = new FrmEpShipmentRepo();
    }

    public function updateFieldsAll( array $data, array $site ): bool {
        return $this->shipmentRepo->updateShipmentsMultiple( $data, $site );
    }   

    public function getList(array $filters = [], array $sorting = [], $paginate = 25, $pageNum = 1)
    { 
        return $this->shipmentRepo->getAll($filters, $paginate, $pageNum, $sorting, true);
    }

}