<?php

namespace App\Http\Actions\Api\Site;

use App\Repositories\Site\SiteRepo;

class SiteActions {

    protected $siteRepo;

    public function __construct() {
        
        $this->siteRepo = new SiteRepo();

    }

    public function list( $request )
    {
        $res = $this->siteRepo->getAll();
        return $res['items'] ?? [];
    }

    public function view( $site_id )
    {
        $site = $this->siteRepo->getById( $site_id );
        return $site;
    }

    public function create()
    {
        return [];
    }

    public function store( $data )
    {
        $site = $this->siteRepo->addSite( $data );
        return $site;
    }

    public function delete( $site_id )
    {
        return $this->siteRepo->deleteSite( $site_id );
    }

}