<?php

namespace App\Http\Controllers\Rest\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Actions\Rest\FrmFieldsActions;

class FrmFieldsController extends Controller
{

    public function __construct(
        protected FrmFieldsActions $actions,
    ) {

    }

    public function updateAll( Request $request )
    {
        return $this->actions->updateAll( $request->all() );
    }

}