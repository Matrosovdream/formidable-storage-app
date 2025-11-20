<?php

namespace App\Services\Site;

use App\Repositories\Site\SiteRepo;

class SiteService {

    protected $siteRepo;
    protected $tokenService;

    public function __construct() {
        $this->siteRepo = new SiteRepo();
        $this->tokenService = new SiteTokenService();
    }

    public function getSiteById( int $site_id )
    {
        return $this->siteRepo->getByid($site_id);
    }

    public function createSite(array $data)
    {

        if (empty($data['url']) || empty($data['name'])) {
            return ;
        }

        $site = $this->siteRepo->model->updateOrCreate(
            [ 'url' => $data['url'] ], 
            $data);

        // Create token for the site
        $this->tokenService->createToken($site['id']);

        // Return updated version
        return $this->getSiteById($site['id']);
    }

    public function updateSiteToken( int $site_id ) {

        return $this->tokenService->createToken($site_id);

    }

}