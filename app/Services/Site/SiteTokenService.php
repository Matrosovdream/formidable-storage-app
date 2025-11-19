<?php

namespace App\Services\Site;

use App\Repositories\Site\SiteTokenRepo;

class SiteTokenService {

    protected $tokenRepo;

    public function __construct() {
        $this->tokenRepo = new SiteTokenRepo();
    }

    public function validateToken(string $token): bool {
        
        $token = $this->tokenRepo->getByField('token', $token);
        if (empty($token)) {
            return false;
        }
        return true;

    }

}