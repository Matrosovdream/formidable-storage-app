<?php

namespace App\Http\Actions\Api\Site;

use App\Repositories\Site\SiteRepo;
use App\Http\Actions\Api\ActionsApiAbstract;

class SiteActions extends ActionsApiAbstract {

    protected $siteRepo;

    public function __construct() {
        
        $this->siteRepo = new SiteRepo();

    }

    public function list( $request )
    {
        $res = $this->siteRepo->getAll();
        return $this->returnSuccess( 
            'Sites retrieved successfully', 
            $res['items'] ?? [] 
        );

    }

    public function view( $site_id )
    {
        $site = $this->siteRepo->getById( $site_id );
        return $this->returnSuccess( 'Site retrieved successfully', $site );
    }

    public function create()
    {
        return [];
    }

    public function store( $data )
    {
        $siteRes = $this->siteRepo->addSite( $data );

        if ( !$siteRes ) {
            return $this->returnError( [$siteRes] );
        } else {
            return $this->returnSuccess( 'Site created successfully', $siteRes );
        }
    }

    public function delete( $site_id )
    {
        $resDelete = $this->siteRepo->deleteSite( $site_id );

        if ( !$resDelete ) {
            return $this->returnError( [$resDelete] );
        } else {
            return $this->returnSuccess( 'Site deleted successfully' );
        }
    }

}