<?php

namespace App\Http\Controllers\Rest\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Actions\Rest\Frm\FrmEntryHistoryActions;

class FrmEntryHistoryController extends Controller
{

    public function __construct(
        protected FrmEntryHistoryActions $actions,
    ) {

    }

    public function update( Request $request )
    {
        return $this->actions->update( $request );
    }

    public function list( Request $request )
    {
        return $this->actions->list( $request );
    }

    public function getEntryHistory( $id, Request $request )
    {
        return $this->actions->getEntryHistory( $id, $request );
    }

}