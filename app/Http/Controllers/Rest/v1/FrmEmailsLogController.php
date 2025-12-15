<?php

namespace App\Http\Controllers\Rest\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Actions\Rest\Frm\FrmEmailsLogActions;

class FrmEmailsLogController extends Controller
{

    public function __construct(
        protected FrmEmailsLogActions $actions,
    ) {

    }

    public function updateAll( Request $request )
    {
        return $this->actions->updateAll( $request );
    }

    public function list( Request $request )
    {
        return $this->actions->list( $request );
    }

}