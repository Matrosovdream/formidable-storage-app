<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Actions\Api\Site\SiteActions;

class SiteController extends Controller
{

    public function __construct(
        protected SiteActions $actions
    ) {

    }

    public function list( Request $request )
    {
        return $this->actions->list( $request );
    }

    public function view( Request $request, int $site_id )
    {
        return $this->actions->view( $site_id );
    }

    public function create()
    {
        return $this->actions->create();
    }

    public function store( Request $request )
    {
        return $this->actions->store( $request->all() );
    }

    public function delete( Request $request, int $site_id )
    {
        return $this->actions->delete( $site_id );
    }

}