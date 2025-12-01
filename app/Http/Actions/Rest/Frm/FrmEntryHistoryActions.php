<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmEntryHistoryService;

class FrmEntryHistoryActions extends ActionsRestAbstract
{

    protected $historyService;

    public function __construct()
    {
        $this->historyService = new FrmEntryHistoryService();
    }

    public function update( $request )
    {

        $res = $this->historyService->updateEntryHistory( $request );

        if ( $res ) {
            return $this->returnSuccess( 'Entry history updated successfully.' );
        } else {
            return $this->returnError( 'Failed to update entry history.' );
        }
    }

}