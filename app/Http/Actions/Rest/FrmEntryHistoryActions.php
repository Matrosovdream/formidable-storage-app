<?php

namespace App\Http\Actions\Rest;

use App\Services\Frm\FrmEntryHistoryService;

class FrmEntryHistoryActions
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
            return [
                'success' => true,
                'message' => 'Entry history updated successfully.',
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update entry history.',
            ];
        }
    }

}