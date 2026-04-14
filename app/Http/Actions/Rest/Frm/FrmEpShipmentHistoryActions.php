<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmEntryHistoryService;
use App\Services\QueueStatsService;
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

        QueueStatsService::increment((int) $data['site']['id'], QueueStatsService::TYPE_ENTRY_HISTORY);

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

        $payload = [
            'site_id' => $data['site']['id'],
            'items' => $res['value'],
        ];

        if ( $res['value'] !== false ) {
            return $this->returnData( $payload, 'Entry history retrieved successfully.', [], $res['hit'] );
        } else {
            return $this->returnError( 'Failed to retrieve entry history.' );
        }
    }

    public function list( $request )
    {

        // Get bearer token from request headers
        $data = $this->prepareRequestData( $request );

        $res = $this->historyService->getEntryHistory( $data['data'], $data['site'] );

        if ( $res['value'] !== false ) {
            return $this->returnData( $res['value'], 'Entry history retrieved successfully.', [], $res['hit'] );
        } else {
            return $this->returnError( 'Failed to retrieve entry history.' );
        }
    }

}