<?php

namespace App\Services\Frm;

use App\Repositories\Frm\FrmFieldRepo;

class FrmFieldService {

    protected $fieldRepo;

    public function __construct() {
        $this->fieldRepo = new FrmFieldRepo();
    }

    public function updateFieldsAll( array $data, array $site ): bool {

        return $this->fieldRepo->updateFieldsMultiple( $data, $site );

    }   

}