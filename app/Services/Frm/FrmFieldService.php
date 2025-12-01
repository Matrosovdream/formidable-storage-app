<?php

namespace App\Services\Frm;

use App\Repositories\Frm\FrmFieldRepo;

class FrmFieldService {

    protected $fieldRepo;

    public function __construct() {
        $this->fieldRepo = new FrmFieldRepo();
    }

    public function updateFieldsAll( $request ) {
        
        $site_id = $request['site_id'] ?? null;
        $fields = $request['fields'] ?? [];

        foreach ( $fields as $fieldData ) {
            $field_id = $fieldData['field_id'] ?? null;
            $type     = $fieldData['type'] ?? null;
            $key      = $fieldData['field_key'] ?? null;
            $label    = $fieldData['label'] ?? null;

            if ( $site_id && $field_id ) {
                
                $this->fieldRepo->model->updateOrCreate(
                    [
                        'site_id'  => $site_id,
                        'field_id' => $field_id,
                    ],
                    [
                        'key'    => $key,
                        'type'   => $type,
                        'label'  => $label
                    ]
                );
            }
        }

        return true;

    }

}