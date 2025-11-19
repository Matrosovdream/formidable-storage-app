<?php

namespace App\Http\Controllers\Rest\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Actions\Rest\FrmEntryHistoryActions;

class FrmEntryHistoryController extends Controller
{

    public function __construct(
        protected FrmEntryHistoryActions $actions,
    ) {

    }

    public function update( Request $request )
    {

        /*
        $validated = $request->validate([
            'entry_id' => 'required|integer',
            'site_id' => 'required|integer',
            'field_id' => 'required|integer',
            'update_type_id' => 'required|integer',
            'value' => 'nullable|string',
        ]);
        */
        $validated = $request->all();

        return $this->actions->update( $validated );
    }

}