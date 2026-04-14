<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmFieldService;
use App\Services\QueueStatsService;
use App\Jobs\Frm\UpdateFrmFieldsJob;

class FrmFieldsActions extends ActionsRestAbstract
{

    protected $fieldService;

    public function __construct()
    {

        parent::__construct();

        $this->fieldService = new FrmFieldService();
    }

    public function updateAll( $request )
    {

        $data = $this->prepareRequestData($request);

        QueueStatsService::increment((int) $data['site']['id'], QueueStatsService::TYPE_FIELDS);

        // Dispatch job to queue
        UpdateFrmFieldsJob::dispatch(
            $data['data'],
            $data['site']
        );

        return $this->returnSuccess('Formidable fields queued for update.');
    }

}