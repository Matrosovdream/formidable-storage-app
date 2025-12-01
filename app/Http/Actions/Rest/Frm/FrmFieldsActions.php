<?php

namespace App\Http\Actions\Rest\Frm;

use App\Http\Actions\Rest\ActionsRestAbstract;
use App\Services\Frm\FrmFieldService;

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

        // Get bearer token from request headers
        $data = $this->prepareRequestData( $request );
        return $data;

        $res = $this->fieldService->updateFieldsAll( $data );

        if ( $res ) {
            return $this->returnSuccess( 'Formidable fields updated successfully.' );
        } else {
            return $this->returnError( 'Failed to update Formidable fields.' );
        }
    }

}