<?php

namespace App\Http\Controllers\Rest\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Actions\Rest\Frm\FrmEpShipmentHistoryActions;


class FrmEpShipmentHistoryController extends Controller
{

    public function __construct(
        protected FrmEpShipmentHistoryActions $actions,
    ) {

    }

    public function updateAll( Request $request )
    {
        return $this->actions->update( $request );
    }

    public function list( Request $request )
    {
        return $this->actions->list( $request );
    }

}