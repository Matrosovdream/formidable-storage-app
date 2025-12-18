<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmEmailLogService;
use App\Jobs\Frm\UpdateEmailsLogJob;

class FrmEmailsLogActions extends ActionsRestAbstract
{

    protected $logService;

    public function __construct()
    {

        parent::__construct();

        $this->logService = new FrmEmailLogService();
    }

    public function updateAll( $request )
    {

        $data = $this->prepareRequestData($request);

        // Dispatch job to queue
        UpdateEmailsLogJob::dispatch(
            $data['data'],
            $data['site']
        );

        return $this->returnSuccess('Formidable email logs queued for update.');
    }

    public function updateAllRaw( $request )
    {

        $data = $this->prepareRequestData($request);

        $res = $this->logService->updateDataMultiple(
            $data['data'],
            $data['site']
        );

        return $this->returnSuccess('Formidable fields queued for update.', $res);
    }

    public function list( $request )
    {
        $data = $this->prepareRequestData($request);

        $res = $this->logService->getList(
            $data['data']['filters'] ?? [],
            $data['data']['sorting'] ?? [],
            $data['data']['paginate'] ?? 25,
        );

        return $this->returnSuccess(
            'Formidable email logs retrieved successfully',
            $res ?? []
        );
    }

}