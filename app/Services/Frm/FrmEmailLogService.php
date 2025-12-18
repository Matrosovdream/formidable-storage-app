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

    public function getList(array $filters = [], array $sorting = [], $paginate = 25)
    { 
        return $this->logRepo->getAll($filters, $paginate, $sorting, true);
    }

}