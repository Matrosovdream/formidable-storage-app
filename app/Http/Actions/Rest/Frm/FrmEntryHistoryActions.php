<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmEntryHistoryService;

class FrmEntryHistoryActions extends ActionsRestAbstract
{

    protected $historyService;

    public function __construct()
    {

        parent::__construct();

        $this->historyService = new FrmEntryHistoryService();
    }

    public function update( $request )
    {

        // Get bearer token from request headers
        $data = $this->prepareRequestData( $request );

        $res = $this->historyService->updateEntryHistory( $data['data'], $data['site'] );

        if ( $res ) {
            return $this->returnSuccess( 'Entry history updated successfully.' );
        } else {
            return $this->returnError( 'Failed to update entry history.' );
        }
    }

}