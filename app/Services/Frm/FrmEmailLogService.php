<?php

namespace App\Services\Frm; 

use App\Repositories\Frm\FrmEmailLogRepo;

class FrmEmailLogService
{

    protected $logRepo;
    
    public function __construct()
    {
        $this->logRepo = new FrmEmailLogRepo();
    }

    public function updateDataMultiple(array $data, array $site)
    {
        return $this->logRepo->updateLogsMultiple($data, $site);
    }

    public function getList(array $filters = [])
    {
        return $this->logRepo->getAll($filters, 2, [], true);
    }

}