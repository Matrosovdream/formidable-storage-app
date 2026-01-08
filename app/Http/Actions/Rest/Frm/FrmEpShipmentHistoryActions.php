<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmEntryHistoryService;
use App\Jobs\Frm\UpdateFrmEntryHistoryJob;


class FrmEpShipmentHistoryActions extends ActionsRestAbstract
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

        // Dispatch job to queue
        UpdateFrmEntryHistoryJob::dispatch(
            $data['data'],
            $data['site']
        );

        return $this->returnSuccess('Formidable entry history queued for update.');
    }

    public function getEntryHistory( $id, $request )
    {

        // Get bearer token from request headers
        $data = $this->prepareRequestData( $request );

        $res = $this->historyService->getEntryHistory( $id, $data['site'] );

        $data = [
            'site_id' => $data['site']['id'],
            'items' => $res,
        ];

        if ( $res !== false ) {
            return $this->returnData( $data, 'Entry history retrieved successfully.' );
        } else {
            return $this->returnError( 'Failed to retrieve entry history.' );
        }
    }

    public function list( $request )
    {

        // Get bearer token from request headers
        $data = $this->prepareRequestData( $request );

        $res = $this->historyService->getEntryHistory( $data['data'], $data['site'] );

        if ( $res !== false ) {
            return $this->returnData( $res, 'Entry history retrieved successfully.', $res['data'] );
        } else {
            return $this->returnError( 'Failed to retrieve entry history.' );
        }
    }

}