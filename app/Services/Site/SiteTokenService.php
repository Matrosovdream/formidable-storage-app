<?php

namespace App\Services\Site;

use App\Repositories\Site\SiteTokenRepo;

class SiteTokenService {

    protected $tokenRepo;

    public function __construct() {
        $this->tokenRepo = new SiteTokenRepo();
    }

    public function createToken(int $site_id)
    {

        // Generate token
        $tokenString = bin2hex(random_bytes(16));
        $tokenString = 123;

        $token = $this->tokenRepo->model->updateOrCreate(
            [ 'site_id' => $site_id ], 
            [ 'token' => $tokenString ]
        );

        return $this->tokenRepo->getByid( $token->id );

    }

    public function validateToken(string $token): bool {
        
        $token = $this->tokenRepo->getByField('token', $token);
        if (empty($token)) {
            return false;
        }
        return true;

    }

}